<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Profile';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('user/index', $data);
		$this->load->view('templates/footer', $data);
		
	}
	public function edit()

	{
		$data['title'] = 'Edit Profile';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$this->form_validation->set_rules('nama_lengkap', 'Nama_lengkap', 'required|trim');

		if($this->form_validation->run() == false){

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('user/index', $data);
			$this->load->view('templates/footer');
		} else {

			$nama_lengkap = $this->input->post('nama_lengkap');
			$username = $this->input->post('username');

			$upload_image = $_FILES['gambar']['name'];

			if ($upload_image) {
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size']     = '2048';
				$config['upload_path'] = './assets/img/profile/';

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('gambar')) {
					$old_image = $data['user']['gambar'];
					if ($old_image != 'default.jpg'){
						unlink(FCPATH . 'assets/img/profile/' . $old_image);
					}

					$new_image = $this->upload->data('file_name');
					$this->db->set('gambar', $new_image);

				} else {

					echo $this->upload->display_errors();
				}

			}

			$this->db->set('nama_lengkap', $nama_lengkap);
			$this->db->where('username', $username);
			$this->db->update('user');

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Selamat profil anda berhasil diubah
				</div>');
			redirect('user');
		}
	}

	public function ubahpassword()
	{
		$data['title'] = 'Ubah Password';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$this->form_validation->set_rules('password_lama', 'Password lama', 'required|trim');
		$this->form_validation->set_rules('password_baru1', 'Password baru', 'required|trim|min_length[3]|matches[password_baru1]');
		$this->form_validation->set_rules('password_baru2', 'Ulangi password baru', 'required|trim|min_length[3]|matches[password_baru2]');


		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('user/ubahpassword', $data);
			$this->load->view('templates/footer');

		} else {
			$password_lama = $this->input->post('password_lama');
			$password_baru = $this->input->post('password_baru1');
			if (!password_verify($password_lama, $data['user']['password'])) {
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
					Password lama salah!
					</div>');
				redirect('user/ubahpassword');  
			} else {
				if ($password_lama == $password_baru) {
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
						Password baru harus berbeda!
						</div>');
					redirect('user/ubahpassword'); 
				} else {
					$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

					$this->db->set('password', $password_hash);
					$this->db->where('username', $this->session->userdata('username'));
					$this->db->update('user');

					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
						Password telah diubah!
						</div>');
					redirect('user/ubahpassword'); 
				}
			}
		}
	}

	public function detail_pembayaran()
	{
		$data['title'] = 'Detail Pembayaran';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$this->load->model('Siswa_model', 'siswa');
		$this->load->model('Laporan_model');

		$data['siswa'] = $this->siswa->getSiswa();
		$data['jk'] = $this->db->get('siswa_jk')->result_array();
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();
		$data['transaksi'] = $this->db->get('transaksi')->result_array();
		$data['laporan'] = $this->Laporan_model->detailbayar();
		$data['bayar'] = $this->Laporan_model->jumlahbayar();
		$data['laporan_bulanan'] = $this->Laporan_model->laporan_bulanan();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('user/detail_pembayaran', $data);
		$this->load->view('templates/footer', $data);
		
	}

}