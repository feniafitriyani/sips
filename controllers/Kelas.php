<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Kelas';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['kelas'] = $this->db->get('siswa_kelas')->result_array();

		$this->form_validation->set_rules('kelas', 'Kelas', 'required');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('kelas/index', $data);
			$this->load->view('templates/footer');
		} else {
			$this->db->insert('siswa_kelas', ['kelas' => $this->input->post('kelas')]);
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Menu baru berhasil ditambahkan!
				</div>');
			redirect('kelas');
		}
	}

	public function hapus($id)
	{
		$this->db->delete('siswa_kelas', ['id' => $id]);
		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
			Menu berhasil dihapus!
			</div>');
		redirect('kelas/index');
	}

	public function editkelas($id)
	{
		$data['title'] = 'Kelas';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['kelas'] = $this->db->get_where('siswa_kelas', ['id' => $id])->row_array($id);

		$this->form_validation->set_rules('kelas', 'Kelas', 'required');
		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('kelas/index', $data);
			$this->load->view('templates/footer');
		} else {

			$id = $this->input->post('id');
			$kelas = $this->input->post('kelas');

			$this->db->set('kelas', $kelas);
			$this->db->where('id', $id);
			$this->db->update('siswa_kelas');

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Menu berhasil diperbaharui!
				</div>');
			redirect('kelas/index');
		}
	}

}