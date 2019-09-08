<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Transaksi'; 
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$this->load->model('Siswa_model', 'siswa');

		$data['siswa'] = $this->siswa->getSiswa();
		$data['jk'] = $this->db->get('siswa_jk')->result_array();
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();
		$data['transaksi'] = $this->db->get('transaksi')->result_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('transaksi/index', $data);
		$this->load->view('templates/footer');
	}

	public function bayar($nis)
	{
		$data['title'] = 'Transaksi';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$this->load->model('Siswa_model', 'siswa');

		$data['siswa'] = $this->db->get_where('siswa',['nis' => $nis])->row_array($nis);
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();
		$data['transaksi'] = $this->db->get('transaksi')->result_array();

		$this->form_validation->set_rules('nis', 'Nis', 'required');
		$this->form_validation->set_rules('nama_siswa', 'Nama_siswa', 'required');
		$this->form_validation->set_rules('kelas_id', 'Kelas', 'required');
		$this->form_validation->set_rules('jurusan_id', 'Jurusan', 'required');
		if ($this->form_validation->run() == false) {

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('transaksi/index', $data);
			$this->load->view('templates/footer');
		} else {

			$nis = $this->input->post('nis');
			$nama_siswa = $this->input->post('nama_siswa');
			$kelas_id = $this->input->post('kelas_id');
			$jurusan_id = $this->input->post('jurusan_id');
			$biaya = $this->input->post('biaya');

			$data1 = [
				'nis' => $this->input->post('nis'),
				'bayar' => $this->input->post('bayar'),
				'catatan' => $this->input->post('catatan'),
				'tanggal' => $this->input->post('tanggal'),
			];


			$this->db->insert('transaksi', $data1);

			$this->db->set('nama_siswa', $nama_siswa);
			$this->db->set('kelas_id', $kelas_id);
			$this->db->set('jurusan_id', $jurusan_id);
			$this->db->where('nis', $nis);
			$this->db->update('siswa');

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Selamat! Transaksi berhasil.
				</div>');
			redirect('transaksi/print_transaksi');
		}
	}

	public function print_transaksi($nis)
	{
		$this->load->model('Siswa_model', 'siswa');
		$this->load->model('Transaksi_model');
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$data['siswa'] = $this->siswa->getSiswa();
		$data['jk'] = $this->db->get('siswa_jk')->result_array();
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();
		$data['transaksi'] = $this->Transaksi_model->printTransaksi();
		$this->load->library('Pdf');
		$this->load->view('transaksi/print_transaksi', $data);
	}
}