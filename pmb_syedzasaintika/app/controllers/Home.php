<?php
require_once '../core/Controller.php';

class Home extends Controller 
{
    public function index()
    {
        $data['title'] = 'Beranda - PMB Universitas Syedza Saintika';
        $this->view('templates/header', $data);
        $this->view('home/index', $data);
        $this->view('templates/footer', $data);
    }

    public function profil()
    {
        $data['title'] = 'Profil Universitas - PMB Universitas Syedza Saintika';
        $this->view('templates/header', $data);
        $this->view('home/profil', $data);
        $this->view('templates/footer', $data);
    }

    public function panduan()
    {
        $data['title'] = 'Panduan Pendaftaran - PMB Universitas Syedza Saintika';
        $this->view('templates/header', $data);
        $this->view('home/panduan', $data);
        $this->view('templates/footer', $data);
    }

    public function kontak()
    {
        $data['title'] = 'Kontak - PMB Universitas Syedza Saintika';
        $this->view('templates/header', $data);
        $this->view('home/kontak', $data);
        $this->view('templates/footer', $data);
    }
}