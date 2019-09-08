<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Beranda';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

        $this->load->model('Siswa_model', 'siswa');
        $this->load->model('Admin_model');

        $data['siswa'] = $this->siswa->getSiswa();
        $data['admin'] = $this->Admin_model->hitungSiswa();
        $data['hitung_transaksi'] = $this->Admin_model->hitungTransaksi();
        $data['transaksi_harian'] = $this->Admin_model->hitungTransaksiharian();
        $data['jumlah_transaksi_harian'] = $this->Admin_model->jumlahTransaksiharian();
        $data['transaksi_bulanan'] = $this->Admin_model->hitungTransaksibulanan();
        $data['daftar'] = $this->Admin_model->daftartransaksi();
        $data['jk'] = $this->db->get('siswa_jk')->result_array();
        $data['kelas'] = $this->db->get('siswa_kelas')->result_array();
        $data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();
        $data['transaksi'] = $this->db->get('transaksi')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
         $this->load->view('templates/footer');

    }

    public function akses()
    {
      $data['title'] = 'Hak Akses';
      $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

      $data['akses'] = $this->db->get('user_akses')->result_array();

      $this->form_validation->set_rules('akses', 'Akses', 'required');
      if ($this->form_validation->run() == false) {
          $this->load->view('templates/header', $data);
          $this->load->view('templates/navbar', $data);
          $this->load->view('templates/topbar', $data);
          $this->load->view('admin/akses', $data);
          $this->load->view('templates/footer');
      } else {
        $this->db->insert('user_akses', ['akses' => $this->input->post('akses')]);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Akses baru berhasil ditambahkan!
            </div>');
        redirect('admin/akses');
    }

}


public function hakakses($akses_id)
{
    $data['title'] = 'Hak Akses';
    $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

    $data['akses'] = $this->db->get_where('user_akses', ['id' => $akses_id])->row_array();


    $this->db->where('id !=', 1);
    $data['menu'] = $this->db->get('user_menu')->result_array();

    $this->load->view('templates/header', $data);
    $this->load->view('templates/navbar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('admin/hak-akses', $data);
    $this->load->view('templates/footer');
}

public function gantiakses()
{
    $menu_id = $this->input->post('menuId');
    $akses_id = $this->input->post('aksesId');

    $data = [
        'akses_id' => $akses_id,
        'menu_id' => $menu_id
    ];

    $result = $this->db->get_where('user_akses_menu', $data);

    if ($result->num_rows() < 1) {
        $this->db->insert('user_akses_menu', $data);
    } else {
        $this->db->delete('user_akses_menu', $data);
    }

    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Akses telah berubah!
        </div>');
}

public function grafik()
{
    $data['grafik'] = $this->Admin_model->hitungTransaksibulanan();
    echo json_encode($data);
}

}