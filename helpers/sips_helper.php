<?php 

function is_logged_in()
{
	$ci = get_instance();

	if(!$ci->session->userdata('username')){
		redirect('auth');
	} else {
		$akses_id = $ci->session->userdata('akses_id');
		$menu = $ci->uri->segment(1);

		$queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
		$menu_id = $queryMenu['id'];

		$userAccess = $ci->db->get_where('user_akses_menu', [
			'akses_id' => $akses_id,
			'menu_id' => $menu_id
		]);

		if ($userAccess->num_rows() < 1 ) {
			redirect('auth/blocked');
		}
	}
} 
function check_access($akses_id, $menu_id)
{
	$ci = get_instance();

	$ci->db->where('akses_id', $akses_id);
	$ci->db->where('menu_id', $menu_id);
	$result = $ci->db->get('user_akses_menu');

	if ($result->num_rows() > 0) {
		return "checked='checked'";
	}
}