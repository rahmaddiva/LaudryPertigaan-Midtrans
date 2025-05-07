<?php

namespace App\Controllers;
use Config\Midtrans as MidtransConfig;
use Midtrans\Snap;
use App\Controllers\BaseController;
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

    public function bayar($id_transaksi)
    {
        MidtransConfig::init();

        $transaksi = db_connect()->table('transaksi_laundry')
            ->select('transaksi_laundry.*, tb_pelanggan.nama_pelanggan , tb_pelanggan.id_pelanggan, tb_pelanggan.email, tb_pelanggan.no_telepon')
            ->join('tb_pelanggan', 'tb_pelanggan.id_pelanggan = transaksi_laundry.id_pelanggan')
            ->where('transaksi_laundry.id_transaksi', $id_transaksi)
            ->get()
            ->getRowArray();
        if (!$transaksi) {
            return redirect()->to('/transaksi')->with('error', 'Transaksi tidak ditemukan.');
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transaksi['kode_transaksi'],
                'gross_amount' => $transaksi['total_harga'],
            ],
            'customer_details' => [
                'first_name' => $transaksi['nama_pelanggan'], // sesuaikan
                'email' => $transaksi['email'], // sesuaikan
                'phone' => $transaksi['no_telepon'], // sesuaikan
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        // simpan data ke tabel pembayaran
        $this->pembayaranModel->insert([
            'id_transaksi' => $id_transaksi,
            'metode_pembayaran' => 'Midtrans',
            'status_pembayaran' => 'Pending',
            'tanggal_bayar' => date('Y-m-d H:i:s'),
            'snap_token' => $snapToken
        ]);

        return view('transaksi/bayar', [
            'snapToken' => $snapToken,
            'transaksi' => $transaksi
        ]);

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


}
