<?php
class HomeController extends Controller {
    public function index() {
        $data['judul'] = 'Katalog Kopi';
        
        // Memastikan file model ada sebelum dipanggil (jika autoloader belum disetup)
        require_once __DIR__ . '/../models/Product.php';
        
        $data['kopi'] = $this->model('Product')->getAllProducts(); // Panggil model Annas
        
        $this->view('layouts/header', $data);
        $this->view('home/index', $data);
        $this->view('layouts/footer');
    }
}
