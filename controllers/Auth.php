<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
	}

	public function index()
	{
		 if ($this->session->userdata('username')) {
            redirect('user');
        }

		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		
		if ($this->form_validation->run() == false) {

			$data['title'] = 'Login akun';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/login');
			$this->load->view('templates/auth_footer');

		} else {

			$this->_login();
		}
	}

	private function _login()
	{

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$user = $this->db->get_where('user', ['username' => $username])->row_array();

		if ($user) {
			if ($user['is_active'] == 1) {

				if (password_verify($password, $user['password'])) {

					$data = [
						'username' => $user['username'],
						'akses_id' => $user['akses_id']
					];
					$this->session->set_userdata($data);
					if ($user['akses_id'] == 1) {
						redirect('admin');
					} else {
						redirect('user');
					}
				} else {

					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
						Password salah!
						</div>');
					redirect('auth');
				}
			} else {

				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
					Username belum diaktifkan!
					</div>');
				redirect('auth');
			}
		} else {

			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
				Username tidak terdaftar!
				</div>');
			redirect('auth');
		}
	}

	public function registrasi()
	{
		 if ($this->session->userdata('username')) {
            redirect('user');
        }
		$this->form_validation->set_rules('nama_lengkap', 'Nama_lengkap', 'required|trim');
		$this->form_validation->set_rules('username', 'username', 'required|trim|is_unique[user.username]');
		$this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
			'matches' => 'Password tidak sama!',
			'min_length' => 'Password terlalu pendek'
		]);
		$this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');
		if ($this->form_validation->run() == false) {
			$data['title'] = 'Registrasi Akun';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/registrasi');
			$this->load->view('templates/auth_footer');
		} else {
			$data = [
				'nama_lengkap' => htmlspecialchars($this->input->post('nama_lengkap', true)),
				'username' => htmlspecialchars($this->input->post('username', true)),
				'gambar' => 'default.jpg',
				'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
				'akses_id' => 2,
				'is_active' => 1,
				'bergabung_sejak' => time()
			];

			$this->db->insert('user', $data);
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Selamat akun telah dibuat, silahkan login!
				</div>');
			redirect('auth');
		}
	}

	public function logout()
	{
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('akses_id');

		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
			Anda berhasil keluar!
			</div>');
		redirect('auth');
	}

	 public function blocked()
    {
        $this->load->view('auth/blocked');
    }
}
