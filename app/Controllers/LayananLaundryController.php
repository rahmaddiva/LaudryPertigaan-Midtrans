<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LayananLaundryModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class LayananLaundryController extends BaseController
{

    protected $layananlaundryModel;

    public function __construct()
    {
        $this->layananlaundryModel = new LayananLaundryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Layanan Laundry',
            'layanan_laundry' => $this->layananlaundryModel->findAll()
        ];

        return view('layanan_laundry/index', $data);
    }

    public function proses()
    {
        $validation = Services::validation();
        $validation->setRules([
            'nama_layanan' => 'required',
            'harga_per_unit' => 'required',
            'satuan' => 'required',
        ]);
        $isDataValid = $validation->withRequest($this->request)->run();
        if ($isDataValid) {
            $data = [
                'nama_layanan' => $this->request->getPost('nama_layanan'),
                'harga_per_unit' => $this->request->getPost('harga_per_unit'),
                'satuan' => $this->request->getPost('satuan'),

            ];
            $this->layananlaundryModel->insert($data);
            return redirect()->to('/layanan_laundry')->with('success', 'Data berhasil ditambahkan');
        } else {
            return redirect()->to('/layanan_laundry')->withInput()->with('validation', $validation);
        }
    }

    public function update()
    {
        $validation = Services::validation();
        $validation->setRules([
            'nama_layanan' => 'required',
            'harga_per_unit' => 'required|numeric',
            'satuan' => 'required',
        ]);
        $isDataValid = $validation->withRequest($this->request)->run();
        if ($isDataValid) {
            $id_layanan = $this->request->getPost('id_layanan');
            $data = [
                'nama_layanan' => $this->request->getPost('nama_layanan'),
                'harga_per_unit' => $this->request->getPost('harga_per_unit'),
                'satuan' => $this->request->getPost('satuan'),
            ];
            $this->layananlaundryModel->update($id_layanan, $data);
            return redirect()->to('/layanan_laundry')->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('validation', $validation);
        }
    }

    public function delete($id)
    {
        $this->layananlaundryModel->delete($id);
        return redirect()->to('/layanan_laundry')->with('success', 'Data berhasil dihapus');
    }



}
