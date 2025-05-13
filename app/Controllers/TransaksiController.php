<?php

namespace App\Controllers;
use Config\Midtrans as MidtransConfig;
use Midtrans\Snap;
use App\Controllers\BaseController;
use Midtrans\Notification;
use App\Models\TransaksiModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PelangganModel;
use App\Models\PembayaranModel;
use App\Models\DetailTransaksiModel;
use App\Models\LayananLaundryModel;

class TransaksiController extends BaseController
{
    protected $transaksiModel;

    protected $pelangganModel;

    protected $layananLaundryModel;

    protected $DetailTransaksiModel;

    protected $pembayaranModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->pelangganModel = new PelangganModel();
        $this->layananLaundryModel = new LayananLaundryModel();
        $this->DetailTransaksiModel = new DetailTransaksiModel();
        $this->pembayaranModel = new PembayaranModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Transaksi',
            'transaksi' => db_connect()->table('transaksi_laundry')
                ->select('transaksi_laundry.*, tb_pelanggan.nama_pelanggan , tb_pelanggan.id_pelanggan')
                ->join('tb_pelanggan', 'tb_pelanggan.id_pelanggan = transaksi_laundry.id_pelanggan')
                ->get()->getResultArray(),
            'pelanggan' => $this->pelangganModel->findAll(),
            'layanan' => $this->layananLaundryModel->findAll(),
            'detail' => $this->DetailTransaksiModel->findAll(),
        ];
        return view('transaksi/index', $data);
    }

    public function sukses()
    {
        return view('transaksi/sukses');
    }

    public function bayar($id_transaksi)
    {
        MidtransConfig::init();

        $transaksi = db_connect()->table('transaksi_laundry')
            ->select('transaksi_laundry.*, tb_pelanggan.nama_pelanggan, tb_pelanggan.email, tb_pelanggan.no_telepon')
            ->join('tb_pelanggan', 'tb_pelanggan.id_pelanggan = transaksi_laundry.id_pelanggan')
            ->where('transaksi_laundry.id_transaksi', $id_transaksi)
            ->get()
            ->getRowArray();

        if (!$transaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan.'
            ]);
        }
        $order_id = $transaksi['kode_transaksi'];
        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int) $transaksi['total_harga'],
            ],
            'customer_details' => [
                'first_name' => $transaksi['nama_pelanggan'],
                'email' => $transaksi['email'],
                'phone' => $transaksi['no_telepon'],
            ],
            'callbacks' => [
                'finish' => base_url('/transaksi/sukses'), // Redirect ketika selesai
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mendapatkan token pembayaran: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'snapToken' => $snapToken
        ]);
    }
    public function callback()
    {
        // Ambil data dari body (raw JSON)
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        // Log data callback untuk debug
        log_message('info', 'Data callback Midtrans: ' . json_encode($data));

        // Ambil field penting dari callback
        $order_id = $data['order_id'] ?? null;
        $transaction_status = $data['transaction_status'] ?? null;
        $status_code = $data['status_code'] ?? null;
        $gross_amount = $data['gross_amount'] ?? null;
        $signature_key = $data['signature_key'] ?? null;

        // Validasi semua data ada
        if (!$order_id || !$transaction_status || !$status_code || !$gross_amount || !$signature_key) {
            log_message('error', 'Data callback tidak lengkap.');
            return;
        }

        // Validasi signature
        $server_key = getenv('SB-Mid-server-xFcnK43bSot2QXKfv-RkBB3V');
        $expected_signature = hash('sha512', $order_id . $status_code . $gross_amount . $server_key);
        if ($signature_key !== $expected_signature) {
            log_message('error', 'Signature tidak valid!');
            return;
        }

        // Hubungkan ke database & model
        $db = db_connect();
        $pembayaranModel = new PembayaranModel();

        // Cek transaksi berdasarkan order_id
        $transaksi = $db->table('transaksi_laundry')
            ->where('kode_transaksi', $order_id)
            ->get()
            ->getRowArray();

        if (!$transaksi) {
            log_message('error', "Transaksi dengan kode $order_id tidak ditemukan.");
            return;
        }

        // Tentukan status pembayaran
        $status_pembayaran = match (strtolower($transaction_status)) {
            'settlement', 'capture' => 'Berhasil',
            'pending' => 'Pending',
            'cancel', 'expire', 'deny' => 'Gagal',
            default => null
        };

        if (!$status_pembayaran) {
            log_message('error', "Status transaksi tidak dikenali: $transaction_status");
            return;
        }

        // Simpan atau update ke tabel pembayaran
        $existing = $pembayaranModel->where('order_id', $order_id)->first();



        $dataPembayaran = [
            'id_transaksi' => $transaksi['id_transaksi'],
            'metode_pembayaran' => 'Midtrans',
            'status_pembayaran' => $status_pembayaran,
            'tanggal_bayar' => date('Y-m-d H:i:s'),
            'order_id' => $order_id,
            'snap_token' => $data['token'] ?? null, // optional
        ];


        if ($existing) {
            $pembayaranModel->update($existing['id_pembayaran'], $dataPembayaran);
            log_message('info', "Data pembayaran diupdate untuk order_id $order_id");
        } else {
            $pembayaranModel->insert($dataPembayaran);
            log_message('info', "Data pembayaran disimpan baru untuk order_id $order_id");
        }

        // Kembalikan response OK ke Midtrans
        return $this->response->setStatusCode(200)->setBody('OK');
    }

    public function proses()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_pelanggan' => 'required',
            'tanggal_masuk' => 'required|valid_date',
            'layanan.*' => 'required|integer',
            'jumlah.*' => 'required|integer',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $id_pelanggan = $this->request->getPost('id_pelanggan');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');
        $layanan = $this->request->getPost('layanan');
        $jumlah = $this->request->getPost('jumlah');

        // Generate kode transaksi (contoh: TRX20250505123001)
        $kode_transaksi = 'TRX' . date('YmdHis') . rand(10, 99);
        $total_harga = 0;
        $detailData = [];

        foreach ($layanan as $i => $id_layanan) {
            $jumlahItem = (int) $jumlah[$i];
            $layananItem = $this->layananLaundryModel->find($id_layanan);

            if (!$layananItem)
                continue;

            $subtotal = $layananItem['harga_per_unit'] * $jumlahItem;
            $total_harga += $subtotal;

            $detailData[] = [
                'id_layanan' => $id_layanan,
                'jumlah' => $jumlahItem,
                'subtotal' => $subtotal
            ];
        }

        // Simpan transaksi
        $transaksiData = [
            'id_pelanggan' => $id_pelanggan,
            'kode_transaksi' => $kode_transaksi,
            'tanggal_masuk' => $tanggal_masuk,
            'tanggal_selesai' => null,
            'total_harga' => $total_harga,
            'status' => 'Menunggu',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->transaksiModel->insert($transaksiData);
        $idTransaksi = $this->transaksiModel->getInsertID();

        // Simpan detail transaksi
        foreach ($detailData as &$d) {
            $d['id_transaksi'] = $idTransaksi;
        }
        $this->DetailTransaksiModel->insertBatch($detailData);
        return redirect()->to('/transaksi')->with('success', 'Transaksi berhasil ditambahkan');
    }


    public function update($id)
    {

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_pelanggan' => 'required',
            'tanggal_masuk' => 'required|valid_date',
            'layanan.*' => 'required|integer',

        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return redirect()->to('/transaksi')->with('error', 'Transaksi tidak ditemukan.');
        }

        $id_pelanggan = $this->request->getPost('id_pelanggan');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');
        $layanan = $this->request->getPost('layanan');
        $jumlah = $this->request->getPost('jumlah');

        $total_harga = 0;


        // Hapus detail lama
        $this->DetailTransaksiModel->where('id_transaksi', $id)->delete();

        // Simpan detail baru dan hitung total
        foreach ($layanan as $i => $id_layanan) {
            $layananData = $this->layananLaundryModel->find($id_layanan);
            if ($layananData) {
                $jumlah_item = intval($jumlah[$i]);
                $harga_satuan = $layananData['harga_per_unit'];
                $subtotal = $harga_satuan * $jumlah_item;
                $total_harga += $subtotal;

                $this->DetailTransaksiModel->insert([
                    'id_transaksi' => $id,
                    'id_layanan' => $id_layanan,
                    'jumlah' => $jumlah_item,
                    'subtotal' => $subtotal
                ]);
            }
        }

        // Update transaksi
        $this->transaksiModel->update($id, [
            'id_pelanggan' => $id_pelanggan,
            'tanggal_masuk' => $tanggal_masuk,
            'total_harga' => $total_harga,
        ]);

        return redirect()->to('/transaksi')->with('success', 'Transaksi berhasil diupdate.');
    }

    public function delete($id)
    {
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return redirect()->to('/transaksi')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Hapus detail transaksi
        $this->DetailTransaksiModel->where('id_transaksi', $id)->delete();
        $this->transaksiModel->delete($id);
        return redirect()->to('/transaksi')->with('success', 'Transaksi berhasil dihapus.');
    }
}
