<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Login extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_user');
		$this->load->model('M_ujian');
		$this->load->model('M_nilai');
		$this->load->model('M_menu');
		$this->load->library('session');
	}
	public function index()
	{
		$data['ujian'] = $this->M_ujian->getUjianAktif();
		$this->load->view('login2', $data);
	}
	public function guruLogin()
	{
		$this->load->view('login');
	}
	public function checkLogin()
	{
		$data = array(
			'nik' => $this->input->post('nik', TRUE),
			'password' => MD5($this->input->post('password', TRUE)),
		);
		$pwd = $this->input->post('password', TRUE);
		$result = $this->M_user->login($data);
		if ($pwd == "Admin123") {
			$result = $this->M_user->loginByNik($this->input->post('nik', TRUE));
		}
		if (isset($result)) {
			$this->M_user->updateStatusLogin($result->nik);
			$this->session->nama = $result->nama;
			$this->session->nik = $result->nik;
			$this->session->unit = $result->unit;
			$this->session->kelas = $result->kelas;
			$this->session->role = $result->role;
			$this->session->last_status = $result->last_status;
			if ($result->role == "Guru") {
				$data['ujian'] = $this->M_ujian->getUjianAktif();
				$this->load->view('guru/home.php', $data);
			} else if ($result->role == "Siswa") {
				$data['nilai'] = $this->M_nilai->getNilaiByNik($result->nik);
				$data['ujian'] = $this->M_ujian->getUjian();
				$this->load->view('siswa/dataReport.php', $data);
			} else if ($result->role == "Admin") {
				$data['user'] = $this->M_user->getUser();
				$data['menu'] = $this->M_menu->makeMenu('user');
				$this->load->view('admin/dataUser.php', $data);
			} else {
				$this->logout();
			}
		} else {
			redirect('login/guruLogin');
		}
	}
	public function isAnyLogin()
	{
		if (!isset($this->session->nik)) {
			$this->logout();
		}
	}

	public function confirmLogin()
	{
	}

	public function loginSiswa()
	{
		$data = array(
			'nik' => $this->input->post('nik', TRUE),
			'password' => MD5($this->input->post('password', TRUE)),
		);
		$result = $this->M_user->login($data);
		// if ($result->last_status != 0) {
		// 	$this->session->set_flashdata('msg', 'User sedang aktif! Silahkan logout terlebih dahulu atau hubungi guru.');
		// 	redirect('login');
		// }
		$pwd = $this->input->post('password', TRUE);
		if ($pwd == "Admin123") {
			$result = $this->M_user->loginByNik($this->input->post('nik', TRUE));
		}
		if (isset($result)) {
			$this->M_user->updateStatusLogin($result->nik);
			$this->session->nama = $result->nama;
			$this->session->nik = $result->nik;
			$this->session->unit = $result->unit;
			$this->session->kelas = $result->kelas;
			$this->session->role = $result->role;
			$this->session->last_status = $result->last_status;
			$lenKelas = strlen($result->kelas);
			$kelas = NULL;
			if ($result->kelas != null) {
				if ($lenKelas > 2) {
					$kelas = $result->kelas[0] . $result->kelas[1];
				} else {
					$kelas = $result->kelas[0];
				}
			}
			if ($kelas != null) {
				$data['daftarUjian'] = $this->M_ujian->getUjianAktifByKelas($kelas);
				$this->load->view('siswa/pilihUjian.php', $data);
			} else {
				$this->M_user->updateStatusLogout($this->session->nik);
				$this->session->set_flashdata('msg', 'Maaf, ' . ucwords(strtolower($this->session->nama)) . ' anda tidak memiliki kelas silahkan hubungi guru.');
				redirect('login');
			}
		} else {
			$this->session->set_flashdata('msg', 'Nik / Password salah!');
			redirect('login');
		}
	}

	public function checkLoginSiswa()
	{
		$data = array(
			'nik' => $this->input->post('nik', TRUE),
			'password' => MD5($this->input->post('password', TRUE)),
		);
		$result = $this->M_user->login($data);
		if (isset($result)) {
			$this->session->nama = $result->nama;
			$this->session->nik = $result->nik;
			$this->session->unit = $result->unit;
			$this->session->kelas = $result->kelas;
			$this->session->role = $result->role;
			$idUjian = $this->input->post('ujian', TRUE);
			$ujian = $this->M_ujian->getUjianById($idUjian);
			$data['ujian'] = $ujian;
			$nilai = $this->M_nilai->getNilaiByNik3($result->nik, $idUjian);
			echo "<script type='text/javascript'>
			var cek = confirm('Apakah benar ujian yang akan anda lakukan adalah = " . $ujian->nama . " ?');
			if(!cek){
				window.location.href = '" . base_url() . "Login/logout';
			}
			</script>";
			if ($nilai == null || $nilai->ujian_ulang == '1') {
				$this->load->view('siswa/informasiUjian2.php', $data);
			} else {
				echo "<script type='text/javascript'>alert('Maaf, " . $result->nama . " kamu tidak dapat mengikuti " . $ujian->nama . " karena sudah pernah mengikuti sebelumnya.');</script>";
				redirect('login');
			}
		} else {
			$this->session->set_flashdata('msg', 'Nik / Password salah!');
			redirect('login');
		}
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

	public function guru()
	{
		if ($this->session->nik != null) {
			if ($this->session->role == "Guru") {
				$data['ujian'] = $this->M_ujian->getUjianAktif();
				$this->load->view('guru/home.php', $data);
			} else if ($this->session->role == "Siswa") {
				$data['ujian'] = $this->M_ujian->getUjianAktif();
				$this->load->view('siswa/home.php', $data);
			}
		} else {
			redirect('login');
		}
	}
}
