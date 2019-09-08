<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Laporan Transaksi';
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
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('laporan/index', $data);
		$this->load->view('templates/footer', $data);
		
	}

	public function cetak_laporan()
	{
		$data['title'] = 'Cetak Laporan';
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
		$this->load->view('laporan/cetak_laporan', $data);
		$this->load->view('templates/footer', $data);
		
	}

	public function cetak_tanggal()
	{
		$this->load->model('Laporan_model');
		$this->load->model('Admin_model');
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['cetak_tanggal'] = $this->Laporan_model->cetak_tanggal();
		$data['total_tanggal']  = $this->Admin_model->hitungTransaksiharian();
		$data['jk'] = $this->db->get('siswa_jk')->result_array();
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();


		$this->load->library('Pdf');
		$this->load->view('laporan/cetak_tanggal', $data);
	}

	public function cetak_bulanan()
	{
		$this->load->model('Laporan_model');
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['cetak_bulan'] = $this->Laporan_model->cetak_bulanan();
		$data['total_bulan'] = $this->Laporan_model->hitungTransaksibulanan();
		$data['jk'] = $this->db->get('siswa_jk')->result_array();
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();


		$this->load->library('Pdf');
		$this->load->view('laporan/cetak_bulan', $data);
	}

	public function cetak_tahunan()
	{
		$this->load->model('Laporan_model');
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['cetak_tahun'] = $this->Laporan_model->cetak_tahunan();
		$data['total_tahun'] = $this->Laporan_model->hitungTransaksitahunan();
		$data['jk'] = $this->db->get('siswa_jk')->result_array();
		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();
		$data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();


		$this->load->library('Pdf');
		$this->load->view('laporan/cetak_tahun', $data);
	}

}