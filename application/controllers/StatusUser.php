<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StatusUser extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_user');
		$this->load->model('M_kelas');
		$this->load->model('M_soalpg');
		$this->load->model('M_soalisian');
		$this->load->model('M_ujian');
		$this->load->model('M_ujian_has_soal');
		$this->load->model('M_ujian_gabungan_has_soal');
		$this->load->model('M_jawaban_siswa');
		$this->load->model('M_jawaban_siswa_isian');
		$this->load->model('M_nilai');
		$this->load->model('M_menu');
		$this->load->model('M_gambar');
		$this->load->library('session');
		$this->load->library('pdf');
	}

	public function logout()
	{
		$this->M_user->updateStatusLogout($this->session->nik);
		$user_data = $this->session->all_userdata();
		foreach ($user_data as $key => $value) {
			if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
				$this->session->unset_userdata($key);
			}
		}
		$this->session->sess_destroy();
		redirect('login');
	}

	public function isAnyLogin()
	{
		if (!isset($this->session->nik)) {
			$this->logout();
		}
	}

	public function index()
	{
		$this->isAnyLogin();
		$data['user'] = $this->M_user->getAllUserOnline();
		$data['menu'] = $this->M_menu->makeMenu('aktif');
		$data['ujianAktif'] = $this->M_ujian->getUjianAktif();
		$this->load->view('statusAktif/index', $data);
	}

	public function reset($nikUser)
	{
		$this->isAnyLogin();
		$this->M_user->updateStatusLogout($nikUser);
		redirect('StatusUser/index');
	}

	public function resetAll()
	{
		$this->isAnyLogin();
		$user = $this->M_user->getAllUserOnline();
		foreach ($user as $u) {
			if ($u->role == "Siswa") {
				$this->M_user->updateStatusLogout($u->nik);
			}
		}
		redirect('StatusUser/index');
	}
}
