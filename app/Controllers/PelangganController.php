<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PelangganModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class PelangganController extends BaseController
{

    protected $pelangganModel;
    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
    }


    public function index()
    {
        $data = [
            'title' => 'Pelanggan',
            'pelanggan' => $this->pelangganModel->findAll()
        ];

        return view('pelanggan/index', $data);
    }

    public function proses()
    {
        $validation = Services::validation();
        $validation->setRules([
            'nama_pelanggan' => 'required',
            'no_telepon' => 'required',
            'alamat' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ]);
        $isDataValid = $validation->withRequest($this->request)->run();
        if ($isDataValid) {
            $data = [
                'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
                'no_telepon' => $this->request->getPost('no_telepon'),
                'alamat' => $this->request->getPost('alamat'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ];
            $this->pelangganModel->insert($data);
            return redirect()->to('/pelanggan')->with('success', 'Data berhasil ditambahkan');
        } else {
            return redirect()->to('/pelanggan')->withInput()->with('validation', $validation);
        }

    }

    public function update()
    {
        $validation = Services::validation();
        $validation->setRules([
            'nama_pelanggan' => 'required',
            'no_telepon' => 'required',
            'alamat' => 'required',
            'email' => 'required|valid_email',
        ]);

        $isDataValid = $validation->withRequest($this->request)->run();

        if ($isDataValid) {
            $id_pelanggan = $this->request->getPost('id_pelanggan');
            $password = $this->request->getPost('password');
            // Data dasar
            $data = [
                'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
                'no_telepon' => $this->request->getPost('no_telepon'),
                'alamat' => $this->request->getPost('alamat'),
                'email' => $this->request->getPost('email'),
            ];
            // Tambahkan password hanya jika diisi
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $this->pelangganModel->update($id_pelanggan, $data);
            return redirect()->to('/pelanggan')->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->to('/pelanggan')->withInput()->with('validation', $validation);
        }
    }


    public function delete($id)
    {
        $this->pelangganModel->delete($id);
        return redirect()->to('/pelanggan')->with('success', 'Data berhasil dihapus');
    }
}
