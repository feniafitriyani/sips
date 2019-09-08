<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Menu Management';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['menu'] = $this->db->get('user_menu')->result_array();

		$this->form_validation->set_rules('menu', 'Menu', 'required');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('menu/index', $data);
			$this->load->view('templates/footer');
		} else {
			$this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Menu baru berhasil ditambahkan!
				</div>');
			redirect('menu');
		}
		
	}

	public function submenu()
	{
		$data['title'] = 'Submenu Management';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$this->load->model('Menu_model', 'menu');

		$data['subMenu'] = $this->menu->getSubMenu();
		$data['menu'] = $this->db->get('user_menu')->result_array();

		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('menu_id', 'Menu', 'required');
		$this->form_validation->set_rules('url', 'Url', 'required');
		$this->form_validation->set_rules('icon', 'Icon', 'required');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('menu/submenu', $data);
			$this->load->view('templates/footer');
		} else {
			$data = [
				'title' => $this->input->post('title'),
				'menu_id' => $this->input->post('menu_id'),
				'url' => $this->input->post('url'),
				'icon' => $this->input->post('icon'),
				'is_active' => $this->input->post('is_active')
			];
			$this->db->insert('user_sub_menu', $data);
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				New sub menu added!
				</div>');
			redirect('menu/submenu');
		}
	}

	public function deleteSubMenu($id)
	{
		$this->db->delete('user_sub_menu', ['id' => $id]);
		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
			Sub Menu berhasil dihapus!
			</div>');
		redirect('menu/submenu');
	}

	public function deleteMenu($id)
	{
		$this->db->delete('user_menu', ['id' => $id]);
		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
			Menu berhasil dihapus!
			</div>');
		redirect('menu/index');
	}

	public function editmenu($id)
	{
		$data['title'] = 'Menu Management';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

		$data['menu'] = $this->db->get_where('user_menu', ['id' => $id])->row_array($id);

		$this->form_validation->set_rules('menu', 'Menu', 'required');
		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('menu/index', $data);
			$this->load->view('templates/footer');
		} else {

			$id = $this->input->post('id');
			$menu = $this->input->post('menu');

			$this->db->set('menu', $menu);
			$this->db->where('id', $id);
			$this->db->update('user_menu');

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Menu berhasil diperbaharui!
				</div>');
			redirect('menu/index');
		}
	}

	public function editsubmenu($id)
	{
		$data['title'] = 'Submenu Management';
		$data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
		$this->load->model('Menu_model', 'menu');

		$data['subMenu'] =  $this->db->get_where('user_sub_menu', ['id' => $id])->row_array($id);
		$data['menu'] = $this->db->get('user_menu')->result_array();

		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('menu_id', 'Menu', 'required');
		$this->form_validation->set_rules('url', 'Url', 'required');
		$this->form_validation->set_rules('icon', 'Icon', 'required');

		if ($this->form_validation->run() == false) {

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('menu/submenu', $data);
			$this->load->view('templates/footer');

		} else {

			$id =$this->input->post('id');
			$title = $this->input->post('title');
			$menu_id = $this->input->post('menu_id');
			$url = $this->input->post('url');
			$icon = $this->input->post('icon'); 
			$is_active = $this->input->post('is_active');

			$this->db->set('title', $title);
			$this->db->set('menu_id', $menu_id);
			$this->db->set('url', $url);
			$this->db->set('icon', $icon);
			$this->db->set('is_active', $is_active);
			$this->db->where('id', $id);
			$this->db->update('user_sub_menu');

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Submenu berhasil diperbaharui!
				</div>');
			redirect('menu/submenu');
		}
	}

}