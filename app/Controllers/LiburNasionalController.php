<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LiburNasional;

class LiburNasionalController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $liburNasionalModel = new LiburNasional();
        $data['libur_nasional'] = $liburNasionalModel->orderBy('tahun', 'DESC')->orderBy('tanggal', 'ASC')->findAll();

        return view('admin/libur_nasional/index', $data);
    }
    
    public function tambah()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        return view('admin/libur_nasional/tambah');
    }
    
    public function simpan()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $liburNasionalModel = new LiburNasional();
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'jenis_libur' => $this->request->getPost('jenis_libur'),
            'tahun' => date('Y', strtotime($this->request->getPost('tanggal'))),
        ];

        // Validate using model validation
        if (!$liburNasionalModel->save($data)) {
            $errors = $liburNasionalModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal menyimpan data libur nasional: ' . $errorMessage);
        }

        return redirect()->to('/admin/libur-nasional')->with('success', 'Data libur nasional berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $liburNasionalModel = new LiburNasional();
        $data['libur_nasional'] = $liburNasionalModel->find($id);

        if (!$data['libur_nasional']) {
            return redirect()->to('/admin/libur-nasional')->with('error', 'Data libur nasional tidak ditemukan');
        }

        return view('admin/libur_nasional/edit', $data);
    }
    
    public function update($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $liburNasionalModel = new LiburNasional();
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'jenis_libur' => $this->request->getPost('jenis_libur'),
            'tahun' => date('Y', strtotime($this->request->getPost('tanggal'))),
        ];

        // Validate using model validation
        if (!$liburNasionalModel->update($id, $data)) {
            $errors = $liburNasionalModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal mengupdate data libur nasional: ' . $errorMessage);
        }

        return redirect()->to('/admin/libur-nasional')->with('success', 'Data libur nasional berhasil diupdate');
    }
    
    public function hapus($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $liburNasionalModel = new LiburNasional();
        
        // Check if libur nasional exists
        $libur = $liburNasionalModel->find($id);
        if (!$libur) {
            return redirect()->to('/admin/libur-nasional')->with('error', 'Data libur nasional tidak ditemukan');
        }

        try {
            if ($liburNasionalModel->delete($id)) {
                return redirect()->to('/admin/libur-nasional')->with('success', 'Data libur nasional berhasil dihapus');
            } else {
                return redirect()->to('/admin/libur-nasional')->with('error', 'Gagal menghapus data libur nasional');
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error deleting libur nasional: ' . $e->getMessage());
            return redirect()->to('/admin/libur-nasional')->with('error', 'Terjadi kesalahan saat menghapus data libur nasional');
        }
    }
}