<?php
class Home extends Controller 
{
    public function index()
    {
        $this->view('home/index');
    }

    public function profil()
    {
        $this->view('home/profil');
    }

    public function panduan()
    {
        $this->view('home/panduan');
    }

    public function kontak()
    {
        $this->view('home/kontak');
    }
}