<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Siswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }
    
    public function index()
    {
        $data['title'] = 'Daftar Siswa';
        $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
        $this->load->model('Siswa_model', 'siswa');
        $this->load->model('Laporan_model');

        $data['siswa'] = $this->siswa->getSiswa();
        $data['jk'] = $this->db->get('siswa_jk')->result_array();
        $data['kelas'] = $this->db->get('siswa_kelas')->result_array();
        $data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();
        $data['transaksi'] = $this->db->get('transaksi')->result_array();
        $data['bayar'] = $this->Laporan_model->jumlahbayar();

        $this->form_validation->set_rules('nis', 'Nis', 'required|trim|is_unique[siswa.nis]');
        $this->form_validation->set_rules('nama_siswa', 'Nama siswa', 'required');
        $this->form_validation->set_rules('kelas_id', 'Kelas', 'required');
        $this->form_validation->set_rules('jurusan_id', 'Jurusan', 'required');
        if ($this->form_validation->run() == false) {

            $this->load->view('templates/header', $data);
            $this->load->view('templates/navbar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('siswa/index', $data);
            $this->load->view('templates/footer');
        } else {
            $upload_image = $_FILES['photo']['name'];
            if ($upload_image) {
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']     = '2048';
                $config['upload_path'] = './assets/img/siswa/';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('photo')) {
                    $siswa_photo = $data['siswa']['photo'];
                } else {
                    echo $this->upload->display_errors();
                }
            }

            $data = [
                'nis' => $this->input->post('nis'),
                'nama_siswa' => $this->input->post('nama_siswa'),
                'jk_id' => $this->input->post('jk_id'),
                'kelas_id' => $this->input->post('kelas_id'),
                'jurusan_id' => $this->input->post('jurusan_id'),
                'photo' => $this->upload->data('file_name'),
                'alamat' => $this->input->post('alamat'),
                'biaya' => $this->input->post('biaya'),
                'sisa_bayar' => $this->input->post('sisa_bayar'),
            ];
            
            $data1 = [
                'nis' => $this->input->post('nis'),
                'bayar' => $this->input->post('bayar'),
                'catatan' => $this->input->post('catatan'),
                'tanggal' => $this->input->post('tanggal'),
            ];


            $this->db->insert('transaksi', $data1);

            $this->db->insert('siswa', $data);
            
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                Siswa baru ditambahkan!
                </div>');
            redirect('transaksi/print_transaksi');
        }
    }

    public function edit($nis)
    {
       $data['title'] = 'Daftar Siswa';
       $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
       $this->load->model('Siswa_model', 'siswa');

       $data['siswa'] = $this->db->get_where('siswa',['nis' => $nis])->row_array($nis);
       $data['kelas'] = $this->db->get('siswa_kelas')->result_array();
       $data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();

       $this->form_validation->set_rules('nis', 'Nis', 'required');
       $this->form_validation->set_rules('nama_siswa', 'Nama_siswa', 'required');
       $this->form_validation->set_rules('kelas_id', 'Kelas', 'required');
       $this->form_validation->set_rules('jurusan_id', 'Jurusan', 'required');
       if ($this->form_validation->run() == false) {

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('siswa/index', $data);
        $this->load->view('templates/footer');
    } else {

        $nis = $this->input->post('nis');
        $nama_siswa = $this->input->post('nama_siswa');
        $jk_id = $this->input->post('jk_id');
        $kelas_id = $this->input->post('kelas_id');
        $jurusan_id = $this->input->post('jurusan_id');
        $alamat = $this->input->post('alamat');

        $upload_image = $_FILES['photo']['name'];

        if ($upload_image) {
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']     = '2048';
            $config['upload_path'] = './assets/img/siswa/';

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('photo')) {
                $photo_lama = $data['siswa']['photo'];
                if ($photo_lama != 'default.jpg'){
                    unlink(FCPATH . 'assets/img/siswa/' . $photo_lama);
                }

                $photo_baru = $this->upload->data('file_name');
                $this->db->set('photo', $photo_baru);

            } else {

                echo $this->upload->display_errors();
            }

        }

        $this->db->set('nama_siswa', $nama_siswa);
        $this->db->set('jk_id', $jk_id);
        $this->db->set('kelas_id', $kelas_id);
        $this->db->set('jurusan_id', $jurusan_id);
        $this->db->set('alamat', $alamat);
        $this->db->where('nis', $nis);
        $this->db->update('siswa');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Selamat! Siswa berhasil di perbaharui.
            </div>');
        redirect('siswa');
    }
}

public function hapus($nis)
{
    $_nis = $this->db->get_where('siswa',['nis' => $nis])->row();
    $query = $this->db->delete('siswa',['nis'=>$nis]);
    if($query){
        unlink("assets/img/siswa/".$_nis->photo);
    }
    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Siswa berhasil dihapus!
        </div>');
    redirect('siswa');
}


public function printSiswa()
{
    $this->load->model('Siswa_model', 'siswa');
    $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
    
    $data['siswa'] = $this->siswa->printSiswa();
    $data['jk'] = $this->db->get('siswa_jk')->result_array();
    $data['kelas'] = $this->db->get('siswa_kelas')->result_array();
    $data['jurusan'] = $this->db->get('siswa_jurusan')->result_array();


    $this->load->library('Pdf');
    $this->load->view('siswa/print_siswa', $data);
}

}