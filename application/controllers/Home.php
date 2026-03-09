<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    public function index() {
        $this->data['title'] = 'Halaman Depan';
        $data['pesan'] = 'Halo Dunia';
        
        // Cukup panggil render, header/footer otomatis muncul
        $this->render('home_view', $data); 
    }
}
