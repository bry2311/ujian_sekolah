<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment as alignment;
//last commit 12 12
class Guru extends CI_Controller
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
		$this->load->model('M_kelas');
		$this->load->model('M_soalpg');
		$this->load->model('M_soalisian');
		$this->load->model('M_ujian');
		$this->load->model('M_ujian_has_soal');
		$this->load->model('M_ujian_gabungan_has_soal');
		$this->load->model('M_jawaban_siswa');
		$this->load->model('M_jawaban_siswa_isian');
		$this->load->model('M_nilai');
		$this->load->model('M_gambar');
		$this->load->library('session');
		$this->load->library('pdf');
	}
	public function standarDeviasi($arr)
	{
		$num_of_elements = count($arr);

		$variance = 0.0;

		$average = array_sum($arr) / $num_of_elements;

		foreach ($arr as $i) {
			$variance += pow(($i->hasil - $average), 2);
		}

		return (float)sqrt($variance / $num_of_elements);
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
	//Start Kelas
	public function dataKelas()
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelas();
		$this->load->view('guru/dataKelas', $data);
	}
	public function tambahKelas()
	{
		$this->isAnyLogin();
		$this->load->view('guru/tambahKelas');
	}
	public function tambahKelasDb()
	{
		$this->isAnyLogin();
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'unit' => $this->input->post('unit', TRUE),
		);
		$this->M_kelas->add($data);
		redirect('guru/dataKelas', 'refresh');
	}
	public function hapusKelas($kelas_id)
	{
		$this->isAnyLogin();
		$row = $this->M_kelas->getKelasByID($kelas_id);
		if ($row) {
			$this->M_kelas->delete($kelas_id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataKelas');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataKelas');
		}
	}
	public function ubahKelas($id)
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelasByID($id);
		$this->load->view('guru/ubahKelas', $data);
	}
	public function simpanUbahKelas()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id');
		$data = array(
			'id' => $this->input->post('id', TRUE),
			'nama' => $this->input->post('nama', TRUE),
			'unit' => $this->input->post('unit', TRUE),
		);

		if ($this->M_kelas->edit_kelas($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataKelas', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahKelas/' . $id, 'refresh');
		}
	}
	//End Kelas

	//Start User
	public function dataUser()
	{
		$this->isAnyLogin();
		$data['user'] = $this->M_user->getSiswa();
		$this->load->view('guru/dataUser', $data);
	}
	public function tambahUser()
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelas();
		$this->load->view('guru/tambahUser', $data);
	}
	public function tambahUserDb()
	{
		$this->isAnyLogin();
		$pass = $this->input->post('password', TRUE);
		$repass = $this->input->post('repassword', TRUE);
		if ($pass == $repass) {
			$kelas =  $this->input->post('kelas', TRUE);
			if ($kelas == "") {
				$kelas = NULL;
			}
			$nik = $this->input->post('nik', TRUE);
			$userLama = $this->M_user->getUserByNik($nik);
			if ($userLama != null) {
				$data = array(
					'nik' => $nik,
					'password' => MD5($this->input->post('password', TRUE)),
					'nama' => $this->input->post('nama', TRUE),
					'unit' => $this->input->post('unit', TRUE),
					'kelas' => $kelas,
					'role' => ucwords($this->input->post('role', TRUE)),
					'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
				);
				$this->M_user->edit_userByNIK($nik, $data);
			} else {
				$data = array(
					'nik' => $nik,
					'password' => MD5($this->input->post('password', TRUE)),
					'nama' => $this->input->post('nama', TRUE),
					'unit' => $this->input->post('unit', TRUE),
					'kelas' => $kelas,
					'role' => ucwords($this->input->post('role', TRUE)),
					'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
				);
				$this->M_user->add($data);
			}
			redirect('guru/dataUser', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Gagal Menambah Data.</div>");
			redirect('guru/tambahUser', 'refresh');
		}
	}
	public function hapusUser($id)
	{
		$this->isAnyLogin();
		$row = $this->M_user->getUserById($id);
		if ($row) {
			$this->M_user->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataUser');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataUser');
		}
	}
	public function ubahUser($id)
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelas();
		$data['user'] = $this->M_user->getUserByID($id);
		$this->load->view('guru/ubahUser', $data);
	}
	public function simbahUbahUser()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id');
		$pass = $this->input->post('password', TRUE);
		$repass = $this->input->post('repassword', TRUE);
		$nik = $this->input->post('nik', TRUE);
		if ($pass == $repass) {
			$kelas =  $this->input->post('kelas', TRUE);
			if ($kelas == "") {
				$kelas = NULL;
			}
			$data = array(
				'no_absen' => $this->input->post('no_absen', TRUE),
				'nik' => $nik,
				'password' => MD5($this->input->post('password', TRUE)),
				'nama' => $this->input->post('nama', TRUE),
				'unit' => $this->input->post('unit', TRUE),
				'kelas' => $kelas,
				'role' => ucwords($this->input->post('role', TRUE)),
				'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
			);
		}

		if ($this->M_user->edit_user($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataUser', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahUser/' . $id, 'refresh');
		}
	}
	public function importUser()
	{
		$this->isAnyLogin();
		$this->load->view('guru/importUser');
	}
	public function importUserDb()
	{
		$files = '';
		$upload = $this->M_user->upload_file($files);
		$upload_data = $this->upload->data();
		$file_name =   $upload_data['file_name'];
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		$reader->setReadDataOnly(true);
		$sheet = $reader->load('./application/upload/' . $file_name);

		$worksheetData = $reader->listWorksheetInfo('./application/upload/' . $file_name);
		$data = array();
		$numrow = 1;
		foreach ($worksheetData as $row) {
			$sheetName = $row['worksheetName'];
			$reader->setLoadSheetsOnly($sheetName);
			$spreadsheet = $reader->load('./application/upload/' . $file_name);
			$row = $spreadsheet->getActiveSheet();
			$arrayRow = $row->toArray();
			foreach ($arrayRow as $cell) {
				if ($numrow > 1) {
					array_push($data, array(
						'no_absen' => $cell[0],
						'nik' => $cell[1],
						'password' => MD5($cell[2]),
						'nama' => $cell[3],
						'unit' => $cell[4],
						'kelas' => $cell[5],
						'role' => $cell[6],
						'jenis_kelamin' => $cell[7],
					));
					$nik = $cell[1];
					$userLama = $this->M_user->getUserByNik($nik);
					if ($userLama != null) {
						$data = array(
							'no_absen' => $cell[0],
							'nik' => $nik,
							'password' => MD5($cell[2]),
							'nama' => $cell[3],
							'unit' => $cell[4],
							'kelas' => $cell[5],
							'role' => $cell[6],
							'jenis_kelamin' => $cell[7],
						);
						$this->M_user->edit_userByNIK($nik, $data);
					} else {
						$data = array(
							'no_absen' => $cell[0],
							'nik' => $nik,
							'password' => MD5($cell[2]),
							'nama' => $cell[3],
							'unit' => $cell[4],
							'kelas' => $cell[5],
							'role' => $cell[6],
							'jenis_kelamin' => $cell[7],
						);
						$this->M_user->add($data);
					}
				}
				$numrow++;
			}
		}
		// $this->M_user->insert_multiple($data);

		redirect("guru/dataUser");
	}
	//End User

	//Start Soal
	public function dataSoal()
	{
		$this->isAnyLogin();
		$data['soal'] = $this->M_soalpg->getSoalpgByNik($this->session->nik);
		$this->load->view('guru/dataSoal', $data);
	}
	public function tambahSoal()
	{
		$this->isAnyLogin();
		$data['gambar'] = $this->M_gambar->getGambarByNik($this->session->nik);
		$data['kelas'] = $this->M_kelas->getKelas();
		$this->load->view('guru/tambahSoal', $data);
	}
	public function tambahSoalDb()
	{
		$this->isAnyLogin();
		$gs =  $this->input->post('gambarSoal', TRUE);
		if ($gs == "") {
			$gs = null;
		}
		$ga =  $this->input->post('gambarA', TRUE);
		if ($ga == "") {
			$ga = null;
		}
		$gb =  $this->input->post('gambarB', TRUE);
		if ($gb == "") {
			$gb = null;
		}
		$gc =  $this->input->post('gambarC', TRUE);
		if ($gc == "") {
			$gc = null;
		}
		$gd =  $this->input->post('gambarD', TRUE);
		if ($gd == "") {
			$gd = null;
		}
		$ge =  $this->input->post('gambarE', TRUE);
		if ($ge == "") {
			$ge = null;
		}
		$a = $this->input->post('a', TRUE);
		$kunci_pg = $this->input->post('kunci_pg', TRUE);
		$kunci_jawaban = null;
		if ($kunci_pg == "A") {
			$kunci_jawaban = $this->input->post('a', TRUE);
		} else if ($kunci_pg == "B") {
			$kunci_jawaban = $this->input->post('b', TRUE);
		} else if ($kunci_pg == "C") {
			$kunci_jawaban = $this->input->post('c', TRUE);
		} else if ($kunci_pg == "D") {
			$kunci_jawaban = $this->input->post('d', TRUE);
		} else if ($kunci_pg == "E") {
			$kunci_jawaban = $this->input->post('e', TRUE);
		}
		$cekSoal=0;
		$cekA=0;
		$cekB=0;
		$cekC=0;
		$cekD=0;
		$cekE=0;
		if($this->input->post('checkSoal', TRUE) != null){
			$cekSoal = 1;
		}
		if($this->input->post('checkA', TRUE) != null){
			$cekA = 1;
		}
		if($this->input->post('checkB', TRUE) != null){
			$cekB = 1;
		}
		if($this->input->post('checkC', TRUE) != null){
			$cekC = 1;
		}
		if($this->input->post('checkD', TRUE) != null){
			$cekD = 1;
		}
		if($this->input->post('checkE', TRUE) != null){
			$cekE = 1;
		}
		$data = array(
			'materi' => $this->input->post('materi', TRUE),
			'kd' => $this->input->post('kd', TRUE),
			'soal' => $this->input->post('soal', TRUE),
			'a' => $this->input->post('a', TRUE),
			'b' => $this->input->post('b', TRUE),
			'c' => $this->input->post('c', TRUE),
			'd' => $this->input->post('d', TRUE),
			'e' => $this->input->post('e', TRUE),
			'checkSoal' => $cekSoal,
			'checkA' => $cekA,
			'checkB' => $cekB,
			'checkC' => $cekC,
			'checkD' => $cekD,
			'checkE' => $cekE,
			'kelas' => $this->input->post('kelas', TRUE),
			'gambarSoal' => $gs,
			'gambarA' => $ga,
			'gambarB' => $gb,
			'gambarC' => $gc,
			'gambarD' => $gd,
			'gambarE' => $ge,
			'kunci_pg' => $kunci_pg,
			'kunci_jawaban' => $kunci_jawaban,
			'nik' => $this->session->nik,
		);
		$this->M_soalpg->add($data);
		redirect('guru/dataSoal', 'refresh');
	}
	public function hapusSoal($id)
	{
		$this->isAnyLogin();
		$row = $this->M_soalpg->getSoalpgByID($id);
		if ($row) {
			$this->M_soalpg->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataSoal');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataSoal');
		}
	}
	public function ubahSoal($id)
	{
		$this->isAnyLogin();
		$data['gambar'] = $this->M_gambar->getGambarByNik($this->session->nik);
		$data['soal'] = $this->M_soalpg->getSoalpgByID($id);
		$data['kelas'] = $this->M_kelas->getKelas();
		$this->load->view('guru/ubahSoal', $data);
	}
	public function simpanUbahSoal()
	{
		$this->isAnyLogin();
		$gs =  $this->input->post('gambarSoal', TRUE);
		if ($gs == "") {
			$gs = null;
		}
		$ga =  $this->input->post('gambarA', TRUE);
		if ($ga == "") {
			$ga = null;
		}
		$gb =  $this->input->post('gambarB', TRUE);
		if ($gb == "") {
			$gb = null;
		}
		$gc =  $this->input->post('gambarC', TRUE);
		if ($gc == "") {
			$gc = null;
		}
		$gd =  $this->input->post('gambarD', TRUE);
		if ($gd == "") {
			$gd = null;
		}
		$ge =  $this->input->post('gambarE', TRUE);
		if ($ge == "") {
			$ge = null;
		}
		$id = $this->input->post('id');
		$kunci_pg = $this->input->post('kunci_pg', TRUE);
		$kunci_jawaban = null;
		if ($kunci_pg == "A") {
			$kunci_jawaban = $this->input->post('a', TRUE);
		} else if ($kunci_pg == "B") {
			$kunci_jawaban = $this->input->post('b', TRUE);
		} else if ($kunci_pg == "C") {
			$kunci_jawaban = $this->input->post('c', TRUE);
		} else if ($kunci_pg == "D") {
			$kunci_jawaban = $this->input->post('d', TRUE);
		} else if ($kunci_pg == "E") {
			$kunci_jawaban = $this->input->post('e', TRUE);
		}
		$cekSoal=0;
		$cekA=0;
		$cekB=0;
		$cekC=0;
		$cekD=0;
		$cekE=0;
		if($this->input->post('checkSoal', TRUE) != null){
			$cekSoal = 1;
		}
		if($this->input->post('checkA', TRUE) != null){
			$cekA = 1;
		}
		if($this->input->post('checkB', TRUE) != null){
			$cekB = 1;
		}
		if($this->input->post('checkC', TRUE) != null){
			$cekC = 1;
		}
		if($this->input->post('checkD', TRUE) != null){
			$cekD = 1;
		}
		if($this->input->post('checkE', TRUE) != null){
			$cekE = 1;
		}
		$data = array(
			'materi' => $this->input->post('materi', TRUE),
			'kd' => $this->input->post('kd', TRUE),
			'soal' => $this->input->post('soal', TRUE),
			'a' => $this->input->post('a', TRUE),
			'b' => $this->input->post('b', TRUE),
			'c' => $this->input->post('c', TRUE),
			'd' => $this->input->post('d', TRUE),
			'e' => $this->input->post('e', TRUE),
			'checkSoal' => $cekSoal,
			'checkA' => $cekA,
			'checkB' => $cekB,
			'checkC' => $cekC,
			'checkD' => $cekD,
			'checkE' => $cekE,
			'kelas' => $this->input->post('kelas', TRUE),
			'gambarSoal' => $gs,
			'gambarA' => $ga,
			'gambarB' => $gb,
			'gambarC' => $gc,
			'gambarD' => $gd,
			'gambarE' => $ge,
			'kunci_pg' => $this->input->post('kunci_pg', TRUE),
			'kunci_jawaban' => $kunci_jawaban,
		);
		if ($this->M_soalpg->editSoalPg($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataSoal', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahSoal/' . $id, 'refresh');
		}
	}
	public function importSoal()
	{
		$this->isAnyLogin();
		$this->load->view('guru/importSoal');
	}
	public function importSoalDb()
	{
		$this->isAnyLogin();
		$files = '';
		$file = $this->input->post('file2', TRUE);
		$upload = $this->M_user->upload_file($files);
		$upload_data = $this->upload->data();
		$file_name =   $upload_data['file_name'];
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		$reader->setReadDataOnly(true);
		$sheet = $reader->load('./application/upload/' . $file_name);

		$worksheetData = $reader->listWorksheetInfo('./application/upload/' . $file_name);
		$data = array();
		$numrow = 1;
		foreach ($worksheetData as $row) {
			$sheetName = $row['worksheetName'];
			$reader->setLoadSheetsOnly($sheetName);
			$spreadsheet = $reader->load('./application/upload/' . $file_name);
			$row = $spreadsheet->getActiveSheet();
			$arrayRow = $row->toArray();
			foreach ($arrayRow as $cell) {
				if ($numrow > 1) {
					$kunci_pg = trim(strtoupper($cell[8]));
					$kunci_jawaban = null;
					if ($kunci_pg == "A") {
						$kunci_jawaban = $cell[3];
					} else if ($kunci_pg == "B") {
						$kunci_jawaban = $cell[4];
					} else if ($kunci_pg == "C") {
						$kunci_jawaban = $cell[5];
					} else if ($kunci_pg == "D") {
						$kunci_jawaban = $cell[6];
					} else if ($kunci_pg == "E") {
						$kunci_jawaban = $cell[7];
					}
					array_push($data, array(
						'materi' => $cell[0],
						'kd' => $cell[1],
						'soal' => $cell[2],
						'a' => $cell[3],
						'b' => $cell[4],
						'c' => $cell[5],
						'd' => $cell[6],
						'e' => $cell[7],
						'kunci_pg' => trim(strtoupper($cell[8])),
						'kunci_jawaban' => $kunci_jawaban,
						'nik' => $this->session->nik,
						'kelas' => $cell[9],
					));
				}
				$numrow++;
			}
		}
		$this->M_soalpg->insert_multiple($data);

		redirect("guru/dataSoal	");
	}
	//end Soal
	//Start Soal Isian
	public function dataSoalIsian()
	{
		$this->isAnyLogin();
		$data['soalisian'] = $this->M_soalisian->getSoalIsianByNik($this->session->nik);
		$this->load->view('guru/dataSoalIsian', $data);
	}
	public function tambahSoalIsian()
	{
		$this->isAnyLogin();
		$data['gambar'] = $this->M_gambar->getGambarByNik($this->session->nik);
		$data['kelas'] = $this->M_kelas->getKelas();
		$this->load->view('guru/tambahSoalIsian', $data);
	}
	public function tambahSoalIsianDb()
	{
		$this->isAnyLogin();
		$gs =  $this->input->post('gambarSoal', TRUE);
		if ($gs == "") {
			$gs = null;
		}
		$data = array(
			'materi' => $this->input->post('materi', TRUE),
			'kd' => $this->input->post('kd', TRUE),
			'soal' => $this->input->post('soal', TRUE),
			'kelas' => $this->input->post('kelas', TRUE),
			'gambarSoal' => $gs,
			'nik' => $this->session->nik,
			'bobot' => $this->input->post('bobot', TRUE),
		);
		$this->M_soalisian->add($data);
		redirect('guru/dataSoalIsian', 'refresh');
	}
	public function hapusSoalIsian($id)
	{
		$this->isAnyLogin();
		$row = $this->M_soalisian->getSoalisianByID($id);
		if ($row) {
			$this->M_soalisian->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataSoalIsian');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataSoalIsian');
		}
	}
	public function ubahSoalIsian($id)
	{
		$this->isAnyLogin();
		$data['gambar'] = $this->M_gambar->getGambarByNik($this->session->nik);
		$data['soal'] = $this->M_soalisian->getSoalisianByID($id);
		$data['kelas'] = $this->M_kelas->getKelas();
		$this->load->view('guru/ubahSoalIsian', $data);
	}
	public function simpanUbahSoalIsian()
	{
		$this->isAnyLogin();
		$gs =  $this->input->post('gambarSoal', TRUE);
		if ($gs == "") {
			$gs = null;
		}
		$id = $this->input->post('id');
		$data = array(
			'materi' => $this->input->post('materi', TRUE),
			'kd' => $this->input->post('kd', TRUE),
			'soal' => $this->input->post('soal', TRUE),
			'kelas' => $this->input->post('kelas', TRUE),
			'gambarSoal' => $gs,
			'nik' => $this->session->nik,
			'bobot' => $this->input->post('bobot', TRUE),
		);
		if ($this->M_soalisian->editSoalisian($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataSoalIsian', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahSoalIsian/' . $id, 'refresh');
		}
	}
	public function importSoalIsian()
	{
		$this->isAnyLogin();
		$this->load->view('guru/importSoalIsian');
	}
	public function importSoalIsianDb()
	{
		$files = '';
		$upload = $this->M_user->upload_file($files);
		$upload_data = $this->upload->data();
		$file_name =   $upload_data['file_name'];

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		$reader->setReadDataOnly(true);
		$sheet = $reader->load('./application/upload/' . $file_name);

		$worksheetData = $reader->listWorksheetInfo('./application/upload/' . $file_name);
		$data = array();
		$numrow = 1;
		foreach ($worksheetData as $row) {
			$sheetName = $row['worksheetName'];
			$reader->setLoadSheetsOnly($sheetName);
			$spreadsheet = $reader->load('./application/upload/' . $file_name);
			$row = $spreadsheet->getActiveSheet();
			$arrayRow = $row->toArray();
			foreach ($arrayRow as $cell) {
				if ($numrow > 1) {
					array_push($data, array(
						'materi' => $cell[0],
						'kd' => $cell[1],
						'soal' => $cell[2],
						'kunci_jawaban1' => $cell[3],
						'kunci_jawaban2' => $cell[4],
						'kunci_jawaban3' => $cell[5],
						'kunci_jawaban4' => $cell[6],
						'kunci_jawaban5' => $cell[7],
						'nik' => $this->session->nik,
					));
				}
				$numrow++;
			}
		}
		$this->M_soalisian->insert_multiple($data);

		redirect("guru/dataSoalIsian");
	}
	//end Soal

	//Start Ujian
	public function dataUjian()
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianTunggalByNik($this->session->nik);
		$this->load->view('guru/dataUjian', $data);
	}
	public function tambahUjian()
	{
		$this->isAnyLogin();
		$this->load->view('guru/tambahUjian');
	}
	public function tambahUjianDb()
	{
		$this->isAnyLogin();
		$persentasePg= 0;
		$persentaseIsian =0;
		if($this->input->post('jenis', TRUE) == "Pilihan Ganda"){
			$persentasePg = 100;
		}else{
			$persentaseIsian = 100;
		}
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'waktu' => $this->input->post('waktu', TRUE),
			'jenis' => $this->input->post('jenis', TRUE),
			'tipe' => $this->input->post('tipe', TRUE),
			'tipe_ujian' =>  $this->input->post('tipe_ujian', TRUE),
			'waktu_mulai' =>  $this->input->post('waktu_mulai', TRUE),
			'status' => $this->input->post('status', TRUE),
			'kkm' => $this->input->post('kkm', TRUE),
			'mata_pelajaran' => $this->input->post('mata_pelajaran', TRUE),
			'nik' => $this->session->nik,
			'tahun_ajaran' =>  $this->input->post('tahun_ajaran', TRUE),
			'materi_pokok' =>  $this->input->post('materi_pokok', TRUE),
			'kelas' =>  $this->input->post('kelas', TRUE),
			'bab' =>  $this->input->post('bab', TRUE),
			'persentase_pg' => $persentasePg,
			'persentase_isian' => $persentaseIsian,
		);
		$this->M_ujian->add($data);
		redirect('guru/dataUjian', 'refresh');
	}
	public function resetUjian($id)
	{
		$this->isAnyLogin();
		$row = $this->M_ujian->getUjianById($id);
		if ($row) {
			if ($row->jenis == "Isian") {
				$a = $this->M_jawaban_siswa_isian->deleteJawabanSiswaByIdUjian($row->id);
				$ujianHasSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($row->id);
			} else {
				$a = $this->M_jawaban_siswa->deleteJawabanSiswaByIdUjian($row->id);
				$ujianHasSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($row->id);
			}
			if ($ujianHasSoal != null) {
				$this->M_ujian_has_soal->deleteByIdUjian($id);
			}
			$nilai = $this->M_nilai->getNilaiByIdUjian2($id);
			if ($nilai != null) {
				$this->M_nilai->deleteByIdUjian($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataujian');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataujian');
		}
	}
	public function hapusUjian($id)
	{
		$this->isAnyLogin();
		$row = $this->M_ujian->getUjianById($id);
		if ($row) {
			$ujianHasSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian2($id);
			if ($ujianHasSoal != null) {
				$this->M_ujian_has_soal->deleteByIdUjian($id);
			}
			$nilai = $this->M_nilai->getNilaiByIdUjian2($id);
			if ($nilai != null) {
				$this->M_nilai->deleteByIdUjian($id);
			}
			$this->M_ujian->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataujian');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataujian');
		}
	}
	public function ubahUjian($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$this->load->view('guru/ubahUjian', $data);
	}
	public function simpanUbahUjian()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id');
		if($this->input->post('jenis', TRUE) == "Pilihan Ganda"){
			$persentasePg = 100;
		}else{
			$persentaseIsian = 100;
		}
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'waktu' => $this->input->post('waktu', TRUE),
			'jenis' => $this->input->post('jenis', TRUE),
			'tipe' => $this->input->post('tipe', TRUE),
			'tipe_ujian' =>  $this->input->post('tipe_ujian', TRUE),
			'waktu_mulai' =>  $this->input->post('waktu_mulai', TRUE),
			'status' => $this->input->post('status', TRUE),
			'kkm' => $this->input->post('kkm', TRUE),
			'mata_pelajaran' => $this->input->post('mata_pelajaran', TRUE),
			'tahun_ajaran' =>  $this->input->post('tahun_ajaran', TRUE),
			'materi_pokok' =>  $this->input->post('materi_pokok', TRUE),
			'kelas' =>  $this->input->post('kelas', TRUE),
			'bab' =>  $this->input->post('bab', TRUE),
			'persentase_pg' =>  $persentasePg,
			'persentase_isian' =>  $persentaseIsian,
		);
		if ($this->M_ujian->editUjian($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataUjian', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahUjian/' . $id, 'refresh');
		}
	}
	public function addSoalUjian($id)
	{
		// ga di pake lg
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal');
		if (isset($id_soal)) {
			var_dump($id_soal);
			exit;
		}
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpgByNik($this->session->nik);
			$this->load->view('guru/addSoalUjian', $data);
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsianByNik($this->session->nik);
			$this->load->view('guru/addSoalUjianIsian', $data);
		}
	}
	public function addSoalUjian2()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal');
		$id = $this->input->post('idUjian');
		$lastData = $this->M_ujian_has_soal->getCountSoalByIdUjian($id);
		if (isset($id_soal)) {
			for ($i = 0; $i < count($id_soal); $i++) {
				$lastData += 1;
				$data = array(
					'id_soal' => $id_soal[$i],
					'id_ujian' => $id,
					'nik' => $_SESSION['nik'],
					'no_soal' => $lastData,
				);
				$this->M_ujian_has_soal->add($data);
			}
			$this->dataUjian();
		} else {
			echo "<script type='text/javascript'>alert('Tidak ada data yang dipilih')</script>";
			$data['ujian'] = $this->M_ujian->getUjianByID($id);
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$a = $this->M_ujian->getUjianByID($id);
			if ($a->jenis == "Pilihan Ganda") {
				$data['soal'] = $this->M_soalpg->getSoalpg();
				$this->load->view('guru/addSoalUjian', $data);
			} else {
				$data['soal'] = $this->M_soalisian->getSoalIsian();
				$this->load->view('guru/addSoalUjianIsian', $data);
			}
		}
	}

	public function addSoalUjianDb($id, $id2, $kunci)
	{
		$this->isAnyLogin();
		$data = array(
			'id_soal' => $id,
			'id_ujian' => $id2,
			'kunci_jawaban' => $kunci,
		);
		$this->M_ujian_has_soal->add($data);
		redirect('guru/addSoalUjian/' . $id2, 'refresh');
	}
	public function addSoalUjianDbIsian($id, $id2, $kunci, $kunci2, $kunci3, $kunci4, $kunci5)
	{
		$this->isAnyLogin();
		if ($kunci2 == -999) {
			$kunci2 = null;
		}
		if ($kunci3 == -999) {
			$kunci3 = null;
		}
		if ($kunci4 == -999) {
			$kunci4 = null;
		}
		if ($kunci5 == -999) {
			$kunci5 = null;
		}
		$data = array(
			'id_soal' => $id,
			'id_ujian' => $id2,
			'kunci_jawaban' => $kunci,
			'kunci_jawaban2' => $kunci2,
			'kunci_jawaban3' => $kunci3,
			'kunci_jawaban4' => $kunci4,
			'kunci_jawaban5' => $kunci5,
			'nik' => $this->session->nik,
		);
		$this->M_ujian_has_soal->add($data);
		redirect('guru/addSoalUjian/' . $id2, 'refresh');
	}
	public function detailUjian($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$this->load->view('guru/detailUjian', $data);
		} else {
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			$this->load->view('guru/detailUjianIsian', $data);
		}
	}
	public function editNomer($id, $idUjian)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($idUjian);
		$data['id_ujian'] = $idUjian;
		$a = $this->M_ujian->getUjianByID($idUjian);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalById($id);
			$this->load->view('guru/ubahNomer', $data);
		} else {
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalById($id);
			$this->load->view('guru/ubahNomer', $data);
		}
	}
	public function editNomerGabungan($id, $idUjian)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($idUjian);
		$data['id_ujian'] = $idUjian;
		$data['soal_ujian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalById($id);
		$this->load->view('guru/ubahNomerGabungan', $data);
	}
	public function simpanUbahNomer()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id', TRUE);
		$idUjian = $this->input->post('id_ujian', TRUE);
		$data = array(
			'id' => $id,
			'no_soal' => $this->input->post('no_soal', TRUE)
		);
		$this->M_ujian_has_soal->editUjianHasSoal($id,$data);
		redirect('guru/detailUjian/' . $idUjian, 'refresh');
	}
	public function simpanUbahNomerGabungan()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id', TRUE);
		$idUjian = $this->input->post('id_ujian', TRUE);
		$data = array(
			'id' => $id,
			'no_soal' => $this->input->post('no_soal', TRUE)
		);
		$this->M_ujian_gabungan_has_soal->editUjianGabunganHasSoal($id,$data);
		redirect('guru/detailUjianGabungan/' . $idUjian, 'refresh');
	}
	public function hapusUjianHasSoal($id, $id2)
	{
		$this->isAnyLogin();
		$row = $this->M_ujian_has_soal->getUjianHasSoalById($id);
		if ($row) {
			$this->M_ujian_has_soal->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/detailUjian/' . $id2);
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/detailUjian/' . $id2);
		}
	}

	public function testUjian($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$this->load->view('siswa/informasiUjian2', $data);
	}

	public function shareUjian($id)
	{
		// echo "<script>alert('Copy link ini = http://talentaschool.sch.id:8060/ujian_sekolah/guru/shareUjian/" . $id . "')</script>";
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		} else {
			$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
		}
		$this->load->view('share/ujian', $data);
	}
	public function shareUjianGabungan($id)
	{
		// echo "<script>alert('Copy link ini = http://talentaschool.sch.id:8060/ujian_sekolah/guru/shareUjian/" . $id . "')</script>";
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$data['soal_ujian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjian($id);
		$test = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjian($id);
		$data['soal_ujian_isian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjianIsian($id);
		$a = $this->M_ujian->getUjianByID($id);
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$data['soalisian'] = $this->M_soalisian->getSoalIsian();
		$this->load->view('share/ujianGabungan', $data);
	}
	//End Ujian

	//Start Ujian Gabungan
	public function dataUjianGabungan()
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianGabunganByNik($this->session->nik);
		$this->load->view('guru/dataUjianGabungan', $data);
	}
	public function tambahUjianGabungan()
	{
		$this->isAnyLogin();
		$this->load->view('guru/tambahUjianGabungan');
	}
	public function tambahUjianDbGabungan()
	{
		$this->isAnyLogin();
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'waktu' => $this->input->post('waktu', TRUE),
			'tipe' => "Gabungan",
			// 'tipe_ujian' => $this->input->post('tipe_ujian', TRUE),
			// 'waktu_mulai' => NULL,
			'jenis' => $this->input->post('jenis', TRUE),
			'status' => $this->input->post('status', TRUE),
			'kkm' => $this->input->post('kkm', TRUE),
			'mata_pelajaran' =>  $this->input->post('mata_pelajaran', TRUE),
			'bab' =>  $this->input->post('bab', TRUE),
			'tahun_ajaran' =>  $this->input->post('tahun_ajaran', TRUE),
			'materi_pokok' =>  $this->input->post('materi_pokok', TRUE),
			'nik' => $this->session->nik,
			'kelas' =>  $this->input->post('kelas', TRUE),
			'persentase_pg' =>  $this->input->post('persentase_pg', TRUE),
			'persentase_isian' =>  $this->input->post('persentase_isian', TRUE),
		);
		// var_dump($data);exit;
		$test = $this->M_ujian->add($data);
		redirect('guru/dataUjianGabungan', 'refresh');
	}
	public function hapusUjianGabungan($id)
	{
		$this->isAnyLogin();
		$row = $this->M_ujian->getUjianById($id);
		if ($row) {
			$this->M_ujian->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataUjianGabungan');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataUjianGabungan');
		}
	}
	public function ubahUjianGabungan($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$this->load->view('guru/ubahUjianGabungan', $data);
	}
	public function simpanUbahUjianGabungan()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id');
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'waktu' => $this->input->post('waktu', TRUE),
			'jenis' => $this->input->post('jenis', TRUE),
			'tipe' => $this->input->post('tipe', TRUE),
			'status' => $this->input->post('status', TRUE),
			'nik' => $this->session->nik,
			'tahun_ajaran' =>  $this->input->post('tahun_ajaran', TRUE),
			'kkm' => $this->input->post('kkm', TRUE),
			'tahun_ajaran' =>  $this->input->post('tahun_ajaran', TRUE),
			'materi_pokok' =>  $this->input->post('materi_pokok', TRUE),
			'persentase_pg' =>  $this->input->post('persentase_pg', TRUE),
			'persentase_isian' =>  $this->input->post('persentase_isian', TRUE),
		);
		if ($this->M_ujian->editUjian($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataUjianGabungan', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahUjianGabungan/' . $id, 'refresh');
		}
	}
	public function addSoalUjianGabungan($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$a = $this->M_ujian->getUjianByID($id);
		$data['soal'] = $this->M_soalpg->getSoalpgByNik($this->session->nik);
		$this->load->view('guru/addSoalUjianGabungan', $data);
	}
	public function addSoalUjianGabungan2()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal');
		$id = $this->input->post('idUjian');
		$lastData = $this->M_ujian_gabungan_has_soal->getCountSoalByIdUjian($id);
		if (isset($id_soal)) {
			for ($i = 0; $i < count($id_soal); $i++) {
				$lastData += 1;
				$data = array(
					'id_soal' => $id_soal[$i],
					'id_ujian' => $id,
					'nik' => $_SESSION['nik'],
					'tipe' => "Pilihan Ganda",
					'no_soal' => $lastData,
				);
				$this->M_ujian_gabungan_has_soal->add($data);
			}
			$this->dataUjianGabungan();
		} else {
			echo "<script type='text/javascript'>alert('Tidak ada data yang dipilih')</script>";
			$data['ujian'] = $this->M_ujian->getUjianByID($id);
			$a = $this->M_ujian->getUjianByID($id);
			$data['soal'] = $this->M_soalpg->getSoalpg();
			$this->load->view('guru/addSoalUjianGabungan', $data);
		}
	}
	public function addSoalIsianUjianGabungan()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal');
		$id = $this->input->post('idUjian');
		$lastData = $this->M_ujian_gabungan_has_soal->getCountSoalByIdUjian($id);
		if (isset($id_soal)) {
			for ($i = 0; $i < count($id_soal); $i++) {
				$lastData += 1;
				$data = array(
					'id_soal' => $id_soal[$i],
					'id_ujian' => $id,
					'nik' => $_SESSION['nik'],
					'tipe' => "Isian",
					'no_soal' => $lastData,
				);
				$this->M_ujian_gabungan_has_soal->add($data);
			}
			$this->dataUjianGabungan();
		} else {
			echo "<script type='text/javascript'>alert('Tidak ada data yang dipilih')</script>";
			$data['ujian'] = $this->M_ujian->getUjianByID($id);
			$a = $this->M_ujian->getUjianByID($id);
			$data['soal'] = $this->M_soalpg->getSoalpg();
			$this->load->view('guru/addSoalUjianGabungan', $data);
		}
	}

	public function addSoalIsianGabungan($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$a = $this->M_ujian->getUjianByID($id);
		$data['soal'] = $this->M_soalisian->getSoalIsianByNik($this->session->nik);
		$this->load->view('guru/addSoalIsianGabungan', $data);
	}

	public function addSoalPgGabunganDb($id, $id2, $kunci)
	{
		$this->isAnyLogin();
		$data = array(
			'id_soal' => $id,
			'id_ujian' => $id2,
			'kunci_jawaban' => $kunci,
			'tipe' => 'Pilihan Ganda',
			'nik' => $this->session->nik,
		);
		$this->M_ujian_gabungan_has_soal->add($data);
		redirect('guru/addSoalUjianGabungan/' . $id2, 'refresh');
	}
	public function addSoalUjianDbIsianGabungan($id, $id2, $kunci, $kunci2, $kunci3, $kunci4, $kunci5)
	{
		$this->isAnyLogin();
		if ($kunci2 == -999) {
			$kunci2 = null;
		}
		if ($kunci3 == -999) {
			$kunci3 = null;
		}
		if ($kunci4 == -999) {
			$kunci4 = null;
		}
		if ($kunci5 == -999) {
			$kunci5 = null;
		}
		$data = array(
			'id_soal' => $id,
			'id_ujian' => $id2,
			'kunci_jawaban' => $kunci,
			'kunci_jawaban2' => $kunci2,
			'kunci_jawaban3' => $kunci3,
			'kunci_jawaban4' => $kunci4,
			'kunci_jawaban5' => $kunci5,
			'tipe' => 'Isian',
			'nik' => $this->session->nik,
		);
		$this->M_ujian_gabungan_has_soal->add($data);
		redirect('guru/addSoalIsianGabungan/' . $id2, 'refresh');
	}
	public function detailUjianGabungan($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$data['soal_ujian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjian($id);
		$data['soal_ujian_isian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjianIsian($id);
		$a = $this->M_ujian->getUjianByID($id);
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$data['soalisian'] = $this->M_soalisian->getSoalIsian();
		$this->load->view('guru/detailUjianGabungan', $data);
	}
	public function detailUjianGabunganIsian($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$data['soal_ujian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjian($id);
		$data['soal_ujian_isian'] = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjianIsian($id);
		$a = $this->M_ujian->getUjianByID($id);
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$data['soalisian'] = $this->M_soalisian->getSoalIsian();
		$this->load->view('guru/detailUjianGabunganIsian', $data);
	}
	public function hapusUjianHasSoalGabungan($id, $id2)
	{
		$this->isAnyLogin();
		$row = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoal($id);
		if ($row) {
			$this->M_ujian_gabungan_has_soal->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/detailUjianGabungan/' . $id2);
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/detailUjianGabungan/' . $id2);
		}
	}
	//End Ujian Gabungan


	//Start Join Ujian
	public function joinUjian($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$this->load->view('guru/informasiUjian', $data);
	}
	public function startUjian()
	{
		$this->isAnyLogin();
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";

		$id = $this->input->post('id');
		date_default_timezone_set('Asia/Jakarta');
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$temp = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$isi = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$max = 0;
			foreach ($temp as $t) {
				$max += 1;
			}
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$temp = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			$isi = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			$max = 0;
			foreach ($temp as $t) {
				$max += 1;
			}
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}

		$data['max'] = $max;
		$data['soal_ujian'] = $temp;
		$data['index'] = 0;
		$this->session->waktu = date('M d, Y H:i:s', strtotime('+' . $a->waktu . ' minutes'));
		$data['ujian'] = $a;
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$tpz = [];
		$jml = count($isi);
		for ($i = 0; $i < $jml; $i++) {
			$tpz[$i] = 0;
		}
		$indexRandom = 0;
		$random = rand(0, $jml - 1);
		while ($indexRandom < $jml) {
			if ($tpz[$random] == 0) {
				$tpz[$random] = $isi[$indexRandom]->id_soal;
				$indexRandom += 1;
				$random = rand(0, $jml - 1);
			} else {
				if ($random == ($jml - 1)) {
					$random = 0;
				} else {
					$random += 1;
				}
			}
		}
		$this->session->soal_ujian_random = $tpz;
		$this->load->view('guru/ujianSoal', $data);
	}
	public function back($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getCountSoalByIdUjian($id);
		$max = $temp;
		if ($tempIndex <= 0) {
			$data['index'] = 0;
		} else {
			$data['index'] = $tempIndex - 1;
		}
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}
		$data['max'] = $max;
		$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('guru/ujianSoal', $data);
	}
	public function next($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getCountSoalByIdUjian($id);
		$max = $temp;
		if ($tempIndex >= $max - 1) {
			$data['index'] = $max - 1;
			echo '<script type="text/javascript">confirm("sudah soal terakhir)";</script>';
		} else {
			$data['index'] = $tempIndex + 1;
		}
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}
		$data['max'] = $max;
		$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		if ($tempIndex >= $max - 1) {
			echo "<script type='text/javascript'>
			var bol = confirm('Sudah soal terakhir');</script>";
			$this->load->view('guru/ujianSoal', $data);
		} else {
			$this->load->view('guru/ujianSoal', $data);
		}
	}
	public function addJawabanSiswa()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal', TRUE);
		$id_ujian = $this->input->post('id_ujian', TRUE);
		$waktu = $this->input->post('tanggal', TRUE);
		$tempIndex = $this->input->post('tempIndex', TRUE);
		$jawaban = $this->input->post('answer', TRUE);
		$pilihanJawaban = substr($jawaban, 0, 1);
		$hasilJawaban = substr($jawaban, 2);
		if ($jawaban != NULL) {
			$nik = $this->session->nik;
			$check = $this->M_jawaban_siswa->checkJawabanSiswa($id_soal, $id_ujian, $nik);
			if ($check == null) {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $hasilJawaban,
					'nomor_soal' => $tempIndex + 1,
					'pilihan_jawaban' => $pilihanJawaban,
				);
				$this->M_jawaban_siswa->add($data);
			} else {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $hasilJawaban,
					'nomor_soal' => $tempIndex + 1,
					'pilihan_jawaban' => $pilihanJawaban,
				);
				$this->M_jawaban_siswa->editJawabanSiswa($check->id, $data);
			}
			$this->next($id_ujian, $tempIndex);
		} else {
			$this->next($id_ujian, $tempIndex);
		}
	}
	public function addJawabanSiswaIsian()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal', TRUE);
		$id_ujian = $this->input->post('id_ujian', TRUE);
		$waktu = $this->input->post('tanggal', TRUE);
		$tempIndex = $this->input->post('tempIndex', TRUE);
		$jawaban = $this->input->post('jawabanSiswa', TRUE);
		$pilihanJawaban = substr($jawaban, 0, 1);
		$hasilJawaban = substr($jawaban, 2);
		if ($jawaban != NULL) {
			$nik = $this->session->nik;
			$check = $this->M_jawaban_siswa_isian->checkJawabanSiswaIsian($id_soal, $id_ujian, $nik);
			if ($check == null) {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $jawaban,
					'nomor_soal' => $tempIndex + 1,
				);
				$this->M_jawaban_siswa_isian->add($data);
			} else {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $this->input->post('jawabanSiswa', TRUE),
				);
				$this->M_jawaban_siswa_isian->editJawabanSiswaIsian($check->id, $data);
			}
			$this->next($id_ujian, $waktu, $tempIndex);
		} else {
			$this->next($id_ujian, $waktu, $tempIndex);
		}
	}
	public function terminate($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
			$count = 0;
			$jmlSoal = 0;
			$soalUjian = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
			foreach ($jawabanSiswa as $js) {
				$jmlSoal += 1;
				foreach ($soalUjian as $su) {
					if ($js->id_soal == $su->id_soal) {
						if (trim(strtolower($js->jawaban)) == trim(strtolower($su->kunci_jawaban))) {
							$count += 1;
							break;
						}
					}
				}
			}
			$n = $this->M_nilai->getNilaiByNikAndIdUjian($nik, $id);
			if ($n == null) {
				$nilaiAkhir = ($count * 100 / $jmlSoal);
				$data = array(
					'id_ujian' => $id,
					'nik' => $nik,
					'hasil' => $nilaiAkhir,
					'tampil' => "non-aktif",
					'ujian_ulang' => '0',
				);
				$this->M_nilai->add($data);
			} else {
				$nilaiAkhir = ($count * 100 / $jmlSoal);

				$data = array(
					'id_ujian' => $id,
					'nik' => $nik,
					'hasil' => $nilaiAkhir,
					'tampil' => "non-aktif",
					'ujian_ulang' => '0',
				);
				$this->M_nilai->editnilai($n[0]->id, $data);
			}
			$data['nilai'] = $this->M_nilai->getNilaiByNik($nik);
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
			$count = 0;
			$soalUjian = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
			$jmlSoal = 0;
			foreach ($soalUjian as $su) {
				$jmlSoal += 1;
				foreach ($jawabanSiswa as $js) {
					if ($js->id_soal == $su->id_soal) {
						if (trim(strtolower($js->jawaban)) == trim(strtolower($su->kunci_jawaban1))) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban2) && trim(strtolower($js->jawaban)) == trim(strtolower($su->kunci_jawaban2))) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban3) && trim(strtolower($js->jawaban)) == trim(strtolower($su->kunci_jawaban3))) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban4) && trim(strtolower($js->jawaban)) == trim(strtolower($su->kunci_jawaban4))) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban5) && trim(strtolower($js->jawaban)) == trim(strtolower($su->kunci_jawaban5))) {
							$count += 1;
							break;
						}
					}
				}
			}

			$n = $this->M_nilai->getNilaiByNikAndIdUjian($nik, $id);
			if ($n == null) {
				$nilaiAkhir = ($count * 100 / $jmlSoal);
				$data = array(
					'id_ujian' => $id,
					'nik' => $nik,
					'hasil' => $nilaiAkhir,
					'tampil' => "non-aktif",
					'ujian_ulang' => '0',
				);
				$this->M_nilai->add($data);
			} else {
				$nilaiAkhir = ($count * 100 / $jmlSoal);

				$data = array(
					'id_ujian' => $id,
					'nik' => $nik,
					'hasil' => $nilaiAkhir,
					'tampil' => "non-aktif",
					'ujian_ulang' => '0',
				);
				$this->M_nilai->editnilai($n[0]->id, $data);
			}
			$data['nilai'] = $this->M_nilai->getNilaiByNik($nik);
		}
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";
		$this->dataReport();
	}
	//End Join Ujian

	//Start Join Ujian Gabungan
	public function joinUjianGabungan($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$this->load->view('guru/informasiUjian', $data);
	}
	public function startUjianGabungan()
	{
		$this->isAnyLogin();
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";
		$id = $this->input->post('id');
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$max = 0;
		foreach ($temp as $t) {
			$max += 1;
		}
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else if ($a->jenis == "Isian") {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		} else {
			$data['soal'] = $this->M_soalpg->getSoalpg();
		}
		$data['max'] = $max;
		$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$data['index'] = 0;
		$this->session->waktu = date('M d, Y H:i:s', strtotime('+' . $a->waktu . ' minutes'));
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);

		$isi = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$tpz = [];
		$jml = count($isi);
		for ($i = 0; $i < $jml; $i++) {
			$tpz[$i] = 0;
		}
		$indexRandom = 0;
		$random = rand(0, $jml - 1);
		while ($indexRandom < $jml) {
			if ($tpz[$random] == 0) {
				$tpz[$random] = $isi[$indexRandom]->id_soal;
				$indexRandom += 1;
				$random = rand(0, $jml - 1);
			} else {
				if ($random == ($jml - 1)) {
					$random = 0;
				} else {
					$random += 1;
				}
			}
		}
		$this->session->soal_ujian_random = $tpz;
		$this->load->view('guru/ujianSoal', $data);
	}
	public function backGabungan($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$max = 0;
		foreach ($temp as $t) {
			$max += 1;
		}
		if ($tempIndex <= 0) {
			$data['index'] = 0;
		} else {
			$data['index'] = $tempIndex - 1;
		}
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}
		$data['max'] = $max;
		$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('guru/ujianSoal', $data);
	}
	public function nextGabungan($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$max = 0;
		foreach ($temp as $t) {
			$max += 1;
		}
		if ($tempIndex >= $max - 1) {
			$data['index'] = $max - 1;
		} else {
			$data['index'] = $tempIndex + 1;
		}
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}
		$data['max'] = $max;
		$data['soal_ujian'] = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('guru/ujianSoal', $data);
	}
	public function addJawabanSiswaGabungan()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal', TRUE);
		$id_ujian = $this->input->post('id_ujian', TRUE);
		$waktu = $this->input->post('tanggal', TRUE);
		$tempIndex = $this->input->post('tempIndex', TRUE);
		$jawaban = $this->input->post('answer', TRUE);
		$pilihanJawaban = substr($jawaban, 0, 1);
		$hasilJawaban = substr($jawaban, 2);
		if ($jawaban != NULL) {
			$nik = $this->session->nik;
			$check = $this->M_jawaban_siswa->checkJawabanSiswa($id_soal, $id_ujian, $nik);
			if ($check == null) {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $hasilJawaban,
					'nomor_soal' => $tempIndex + 1,
					'pilihan_jawaban' => $hasilJawaban,
				);
				$this->M_jawaban_siswa->add($data);
			} else {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $this->input->post('answer', TRUE),
				);
				$this->M_jawaban_siswa->editJawabanSiswa($check->id, $data);
			}
			$this->next($id_ujian, $tempIndex);
		} else {
			$this->next($id_ujian, $tempIndex);
		}
	}
	public function addJawabanSiswaIsianGabungan()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal', TRUE);
		$id_ujian = $this->input->post('id_ujian', TRUE);
		$waktu = $this->input->post('tanggal', TRUE);
		$tempIndex = $this->input->post('tempIndex', TRUE);
		$jawaban = $this->input->post('jawabanSiswa', TRUE);
		if ($jawaban != NULL) {
			$nik = $this->session->nik;
			$check = $this->M_jawaban_siswa_isian->checkJawabanSiswaIsian($id_soal, $id_ujian, $nik);
			if ($check == null) {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $this->input->post('jawabanSiswa', TRUE),
				);
				$this->M_jawaban_siswa_isian->add($data);
			} else {
				$data = array(
					'id_soal' => $id_soal,
					'id_ujian' => $id_ujian,
					'nik' => $nik,
					'jawaban' => $this->input->post('jawabanSiswa', TRUE),
				);
				$this->M_jawaban_siswa_isian->editJawabanSiswaIsian($check->id, $data);
			}
			$this->next($id_ujian, $waktu, $tempIndex);
		} else {
			$this->next($id_ujian, $waktu, $tempIndex);
		}
	}
	public function terminateGabungan($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$data['soal'] = $this->M_soalpg->getSoalpg();
			$count = 0;
			$soalUjian = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
			foreach ($jawabanSiswa as $js) {
				foreach ($soalUjian as $su) {
					if ($js->id_soal == $su->id) {
						if ($js->jawaban == $su->kunci_jawaban) {
							$count += 1;
							break;
						}
					}
				}
			}
			$n = $this->M_nilai->getNilaiByNikAndIdUjian($nik, $id);
			if ($n == null) {
				$nilaiAkhir = ($count / $a->jumlah_soal) * 100;
				$data = array(
					'id_ujian' => $id,
					'nik' => $nik,
					'hasil' => $nilaiAkhir,
					'tampil' => "non-aktif",
				);
				$this->M_nilai->add($data);
			}
			$data['nilai'] = $this->M_nilai->getNilaiByNik($nik);
		} else {
			$data['soal'] = $this->M_soalisian->getSoalIsian();
			$count = 0;
			$soalUjian = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
			$jmlSoal = 0;
			foreach ($soalUjian as $su) {
				$jmlSoal += 1;
				foreach ($jawabanSiswa as $js) {
					if ($js->id_soal == $su->id_soal) {
						if (strtolower($js->jawaban) == strtolower($su->kunci_jawaban)) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban2) && strtolower($js->jawaban) == strtolower($su->kunci_jawaban2)) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban3) && strtolower($js->jawaban) == strtolower($su->kunci_jawaban3)) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban4) && strtolower($js->jawaban) == strtolower($su->kunci_jawaban4)) {
							$count += 1;
							break;
						} else if (isset($su->kunci_jawaban5) && strtolower($js->jawaban) == strtolower($su->kunci_jawaban5)) {
							$count += 1;
							break;
						}
					}
				}
			}

			$n = $this->M_nilai->getNilaiByNikAndIdUjian($nik, $id);
			if ($n == null) {
				$nilaiAkhir = ($count / $jmlSoal) * 100;
				$data = array(
					'id_ujian' => $id,
					'nik' => $nik,
					'hasil' => $nilaiAkhir,
					'tampil' => "non-aktif",
				);
				$this->M_nilai->add($data);
			}

			$data['nilai'] = $this->M_nilai->getNilaiByNik($nik);
		}
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";
		$this->load->view('guru/lihatNilai', $data);
	}
	//End Join Ujian Gabungan

	//Start Report
	public function dataReport()
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianByNik($this->session->nik);
		$this->load->view('guru/dataReport', $data);
	}

	public function detailReport($id, $jenis)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['jenis'] = $jenis;
		$this->load->view('guru/detailReport', $data);
	}

	public function tampilNilai($id, $nilaiId)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data = array(
			'id' => $nilaiId,
			'tampil' => 'aktif',
		);
		$this->M_nilai->editnilai($nilaiId, $data);
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['jenis'] = "";
		$dataNilai = $this->M_nilai->getNilaiByID($nilaiId);
		$this->load->view('guru/detailReport', $data);
	}
	public function nonTampilNilai($id, $nilaiId)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data = array(
			'id' => $nilaiId,
			'tampil' => 'non-aktif',
		);
		$this->M_nilai->editnilai($nilaiId, $data);
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['jenis'] = "";
		$dataNilai = $this->M_nilai->getNilaiByID($nilaiId);
		$this->load->view('guru/detailReport', $data);
	}
	public function ujianUlang($id, $nilaiId, $nik)
	{
		$this->isAnyLogin();
		$data = array(
			'id' => $nilaiId,
			'ujian_ulang' => '1',
			'hasil' => 0,
		);
		$ujian =  $this->M_ujian->getUjianById($id);
		$jenis = "PilihanGanda";
		if ($ujian->jenis == "Pilihan Ganda") {
			$this->M_jawaban_siswa->deleteJawabanSiswaByNik($nik, $id);
		}
		else if ($ujian->jenis == "Isian") {
			$this->M_jawaban_siswa_isian->deleteJawabanSiswaByNik($nik, $id);
			$jenis = "Isian";
		} 
		else if($ujian->tipe == "Gabungan"){
			$this->M_jawaban_siswa->deleteJawabanSiswaByNik($nik, $id);
			$this->M_jawaban_siswa_isian->deleteJawabanSiswaByNik($nik, $id);
			$jenis = "Gabungan";
		}
		$this->M_nilai->editnilai($nilaiId, $data);
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$dataNilai = $this->M_nilai->getNilaiByID($nilaiId);
		redirect('guru/detailReport/' . $id . '/' . $jenis, 'refresh');
	}

	public function createPdf2($id, $nis, $kelas)
	{
		$this->isAnyLogin();
		$siswa = $this->M_user->getUserByNik($nis);
		$ujian = $this->M_ujian->getUjianById($id);
		$ujianHasSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
		$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik($nis, $id);
		if ($ujian->jenis = "Pilihan Ganda") {
			$soal = $this->M_soalpg->getSoalpg();
		} else {
			$soal = $this->M_soalIsian->getSoalIsian();
		}
		$dataJawaban = [];
		for ($i = 0; $i < count($jawabanSiswa); $i++) {
			$dataJawaban[$jawabanSiswa[$i]->id_soal] = $jawabanSiswa[$i]->jawaban;
		}
		$nilaiSiswa = $this->M_nilai->getNilaiByNikAndIdUjian($nis, $id);

		$pdf = new FPDF('l', 'mm', 'A4');
		$pdf->AddPage();
		$pdf->Image('./assets/img/logo.png', 7, 6, 30);
		$pdf->SetFont('Arial', 'B', 15);
		$pdf->Cell(40);
		$pdf->Cell(0, 0, $this->session->unit . ' TALENTA', 0, 0);
		$pdf->Ln(7);
		$pdf->Cell(40);
		$pdf->Cell(0, 0, 'TAMAN KOPO INDAH III, BLOK F1', 0, 0);
		$pdf->Ln(7);
		$pdf->Cell(40);
		$pdf->Cell(0, 0, 'KAB. BANDUNG', 0, 0);
		$pdf->SetLineWidth(2);
		$pdf->Line(5, 40, 250, 40);
		$pdf->Line(40, 5, 40, 45);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Ln(25);
		$pdf->Cell(40);
		$pdf->Cell(0, 0, $ujian->nama, 0, 0);
		$pdf->Ln(7);
		$pdf->Cell(40);
		$pdf->Cell(0, 0, 'Kelas: ' . $kelas, 0, 0);
		$pdf->Ln(7);
		$pdf->Cell(0, 0, 'NAMA/KELAS/NO.ABSEN : ' . $siswa->nama . ' / ' . $siswa->kelas, 0, 0);
		$pdf->Ln(7);
		$pdf->SetLineWidth(1);
		$pdf->Line(5, 80, 250, 80);
		$pdf->Line(5, 70, 250, 70);
		$pdf->Cell(10, 11, 'SOAL', 0, 1);
		$pdf->SetFont('Arial', '', 12);
		for ($i = 0; $i < count($ujianHasSoal); $i++) {
			foreach ($soal as $s) {
				if ($ujianHasSoal[$i]->id_soal == $s->id) {
					$output = $i + 1 . '. ' . $s->soal;
					$outputA =  'A. ' . $s->a;
					$outputB =  'B. ' . $s->b;
					$outputC =  'C. ' . $s->c;
					$outputD =  'D. ' . $s->d;
					$pdf->Cell(10, 10, $output, 0, 1);
					$pdf->Cell(10, 7, $outputA, 0, 1);
					$pdf->Cell(10, 7, $outputB, 0, 1);
					$pdf->Cell(10, 7, $outputC, 0, 1);
					$pdf->Cell(10, 7, $outputD, 0, 1);
					if (isset($dataJawaban[$s->id]) && $dataJawaban[$s->id] == $s->kunci_jawaban) {
						$pdf->Cell(10, 7, 'Benar', 0, 1);
					} else {
						$pdf->Cell(10, 7, 'Salah', 0, 1);
					}
					$pdf->Cell(10, 10, ' ', 0, 1);
					break;
				}
			}
		}
		$pdf->AliasNbPages();
		$pdf->Output();
	}
	public function downloadNaskahAll($id)
	{
		$this->isAnyLogin();

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayTop = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayTop2 = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayLeft = [
			'borders' => [
				'left' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayOut = [
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$headingArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 14,
				'name'  => 'Arial',
			)
		);
		$contentArray = array(
			'font'  => array(
				'bold'  => false,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial',
			)
		);
		$leadArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 10,
				'name'  => 'Arial'
			)
		);
		$allSiswa = $this->M_nilai->getNilaiByIdUjian($id);
		//var_dump($allSiswa);exit;
		$ujian = $this->M_ujian->getUjianById($id);

		$list_kd = [];
		$ctLk = 0;
		$check = true;

		if ($ujian->tipe == "Gabungan") {
			$soalPg = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
			$soalIsian = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
			//$jawabanSiswaPg = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nis,$id);
			//$jawabanSiswaIsian = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nis,$id);
			$jumlahSoal = count($soalPg) + count($soalIsian);
			$allSoal = [];
			for ($i = 0; $i < $jumlahSoal; $i++) {
				if ($i < count($soalPg)) {
					$allSoal[$i] = $soalPg[$i];
				} else {
					$allSoal[$i] = $soalIsian[$i - count($soalPg)];
				}
			}
		} else {
			if ($ujian->jenis == "Pilihan Ganda") {
				$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
				//$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nis,$id);
			} else {
				$allSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
				//$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nis,$id);
			}
			$jumlahSoal = count($allSoal);
		}
		for ($lk = 0; $lk < $jumlahSoal; $lk++) {
			$check = true;
			for ($lt = 0; $lt < $ctLk; $lt++) {
				if ($list_kd[$lt] == $allSoal[$lk]->kd) {
					$check = false;
					break;
				}
			}
			if ($check && $ctLk < 3) {
				$list_kd[$ctLk] = strval($allSoal[$lk]->kd);
				$ctLk += 1;
			}
		}
		$sh = 0;
		$spreadsheet = new Spreadsheet();
		for ($cta = 0; $cta < count($allSiswa); $cta += 1) {
			$nis = $allSiswa[$cta]->nik;
			$siswa = $this->M_user->getUserByNik($nis);
			$nilai = $this->M_nilai->getNilaiByNikAndIdUjian($nis, $id);
			$sheet1 = $spreadsheet->createSheet();
			$sheet1 = $spreadsheet->setActiveSheetIndex($sh);
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing->setName('logo');
			$drawing->setDescription('logo');
			$drawing->setPath('assets/img/logo.png'); // put your path and image here
			$drawing->setCoordinates('A1');
			$drawing->setOffsetX(10);
			$drawing->setOffsetY(10);
			$drawing->setHeight(110);
			$drawing->setWidth(110);
			$drawing->getShadow()->setVisible(true);
			$drawing->setWorksheet($sheet1);
			$sheet1->setTitle($allSiswa[$cta]->nik);
			$sheet1->getColumnDimension('I')->setWidth(15);
			$i = 1;
			$sheet1->mergeCells('C' . $i . ':I' . $i);
			$sheet1->setCellValue('C' . $i, $this->session->unit . ' TALENTA');
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('C' . $i . ':C' . ($i + 6))->applyFromArray($styleArrayLeft);
			$i += 1;
			$sheet1->mergeCells('C' . $i . ':I' . $i);
			$sheet1->setCellValue('C' . $i, 'TAMAN KOPO INDAH III, BLOK F1');
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->mergeCells('C' . $i . ':I' . $i);
			$sheet1->setCellValue('C' . $i, 'KAB. BANDUNG');
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$i += 4;
			$sheet1->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArrayTop2);
			$i += 1;
			$sheet1->mergeCells('C' . $i . ':F' . $i);
			$sheet1->setCellValue('C' . $i, $ujian->nama);
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$sheet1->setCellValue('G' . $i, 'NILAI');
			$sheet1->setCellValue('H' . $i, 'NILAI');
			$sheet1->setCellValue('I' . $i, 'NILAI');
			$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('G' . $i . ':G' . ($i + 1))->applyFromArray($styleArrayOut);
			$sheet1->getStyle('H' . $i . ':H' . ($i + 1))->applyFromArray($styleArrayOut);
			$sheet1->getStyle('I' . $i . ':I' . ($i + 1))->applyFromArray($styleArrayOut);
			$i += 1;
			$sheet1->mergeCells('C' . $i . ':F' . $i);
			$sheet1->setCellValue('C' . $i, 'KELAS: ' . $siswa->kelas);
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$cellVal = 7;
			for ($lk = 0; $lk < 3; $lk++) {
				if (isset($list_kd[$lk])) {
					$sheet1->setCellValueByColumnAndRow($cellVal, $i, strval($list_kd[$lk]));
				} else {
					$sheet1->setCellValueByColumnAndRow($cellVal, $i, 'KD');
				}
				$cellVal += 1;
			}
			// $sheet1->setCellValue('G'.$i, 'KD');
			// $sheet1->setCellValue('H'.$i, 'KD');
			// $sheet1->setCellValue('I'.$i, 'KD');
			$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->mergeCells('C' . $i . ':F' . $i);
			$sheet1->setCellValue('C' . $i, 'BAB ' . $ujian->bab);
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$sheet1->setCellValue('G' . $i, $nilai[0]->hasil);
			$sheet1->setCellValue('H' . $i, '-');
			$sheet1->setCellValue('I' . $i, '-');
			$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('G' . $i)->applyFromArray($styleArray);
			$sheet1->getStyle('H' . $i)->applyFromArray($styleArray);
			$sheet1->getStyle('I' . $i)->applyFromArray($styleArray);
			$i += 1;
			$sheet1->mergeCells('A' . $i . ':F' . $i);
			$sheet1->setCellValue('A' . $i, 'NAMA/KELAS/NO.ABSEN : ' . $siswa->nama . '/' . $siswa->kelas);
			$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
			$sheet1->setCellValue('G' . $i, '');
			$sheet1->setCellValue('H' . $i, '');
			$sheet1->setCellValue('I' . $i, '');
			$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('G' . $i . ':I' . $i)->applyFromArray($styleArrayOut);

			$i += 2;
			$sheet1->mergeCells('A' . $i . ':H' . $i);
			$sheet1->setCellValue('A' . $i, 'SOAL');
			$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('A' . $i)->applyFromArray($styleArrayOut);
			$sheet1->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('center');
			$sheet1->getStyle('A' . $i . ':H' . $i)->applyFromArray($styleArray);
			$i += 1;
			$j = $i;
			$tempI = $i;
			//$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
			//var_dump($allSoal);exit;
			if ($ujian->tipe == "Gabungan") {
				$jawabanSiswaPg = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nis, $id);
				$jawabanSiswaIsian = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nis, $id);
			} else {
				if ($ujian->jenis == "Pilihan Ganda") {
					$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nis, $id);
				} else {
					$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nis, $id);
				}
			}
			if ($ujian->tipe == "Gabungan") {
				for ($i; $i < count($soalPg) + $j; $i++) {
					$isJawab = false;
					foreach ($jawabanSiswaPg as $js) {
						if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
							$isJawab = true;
							$beginI = $tempI;
							$sheet1->setCellValue('A' . $tempI, $soalPg[$i - $j]->no_soal . '.');
							$sheet1->setCellValue('B' . $tempI, strip_tags($soalPg[$i - $j]->soal));
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'A. ' . $js->ja);
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($js->ja))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'B. ' . $js->jb);
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jb))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'C. ' . $js->jc);
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jc))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'D. ' . $js->jd);
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jd))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
							$sheet1->setCellValue('I' . $beginI, $js->pilihan_jawaban);
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
							$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
							if ($soalPg[$i - $j]->kunci_pg != $js->jawaban_asli) {
								$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
							}
						}
					}
					if (!$isJawab) {
						$beginI = $tempI;
						$sheet1->setCellValue('A' . $tempI, $soalPg[$i - $j]->no_soal . '.');
						$sheet1->setCellValue('B' . $tempI, $soalPg[$i - $j]->soal);
						$tempI += 1;
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->e))) {
							$sheet1->setCellValue('B' . $tempI, 'A. ' . $soalPg[$i - $j]->e);
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->e))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
						} else {
							$sheet1->setCellValue('B' . $tempI, 'A. ' . $soalPg[$i - $j]->a);
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->a))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'B. ' . $soalPg[$i - $j]->b);
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->b))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'C. ' . $soalPg[$i - $j]->c);
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->c))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'D. ' . $soalPg[$i - $j]->d);
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->d))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
						$sheet1->setCellValue('I' . $beginI, '-');
						$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
						$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
						$sheet1->getStyle('I' . $beginI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
						$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
					}
					$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
					$tempI += 1;
				}
				$i = $tempI;
				$j = $i;
				for ($i; $i < count($soalIsian) + $j; $i++) {
					$isJawab = false;
					foreach ($jawabanSiswaIsian as $js) {
						if ($js->id_soal == $soalIsian[$i - $j]->id_soal) {
							$isJawab = true;
							$beginI = $tempI;
							$sheet1->setCellValue('A' . $tempI, $soalIsian[$i - $j]->no_soal . '.');
							$sheet1->setCellValue('B' . $tempI, strip_tags($soalIsian[$i - $j]->soal));
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, strip_tags($js->jawaban));
							$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
							$tempI += 1;
						}
					}
				}
			} else {
				if ($ujian->jenis == "Pilihan Ganda") {
					for ($i; $i < count($allSoal) + $j; $i++) {
						$isJawab = false;
						foreach ($jawabanSiswa as $js) {
							if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
								$isJawab = true;
								$beginI = $tempI;
								$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
								$sheet1->setCellValue('B' . $tempI, ') ' . strip_tags($allSoal[$i - $j]->soal) . '');
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($js->ja));
								if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->ja)))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($js->jb));
								if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jb)))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($js->jc));
								if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jc)))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($js->jd));
								if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jd)))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
								$sheet1->setCellValue('I' . $beginI, $js->pilihan_jawaban);
								$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
								$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
								$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
								if ($allSoal[$i - $j]->kunci_pg != $js->jawaban_asli) {
									$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
								}
							}
						}
						if (!$isJawab) {
							$beginI = $tempI;
							$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
							//$sheet1->setCellValue('B'.$tempI, $allSoal[$i-$j]->soal);
							$tempI += 1;
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->e)))) {
								$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($allSoal[$i - $j]->e));
								if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->e)))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
							} else {
								$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($allSoal[$i - $j]->a));
								if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->a)))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($allSoal[$i - $j]->b));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->b)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($allSoal[$i - $j]->c));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->c)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'D. ' . $allSoal[$i - $j]->d);
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->d)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
							$sheet1->setCellValue('I' . $beginI, '-');
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
							$sheet1->getStyle('I' . $beginI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
							$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
						}
						$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
						$tempI += 1;
					}
				} else {
					for ($i; $i < count($allSoal) + $j; $i++) {
						$isJawab = false;
						foreach ($jawabanSiswa as $js) {
							if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
								$isJawab = true;
								$beginI = $tempI;
								$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
								$sheet1->setCellValue('B' . $tempI, strip_tags($allSoal[$i - $j]->soal));
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, strip_tags($js->jawaban));
								$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
								$tempI += 1;
							}
						}
					}
				}
			}
			$i = $tempI;
			$sh += 1;
		}
		$writer = new Xlsx($spreadsheet);
		$filename = "All Naskah Ujian-" . $ujian->nama;

		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
	public function createPdf($id, $nis)
	{
		$this->isAnyLogin();
		$ujian = $this->M_ujian->getUjianById($id);
		$siswa = $this->M_user->getUserByNik($nis);
		$nilai = $this->M_nilai->getNilaiByNikAndIdUjian($nis, $id);

		if ($ujian->tipe == "Gabungan") {
			$soalPg = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
			$soalIsian = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
			$jawabanSiswaPg = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nis, $id);
			$jawabanSiswaIsian = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nis, $id);
			$jumlahSoal = count($soalPg) + count($soalIsian);
			$allSoal = [];
			for ($i = 0; $i < $jumlahSoal; $i++) {
				if ($i < count($soalPg)) {
					$allSoal[$i] = $soalPg[$i];
				} else {
					$allSoal[$i] = $soalIsian[$i - count($soalPg)];
				}
			}
		} else {
			if ($ujian->jenis == "Pilihan Ganda") {
				$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
				$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nis, $id);
			} else {
				$allSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
				$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nis, $id);
			}
			$jumlahSoal = count($allSoal);
		}
		$list_kd = [];
		$ctLk = 0;
		$check = true;

		for ($lk = 0; $lk < $jumlahSoal; $lk++) {
			$check = true;
			for ($lt = 0; $lt < $ctLk; $lt++) {
				if ($list_kd[$lt] == $allSoal[$lk]->kd) {
					$check = false;
					break;
				}
			}
			if ($check && $ctLk < 3) {
				$list_kd[$ctLk] = strval($allSoal[$lk]->kd);
				$ctLk += 1;
			}
		}
		//var_dump($list_kd);exit;
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayTop = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayTop2 = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayLeft = [
			'borders' => [
				'left' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayOut = [
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$headingArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 14,
				'name'  => 'Arial',
			)
		);
		$contentArray = array(
			'font'  => array(
				'bold'  => false,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial',
			)
		);
		$leadArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 10,
				'name'  => 'Arial'
			)
		);
		$spreadsheet = new Spreadsheet();
		$sheet1 = $spreadsheet->getActiveSheet(0);
		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setName('logo');
		$drawing->setDescription('logo');
		$drawing->setPath('assets/img/logo.png'); // put your path and image here
		$drawing->setCoordinates('A1');
		$drawing->setOffsetX(10);
		$drawing->setOffsetY(10);
		$drawing->setHeight(110);
		$drawing->setWidth(110);
		$drawing->getShadow()->setVisible(true);
		$drawing->setWorksheet($sheet1);
		$sheet1->setTitle('Hasil Ujian');
		$sheet1->getColumnDimension('I')->setWidth(15);
		$i = 1;
		$sheet1->mergeCells('C' . $i . ':I' . $i);
		$sheet1->setCellValue('C' . $i, $this->session->unit . ' TALENTA');
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('C' . $i . ':C' . ($i + 6))->applyFromArray($styleArrayLeft);
		$i += 1;
		$sheet1->mergeCells('C' . $i . ':I' . $i);
		$sheet1->setCellValue('C' . $i, 'TAMAN KOPO INDAH III, BLOK F1');
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->mergeCells('C' . $i . ':I' . $i);
		$sheet1->setCellValue('C' . $i, 'KAB. BANDUNG');
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$i += 4;
		$sheet1->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArrayTop2);
		$i += 1;
		$sheet1->mergeCells('C' . $i . ':F' . $i);
		$sheet1->setCellValue('C' . $i, $ujian->nama);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->setCellValue('G' . $i, 'NILAI');
		$sheet1->setCellValue('H' . $i, 'NILAI');
		$sheet1->setCellValue('I' . $i, 'NILAI');
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('G' . $i . ':G' . ($i + 1))->applyFromArray($styleArrayOut);
		$sheet1->getStyle('H' . $i . ':H' . ($i + 1))->applyFromArray($styleArrayOut);
		$sheet1->getStyle('I' . $i . ':I' . ($i + 1))->applyFromArray($styleArrayOut);
		$i += 1;
		$sheet1->mergeCells('C' . $i . ':F' . $i);
		$sheet1->setCellValue('C' . $i, 'KELAS: ' . $siswa->kelas);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$cellVal = 7;
		for ($lk = 0; $lk < 3; $lk++) {
			if (isset($list_kd[$lk])) {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, strval($list_kd[$lk]));
			} else {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, 'KD');
			}
			$cellVal += 1;
		}
		// $sheet1->setCellValue('G'.$i, 'KD');
		// $sheet1->setCellValue('H'.$i, 'KD');
		// $sheet1->setCellValue('I'.$i, 'KD');
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->mergeCells('C' . $i . ':F' . $i);
		$sheet1->setCellValue('C' . $i, 'BAB ' . $ujian->bab);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->setCellValue('G' . $i, '-');
		$sheet1->setCellValue('H' . $i, '-');
		$sheet1->setCellValue('I' . $i, '-');
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('G' . $i)->applyFromArray($styleArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($styleArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($styleArray);
		$i += 1;
		$sheet1->mergeCells('A' . $i . ':F' . $i);
		$sheet1->setCellValue('A' . $i, 'NAMA/KELAS/NO.ABSEN : ' . $siswa->nama . '/' . $siswa->kelas);
		$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
		$sheet1->setCellValue('G' . $i, '');
		$sheet1->setCellValue('H' . $i, '');
		$sheet1->setCellValue('I' . $i, '');
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('G' . $i . ':I' . $i)->applyFromArray($styleArrayOut);
		$sheet1->setCellValue('G' . $i, $nilai[0]->hasil);


		$i += 2;
		$sheet1->mergeCells('A' . $i . ':H' . $i);
		$sheet1->setCellValue('A' . $i, 'SOAL');
		$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('A' . $i)->applyFromArray($styleArrayOut);
		$sheet1->getStyle('A' . $i . ':I' . $i)->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
		$sheet1->setCellValue('I' . $i, 'Nilai Per Soal');

		$i += 1;
		$j = $i;
		$tempI = $i;
		//$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		//var_dump($allSoal);exit;
		if ($ujian->tipe == "Gabungan") {
			for ($i; $i < count($soalPg) + $j; $i++) {
				$isJawab = false;
				foreach ($jawabanSiswaPg as $js) {
					if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
						$isJawab = true;
						$beginI = $tempI;
						$sheet1->setCellValue('A' . $tempI, $soalPg[$i - $j]->no_soal . '.');
						$sheet1->getDefaultRowDimension(1)->setRowHeight(-1);
						$sheet1->getRowDimension(1)->setRowHeight(-1);
						$sheet1->getStyle('B'. $tempI)->getAlignment()->setWrapText(true);
						$sheet1->getStyle('B'. $tempI)->getAlignment()->setVertical('top');
						$sheet1->setCellValue('B' . $tempI, strip_tags($soalPg[$i - $j]->soal));
						$sheet1->mergeCells('B'.$tempI.':H'.$tempI);
						// if(isset($soalPg[$i - $j]->gambarSoal)){
						// 	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
						// 	$drawing->setName('logo');
						// 	$drawing->setDescription('logo');
						// 	$drawing->setPath('assets/img/'.$soalPg[$i - $j]->gambarSoal); // put your path and image here
						// 	$drawing->setCoordinates('J'. $tempI);
						// 	$drawing->setOffsetX(10);
						// 	$drawing->setOffsetY(10);
						// 	$drawing->setHeight(110);
						// 	$drawing->setWidth(110);
						// 	$drawing->getShadow()->setVisible(true);
						// 	$drawing->setWorksheet($sheet1);
						// }
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($js->ja));
						if (trim(strtolower(strip_tags($soalPg[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->ja)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($js->jb));
						if (trim(strtolower(strip_tags($soalPg[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jb)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($js->jc));
						if (trim(strtolower(strip_tags($soalPg[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jc)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($js->jd));
						if (trim(strtolower(strip_tags($soalPg[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jd)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
						$sheet1->setCellValue('I' . $beginI, $js->pilihan_jawaban);
						$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
						$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
						$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
						if ($soalPg[$i - $j]->kunci_pg != $js->jawaban_asli) {
							$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
						}
					}
				}
				if (!$isJawab) {
					$beginI = $tempI;
					$sheet1->setCellValue('A' . $tempI, $soalPg[$i - $j]->no_soal . '.');
					$sheet1->getDefaultRowDimension(1)->setRowHeight(-1);
					$sheet1->getRowDimension(1)->setRowHeight(-1);
					$sheet1->getStyle('B'. $tempI)->getAlignment()->setWrapText(true);
					$sheet1->getStyle('B'. $tempI)->getAlignment()->setVertical('top');
					$sheet1->setCellValue('B' . $tempI, strip_tags($soalPg[$i - $j]->soal));
					$sheet1->mergeCells('B'.$tempI.':H'.$tempI);
					if(isset($soalPg[$i - $j]->gambarSoal)){
						$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
						$drawing->setName('logo');
						$drawing->setDescription('logo');
						$drawing->setPath('assets/img/'.$soalPg[$i - $j]->gambarSoal); // put your path and image here
						$drawing->setCoordinates('J'. $tempI);
						$drawing->setOffsetX(10);
						$drawing->setOffsetY(10);
						$drawing->setHeight(110);
						$drawing->setWidth(110);
						$drawing->getShadow()->setVisible(true);
						$drawing->setWorksheet($sheet1);
					}
					$tempI += 1;
					if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->e))) {
						$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($soalPg[$i - $j]->e));
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->e))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
					} else {
						$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($soalPg[$i - $j]->a));
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->a))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
					}
					$tempI += 1;
					$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($soalPg[$i - $j]->b));
					if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->b))) {
						$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
					}
					$tempI += 1;
					$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($soalPg[$i - $j]->c));
					if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->c))) {
						$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
					}
					$tempI += 1;
					$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($soalPg[$i - $j]->d));
					if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) == trim(strtolower($soalPg[$i - $j]->d))) {
						$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
					}
					$tempI += 1;
					$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
					$sheet1->setCellValue('I' . $beginI, '-');
					$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
					$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
					$sheet1->getStyle('I' . $beginI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
					$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
				}
				$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
				$tempI += 1;
			}
			$i = $tempI;
			$j = $i;
			for ($i; $i < count($soalIsian) + $j; $i++) {
				$isJawab = false;
				foreach ($jawabanSiswaIsian as $js) {
					if ($js->id_soal == $soalIsian[$i - $j]->id_soal) {
						$isJawab = true;
						$beginI = $tempI;
						$sheet1->setCellValue('A' . $tempI, $soalIsian[$i - $j]->no_soal . '.');
						$sheet1->setCellValue('B' . $tempI, strip_tags($soalIsian[$i - $j]->soal));
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, strip_tags($js->jawaban));
						$sheet1->getStyle('A' . $beginI . ':I' . $tempI)->applyFromArray($styleArrayOut);
						$sheet1->setCellValue('I' . $tempI, $js->nilai_point);
						$tempI += 1;
					}
				}
			}
		} else {
			if ($ujian->jenis == "Pilihan Ganda") {
				for ($i; $i < count($allSoal) + $j; $i++) {
					$isJawab = false;
					foreach ($jawabanSiswa as $js) {
						if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
							$isJawab = true;
							$beginI = $tempI;
							$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
							$sheet1->setCellValue('B' . $tempI, ') ' . strip_tags($allSoal[$i - $j]->soal) . '');
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($js->ja));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->ja)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($js->jb));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jb)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($js->jc));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jc)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($js->jd));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($js->jd)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
							$sheet1->setCellValue('I' . $beginI, $js->pilihan_jawaban);
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
							$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
							if ($allSoal[$i - $j]->kunci_pg != $js->jawaban_asli) {
								$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
							}
						}
					}
					if (!$isJawab) {
						$beginI = $tempI;
						$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
						$sheet1->setCellValue('B'.$tempI,strip_tags( $allSoal[$i-$j]->soal));
						$tempI += 1;
						if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->e)))) {
							$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($allSoal[$i - $j]->e));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->e)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
						} else {
							$sheet1->setCellValue('B' . $tempI, 'A. ' . strip_tags($allSoal[$i - $j]->a));
							if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->a)))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($allSoal[$i - $j]->b));
						if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->b)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($allSoal[$i - $j]->c));
						if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->c)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($allSoal[$i - $j]->d));
						if (trim(strtolower(strip_tags($allSoal[$i - $j]->kunci_jawaban))) == trim(strtolower(strip_tags($allSoal[$i - $j]->d)))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
						$sheet1->setCellValue('I' . $beginI, '-');
						$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
						$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
						$sheet1->getStyle('I' . $beginI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
						$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
					}
					$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
					$tempI += 1;
				}
			} else {
				for ($i; $i < count($allSoal) + $j; $i++) {
					$isJawab = false;
					foreach ($jawabanSiswa as $js) {
						if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
							$isJawab = true;
							$beginI = $tempI;
							$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
							$sheet1->setCellValue('B' . $tempI, strip_tags($allSoal[$i - $j]->soal));
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, strip_tags($js->jawaban));
							$sheet1->getStyle('A' . $beginI . ':I' . $tempI)->applyFromArray($styleArrayOut);
							$sheet1->setCellValue('I' . $tempI, $js->nilai_point);
							$tempI += 1;
						}
					}
				}
			}
		}

		//var_dump($allSoal);exit;
		$i = $tempI;
		$writer = new Xlsx($spreadsheet);
		$filename = "Naskah-KELAS " . strtoupper($siswa->kelas) . "_" . strtoupper($ujian->nama) . "_B" . strtoupper($ujian->bab) . "-" . strtoupper($siswa->kelas) . "-" . strtoupper($siswa->nama);

		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
	public function excelReport($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['kelas'] = $this->M_kelas->getKelasByUnit($this->session->unit);
		$this->load->view('guru/excelReport', $data);
	}
	public function excelReport2($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['kelas'] = $this->M_kelas->getKelasByUnit($this->session->unit);
		$this->load->view('guru/excelReport2', $data);
	}
	public function excelReport3($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['kelas'] = $this->M_kelas->getKelasByUnit($this->session->unit);
		$this->load->view('guru/excelReport3', $data);
	}

	public function downloadExcelPerKelas()
	{
		$this->isAnyLogin();
		$kelas = $this->input->post('kelas');
		$id = $this->input->post('idUjian');
		$ujian = $this->M_ujian->getUjianById($id);
		$ujiangabunganpg = $this->M_ujian_gabungan_has_soal->getUjianPGByIdUjian($id);
		$ujiangabunganisian = $this->M_ujian_gabungan_has_soal->getUjianIsianByIdUjian($id);
		$nilai = $this->M_nilai->getNilaiByIdUjianAndKelas($id, $kelas);
		$namaUjian = $ujian->nama;
		$tgl = date('d-M');
		$tgl2 = date('d-M-Y');

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];

		$styleArrayOut = [
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$headingArray = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => '000000'),
				'size' => 14,
				'name' => 'Arial',
			)
		);
		$contentArray = array(
			'font' => array(
				'bold' => false,
				'color' => array('rgb' => '000000'),
				'size' => 9,
				'name' => 'Arial',
			)
		);
		$leadArray = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => '000000'),
				'size' => 9,
				'name' => 'Arial'
			)
		);
		$tempJumlah = 0;
		$tempBanyakSiswa = 0;
		$countSiswaRemed = 0;
		$spreadsheet = new Spreadsheet();
		$sheet1 = $spreadsheet->getActiveSheet(0);
		$sheet1->setTitle("NILAI ULANGAN HARIAN PER KELAS");
		$sheet1->getColumnDimension('C')->setWidth(3);
		$sheet1->getColumnDimension('D')->setWidth(30);
		$sheet1->getColumnDimension('E')->setWidth(41);
		$sheet1->getColumnDimension('F')->setWidth(20);
		$sheet1->mergeCells('A1:E1');
		$sheet1->setCellValue('A1', 'PENILAIAN ' . $namaUjian . ' KELAS ' . $kelas);
		$sheet1->getStyle('A1')->applyFromArray($headingArray);
		$sheet1->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
		$sheet1->setCellValue('A3', 'Mata Pelajaran');
		$sheet1->setCellValue('C3', ':');
		$sheet1->setCellValue('D3', $ujian->mata_pelajaran);
		$sheet1->setCellValue('F3', 'Tahun Pelajaran');
		$sheet1->setCellValue('G3', ': ' . $ujian->tahun_ajaran);
		$sheet1->getStyle('A3')->applyFromArray($leadArray);
		$sheet1->getStyle('F3')->applyFromArray($leadArray);
		$sheet1->setCellValue('A4', 'Guru Pengampu');
		$sheet1->setCellValue('C4', ':');
		$sheet1->setCellValue('D4', $this->session->nama);
		$sheet1->setCellValue('F4', 'Tingkat - Kelas');
		$sheet1->setCellValue('G4', ': ' . $this->session->unit . ' kelas ' . $kelas);
		$sheet1->getStyle('A4')->applyFromArray($leadArray);
		$sheet1->getStyle('F4')->applyFromArray($leadArray);
		$sheet1->setCellValue('F5', 'KKM');
		$sheet1->setCellValue('G5', ': ' . $ujian->kkm);
		$sheet1->getStyle('F5')->applyFromArray($leadArray);
		$sheet1->mergeCells('A6:A8');
		$sheet1->mergeCells('B6:B8');
		$sheet1->mergeCells('C6:D8');
		$sheet1->mergeCells('F6:F8');
		$sheet1->mergeCells('G6:G8');
		$sheet1->setCellValue('A6', 'No');
		$sheet1->setCellValue('B6', 'No Induk');
		$sheet1->setCellValue('C6', 'Nama Siswa');
		$sheet1->setCellValue('E6', 'KOMPONEN NILAI KD / TANGGAL PENILAIAN');
		$sheet1->setCellValue('F6', 'KETUNTASAN');
		$sheet1->setCellValue('G6', 'KET.');
		$sheet1->setCellValue('E7', 'KD 1');
		$sheet1->setCellValue('E8', $tgl);
		$sheet1->getStyle('A6')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('A6:A8')->getAlignment()->setVertical('center');
		$sheet1->getStyle('B6')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('B6:B8')->getAlignment()->setVertical('center');
		$sheet1->getStyle('C6')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('C6:D8')->getAlignment()->setVertical('center');
		$sheet1->getStyle('F6')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('F6:F8')->getAlignment()->setVertical('center');
		$sheet1->getStyle('G6')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('G6:G8')->getAlignment()->setVertical('center');
		$sheet1->getStyle('A6:A8')->applyFromArray($styleArrayOut);
		$sheet1->getStyle('B6:B8')->applyFromArray($styleArrayOut);
		$sheet1->getStyle('C6:D8')->applyFromArray($styleArrayOut);
		$sheet1->getStyle('E6')->applyFromArray($styleArray);
		$sheet1->getStyle('E7')->applyFromArray($styleArray);
		$sheet1->getStyle('E8')->applyFromArray($styleArray);
		$sheet1->getStyle('F6:F8')->applyFromArray($styleArrayOut);
		$sheet1->getStyle('G6:G8')->applyFromArray($styleArrayOut);
		$sheet1->getStyle('G6:G8')->applyFromArray($styleArrayOut);
		$i = 9;
		$tuntas = "LULUS";
		for ($a = 9; $a < 45; $a++) {
			$sheet1->getStyle('A' . $a . ':B' . $a)->applyFromArray($styleArray);
			$sheet1->getStyle('C' . $a . ':D' . $a)->applyFromArray($styleArrayOut);
			$sheet1->getStyle('E' . $a . ':G' . $a)->applyFromArray($styleArray);
		}
		for ($a = 45; $a < 49; $a++) {
			$sheet1->getStyle('A' . $a . ':D' . $a)->applyFromArray($styleArrayOut);
			$sheet1->getStyle('E' . $a . ':G' . $a)->applyFromArray($styleArray);
		}
		for ($i; $i < count($nilai) + 9; $i++) {
			$sheet1->mergeCells('C' . $i . ':D' . $i);
			$sheet1->setCellValue('A' . $i, $i - 8);
			$sheet1->setCellValue('B' . $i, $nilai[$i - 9]->nik);
			$sheet1->setCellValue('C' . $i, $nilai[$i - 9]->nama);
			$sheet1->setCellValue('E' . $i, $nilai[$i - 9]->hasil);
			if ($nilai[$i - 9]->hasil >= $ujian->kkm) {
				$tuntas = "LULUS";
			} else {
				$tuntas = "REMEDIAL";
				$countSiswaRemed += 1;
			}
			$sheet1->setCellValue('F' . $i, $tuntas);
			$tempJumlah += $nilai[$i - 9]->hasil;
			$tempBanyakSiswa += 1;
		}
		for ($i; $i < 36 + 9; $i++) {
			$sheet1->mergeCells('C' . $i . ':D' . $i);
			$sheet1->setCellValue('A' . $i, $i - 8);
			$sheet1->setCellValue('B' . $i, '');
			$sheet1->setCellValue('D' . $i, '');
			$sheet1->setCellValue('F' . $i, '');
		}
		$dayaserap = (($tempBanyakSiswa - $countSiswaRemed) / $tempBanyakSiswa) * 100;
		$sheet1->mergeCells('A' . $i . ':D' . $i);
		$sheet1->setCellValue('A' . $i, 'JUMLAH NILAI');
		$sheet1->getStyle('A' . $i . ':D' . $i)->getAlignment()->setHorizontal('center');
		$sheet1->setCellValue('E' . $i, $tempJumlah);
		$i += 1;
		$sheet1->mergeCells('A' . $i . ':D' . $i);
		$sheet1->setCellValue('A' . $i, 'NILAI RATA-RATA KELAS');
		$sheet1->getStyle('A' . $i . ':D' . $i)->getAlignment()->setHorizontal('center');
		$sheet1->setCellValue('E' . $i, $tempJumlah / $tempBanyakSiswa);
		$i += 1;
		$sheet1->mergeCells('A' . $i . ':D' . $i);
		$sheet1->setCellValue('A' . $i, 'DAYA SERAP');
		$sheet1->getStyle('A' . $i . ':D' . $i)->getAlignment()->setHorizontal('center');
		$sheet1->setCellValue('E' . $i, $dayaserap);
		$i += 1;
		$sheet1->mergeCells('A' . $i . ':D' . $i);
		$sheet1->setCellValue('A' . $i, '<KKM');
		$sheet1->getStyle('A' . $i . ':D' . $i)->getAlignment()->setHorizontal('center');
		$sheet1->setCellValue('E' . $i, $countSiswaRemed);
		$i += 2;
		$sheet1->setCellValue('D' . $i, 'Mengetahui,');
		$sheet1->setCellValue('F' . $i, 'Bandung,' . $tgl2);
		$i += 1;
		$sheet1->setCellValue('D' . $i, 'Kepala Sekolah');
		$sheet1->setCellValue('F' . $i, 'Guru Mata Pelajaran,');
		$i += 3;
		$sheet1->setCellValue('D' . $i, 'Yosep Yaya K, S.Pd');
		$sheet1->setCellValue('F' . $i, $this->session->nama);
		$i += 1;
		$sheet1->setCellValue('D' . $i, 'NIP.' . $this->session->nik);
		$sheet1->setCellValue('F' . $i, 'NIP.' . $this->session->nik);

		//start sheet 2 //
		$sheet2 = $spreadsheet->createSheet();
		$sheet2 = $spreadsheet->setActiveSheetIndex(1);
		$sheet2->setTitle('REKAP ABSEN PER KELAS SEMUA');
		$sheet2->getColumnDimension('D')->setWidth(3);
		$sheet2->getColumnDimension('E')->setWidth(50);
		$sheet2->getColumnDimension('F')->setWidth(30);
		$sheet2->mergeCells('B1:F1');
		$sheet2->setCellValue('B1', 'REKAP KEHADIRAN ULANGAN');
		$sheet2->getStyle('A1')->applyFromArray($headingArray);
		$sheet2->mergeCells('B3:C3');
		$sheet2->setCellValue('B3', 'Mata Pelajaran');
		$sheet2->setCellValue('D3', ':');
		$sheet2->setCellValue('E3', $ujian->mata_pelajaran);
		$sheet2->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
		$sheet2->getStyle('B3')->applyFromArray($leadArray);
		$sheet2->mergeCells('B4:C4');
		$sheet2->setCellValue('B4', 'Guru Pengampu');
		$sheet2->setCellValue('D4', ':');
		$sheet2->setCellValue('E4', $this->session->nama);
		$sheet2->getStyle('B4')->applyFromArray($leadArray);
		$sheet2->mergeCells('B5:C5');
		$sheet2->setCellValue('B5', 'Kelas');
		$sheet2->setCellValue('D5', ':');
		$sheet2->setCellValue('E5', $kelas);
		$sheet2->getStyle('B5')->applyFromArray($leadArray);
		$sheet2->setCellValue('B6', 'KD');
		$sheet2->setCellValue('C6', '');
		$sheet2->setCellValue('D6', ':');
		$sheet2->setCellValue('E6', '');
		$sheet2->getStyle('B6')->applyFromArray($leadArray);
		$sheet2->mergeCells('B7:C7');
		$sheet2->setCellValue('B7', 'Guru Pengampu');
		$sheet2->setCellValue('D7', ':');
		$sheet2->setCellValue('E7', $this->session->nama);
		$sheet2->getStyle('B7')->applyFromArray($leadArray);
		$sheet2->mergeCells('D9:E9');
		$sheet2->setCellValue('B9', 'No');
		$sheet2->setCellValue('C9', 'No Induk');
		$sheet2->setCellValue('D9', 'Nama Siswa');
		$sheet2->setCellValue('F9', 'Kehadiran');
		$sheet2->getStyle('D9:E9')->getAlignment()->setHorizontal('center');
		$sheet2->getStyle('B9:F9')->applyFromArray($contentArray);
		$i = 10;
		$j = $i;
		$jmlSiswa = 0;
		for ($a = 9; $a < 46; $a++) {
			$sheet2->getStyle('B' . $a . ':C' . $a)->applyFromArray($styleArray);
			$sheet2->getStyle('D' . $a . ':E' . $a)->applyFromArray($styleArrayOut);
			$sheet2->getStyle('F' . $a)->applyFromArray($styleArray);
		}
		$sheet2->getStyle('B46:E46')->applyFromArray($styleArrayOut);
		$sheet2->getStyle('F46')->applyFromArray($styleArray);

		for ($i; $i < count($nilai) + $j; $i++) {
			$sheet2->mergeCells('D' . $i . ':E' . $i);
			$sheet2->setCellValue('B' . $i, $i - $j + 1);
			$sheet2->setCellValue('C' . $i, $nilai[$i - $j]->nik);
			$sheet2->setCellValue('D' . $i, $nilai[$i - $j]->nama);
			$jmlSiswa += 1;
		}
		for ($i; $i < 36 + $j; $i++) {
			$sheet2->mergeCells('D' . $i . ':E' . $i);
			$sheet2->setCellValue('B' . $i, $i - $j + 1);
		}
		$sheet2->mergeCells('B46:E46');
		$sheet2->setCellValue('B46', 'JUMLAH SISWA');
		$sheet2->setCellValue('F46', $jmlSiswa);
		$sheet2->getStyle('B46')->getAlignment()->setHorizontal('right');
		$sheet2->getStyle('B9:F9')->applyFromArray($leadArray);
		$sheet2->setCellValue('E48', 'Mengetahui');
		$sheet2->setCellValue('F48', 'Bandung, ' . $tgl2);
		$sheet2->setCellValue('E49', 'Kepala Sekolah');
		$sheet2->setCellValue('F49', 'Guru Mata Pelajaran,');
		$sheet2->setCellValue('E52', 'Yosep Yaya K, S.Pd');
		$sheet2->setCellValue('F52', $this->session->nama);
		$sheet2->setCellValue('E53', 'NIP. 10413');
		$sheet2->setCellValue('F53', 'NIP.' . $this->session->nik);
		// end sheet 2 //
		// start sheet 3//
		$tempJumlah = 0;
		$tempBanyakSiswa = 0;
		$countSiswaRemed = 0;
		$sheet3 = $spreadsheet->createSheet();
		$sheet3 = $spreadsheet->setActiveSheetIndex(2);
		$sheet3->setTitle("DAFTAR NILAI UJIAN");
		$i = 1;
		$sheet3->getColumnDimension('A')->setWidth(12);
		$sheet3->getColumnDimension('B')->setWidth(30);
		$sheet3->getColumnDimension('C')->setWidth(4);
		$sheet3->getColumnDimension('D')->setWidth(20);
		$sheet3->getColumnDimension('E')->setWidth(16);
		$sheet3->getColumnDimension('F')->setWidth(10);
		$sheet3->getColumnDimension('G')->setWidth(10);
		$sheet3->getColumnDimension('H')->setWidth(10);
		$sheet3->getColumnDimension('I')->setWidth(10);
		$sheet3->getColumnDimension('J')->setWidth(10);
		$sheet3->mergeCells('A' . $i . ':J' . $i);
		$sheet3->setCellValue('A' . $i, 'DAFTAR NILAI UJIAN');
		$sheet3->getStyle('A' . $i)->applyFromArray($headingArray);
		$sheet3->getStyle('A' . $i . ':J' . $i)->getAlignment()->setHorizontal('center');
		//		$cellVal = 1;
		//		$sheet3->getCellByColumnAndRow($cellVal,$i)->getStyle()->applyFromArray($styleArray);
		$i += 2;
		$sheet3->setCellValue('B' . $i, 'NAMA SEKOLAH');
		$sheet3->setCellValue('C' . $i, ':');
		$sheet3->setCellValue('D' . $i, $this->session->unit . ' TALENTA');
		$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet3->setCellValue('B' . $i, 'NAMA TEST');
		$sheet3->setCellValue('C' . $i, ':');
		$sheet3->setCellValue('D' . $i, $ujian->nama);
		$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet3->setCellValue('B' . $i, 'MATA PELAJARAN');
		$sheet3->setCellValue('C' . $i, ':');
		$sheet3->setCellValue('D' . $i, $ujian->mata_pelajaran);
		$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet3->setCellValue('B' . $i, 'KELAS');
		$sheet3->setCellValue('C' . $i, ':');
		$sheet3->setCellValue('D' . $i, $kelas);
		$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet3->setCellValue('B' . $i, 'TANGGAL TES');
		$sheet3->setCellValue('C' . $i, ':');
		$sheet3->setCellValue('D' . $i, $tgl2);
		$sheet3->setCellValue('J' . $i, 'KKM');
		$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('J' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet3->setCellValue('B' . $i, 'MATERI_POKOK');
		$sheet3->setCellValue('C' . $i, ':');
		$sheet3->setCellValue('D' . $i, $ujian->materi_pokok);
		$sheet3->setCellValue('J' . $i, $ujian->kkm);
		$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 2;
		$sheet3->mergeCells('A' . $i . ':A' . ($i + 1));
		$sheet3->mergeCells('B' . $i . ':B' . ($i + 1));
		$sheet3->mergeCells('C' . $i . ':C' . ($i + 1));
		$sheet3->mergeCells('H' . $i . ':H' . ($i + 1));
		$sheet3->mergeCells('I' . $i . ':I' . ($i + 1));
		$sheet3->mergeCells('J' . $i . ':J' . ($i + 1));
		$sheet3->setCellValue('A' . $i, 'No. Urut');
		$sheet3->setCellValue('B' . $i, 'NAMA/KODE PESERTA');
		$sheet3->setCellValue('C' . $i, 'L/P');
		$sheet3->getStyle('A10:A11')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('A10:A11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('A10:A11')->getAlignment()->setVertical('center');
		$sheet3->getStyle('B10:B11')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('B10:B11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('B10:B11')->getAlignment()->setVertical('center');
		$sheet3->getStyle('C10:C11')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('C10:C11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('C10:C11')->getAlignment()->setVertical('center');
		$sheet3->getStyle('D10:E10')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('D10:E10')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('D11')->applyFromArray($styleArray);
		$sheet3->getStyle('D11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('E11')->applyFromArray($styleArray);
		$sheet3->getStyle('E11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('F10:G10')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('F10:G10')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('F11')->applyFromArray($styleArray);
		$sheet3->getStyle('F11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('G11')->applyFromArray($styleArray);
		$sheet3->getStyle('G11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('H10:H11')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('H10:H11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('H10:H11')->getAlignment()->setVertical('center');
		$sheet3->getStyle('I10:I11')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('I10:I11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('I10:I11')->getAlignment()->setVertical('center');
		$sheet3->getStyle('J10:J11')->applyFromArray($styleArrayOut);
		$sheet3->getStyle('J10:J11')->getAlignment()->setHorizontal('center');
		$sheet3->getStyle('J10:J11')->getAlignment()->setVertical('center');
		if ($ujian->tipe == "Gabungan") {
			$sheet3->mergeCells('D' . $i . ':E' . $i);
			$sheet3->setCellValue('D' . $i, 'URAIAN JAWABAN SISWA DAN HASIL PEMERIKSAAN');
			$sheet3->getColumnDimension('D')->setWidth(31);
			$sheet3->mergeCells('F' . $i . ':G' . $i);
			$sheet3->setCellValue('F' . $i, 'JUMLAH');
			$sheet3->setCellValue('H' . $i, 'SKOR');
			$sheet3->setCellValue('I' . $i, 'NILAI');
			$sheet3->setCellValue('J' . $i, 'CATATAN');
			$sheet3->getStyle('A' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('C' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('D' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('D' . $i)->getAlignment()->setWrapText(true);
			$sheet3->getStyle('E' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('F' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('G' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('H' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('J' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet3->setCellValue('D' . $i, 'PG');
			$sheet3->setCellValue('E' . $i, 'ISIAN');
			$sheet3->setCellValue('F' . $i, 'BENAR');
			$sheet3->setCellValue('G' . $i, 'SALAH');
			$sheet3->getStyle('D' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('E' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('F' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('G' . $i)->applyFromArray($leadArray);
		} else if ($ujian->tipe == "Tunggal") {
			$sheet3->mergeCells('D' . $i . ':E' . ($i + 1));
			$sheet3->setCellValue('D' . $i, 'URAIAN JAWABAN SISWA DAN HASIL PEMERIKSAAN');
			$sheet3->getColumnDimension('D')->setWidth(31);
			$sheet3->mergeCells('F' . $i . ':G' . $i);
			$sheet3->setCellValue('F' . $i, 'JUMLAH');
			$sheet3->setCellValue('H' . $i, 'SKOR');
			$sheet3->setCellValue('I' . $i, 'NILAI');
			$sheet3->setCellValue('J' . $i, 'CATATAN');
			$sheet3->getStyle('A' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('B' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('C' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('D' . $i)->applyFromArray($leadArray);
			//			$sheet3->getStyle('D' . $i)->getAlignment()->setWrapText(true);
			$sheet3->getStyle('E' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('F' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('G' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('H' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('J' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet3->setCellValue('F' . $i, 'BENAR');
			$sheet3->setCellValue('G' . $i, 'SALAH');
			$sheet3->getStyle('F' . $i)->applyFromArray($leadArray);
			$sheet3->getStyle('G' . $i)->applyFromArray($leadArray);
		}
		$i += 1;
		$j = $i;
		$tuntas = "LULUS";
		$jumlah = 0;
		$terkecil = 100;
		$terbesar = 0;
		$count = 0;
		$countremed = 0;
		for ($i; $i < count($nilai) + $j; $i++) {
			//start logic//
			$resultD = "";
			$resultE = "";
			$jmlBenar = 0;
			$jmlSalah = 0;
			if ($ujian->tipe == "Gabungan") {
				$allSoal = $ujiangabunganpg;
				$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik($nilai[$i - $j]->nik, $id);
				for ($z = 0; $z < count($allSoal); $z++) {
					for ($js = 0; $js < count($jawabanSiswa); $js++) {
						if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
							if ($jawabanSiswa[$js]->jawaban_asli == $allSoal[$z]->kunci_pg) {
								$resultD = $resultD . "1";
								$jmlBenar += 1;
							} else {
								$resultD = $resultD . "0";
								$jmlSalah += 1;
							}
						}
					}
				}
				$allSoal = $ujiangabunganisian;
				$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nilai[$i - $j]->nik, $id);
				for ($z = 0; $z < count($allSoal); $z++) {
					for ($js = 0; $js < count($jawabanSiswa); $js++) {
						if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
							if (trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban1) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban2) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban3) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban4) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban5)) {
								$resultE = $resultE . "1";
								$jmlBenar += 1;
							} else {
								$resultE = $resultE . "0";
								$jmlSalah += 1;
							}
						}
					}
				}
			} else {
				if ($ujian->jenis == "Pilihan Ganda") {
					$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
					$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik($nilai[$i - $j]->nik, $id);
					for ($z = 0; $z < count($allSoal); $z++) {
						$answered = false;
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								if ($jawabanSiswa[$js]->jawaban_asli == $allSoal[$z]->kunci_pg) {
									$resultD = $resultD . "1";
									$jmlBenar += 1;
								} else {
									$resultD = $resultD . "0";
									$jmlSalah += 1;
								}
								$answered = true;
							}
						}
						if (!$answered) {
							$resultD = $resultD . "0";
							$jmlSalah += 1;
						}
					}
				} else {
					$allSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
					//					var_dump($allSoal);exit;
					$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nilai[$i - $j]->nik, $id);
					//					var_dump($jawabanSiswa);exit; ini ada
					for ($z = 0; $z < count($allSoal); $z++) {
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								if (trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) == trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban1))) || trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) == trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban2))) || trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) == trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban3))) || trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) == trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban4))) || trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) == trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban5)))) {
									$resultD = $resultD . "1";
									$jmlBenar += 1;
								} else {
									$resultD = $resultD . "0";
									$jmlSalah += 1;
								}
							}
						}
					}
					//					var_dump($resultD);exit;
				}
			}
			//end logic//
			for ($a = 12; $a < 48; $a++) {
				$sheet3->getStyle('A' . $a . ':J' . $a)->applyFromArray($styleArray);
			}
			for ($a = 48; $a < 55; $a++) {
				$sheet3->getStyle('A' . $a . ':H' . $a)->applyFromArray($styleArrayOut);
				$sheet3->getStyle('I' . $a)->applyFromArray($styleArray);
				$sheet3->getStyle('J' . $a)->applyFromArray($styleArray);
			}
			$sheet3->setCellValue('A' . $i, $i - $j + 1);
			$sheet3->setCellValue('B' . $i, $nilai[$i - $j]->nama);
			if ($ujian->tipe == "Gabungan") {
				$sheet3->setCellValue('D' . $i, $resultD);
				$sheet3->setCellValue('E' . $i, $resultE);
			} else {
				$sheet3->mergeCells('D' . $i . ':E' . $i);
				$sheet3->setCellValue('D' . $i, $resultD);
			}
			$sheet3->setCellValue('F' . $i, $jmlBenar);
			$sheet3->setCellValue('G' . $i, $jmlSalah);
			$sheet3->setCellValue('H' . $i, $nilai[$i - $j]->hasil);
			$sheet3->setCellValue('I' . $i, $nilai[$i - $j]->hasil);
			if ($nilai[$i - $j]->hasil >= $ujian->kkm) {
				$tuntas = "LULUS";
			} else {
				$tuntas = "REMEDIAL";
				$countSiswaRemed += 1;
			}
			$sheet3->setCellValue('J' . $i, $tuntas);
			$tempJumlah += $nilai[$i - $j]->hasil;
			$tempBanyakSiswa += 1;
			$jumlah += $nilai[$i - $j]->hasil;
			if ($nilai[$i - $j]->hasil <= $terkecil) {
				$terkecil = $nilai[$i - $j]->hasil;
			}
			if ($nilai[$i - $j]->hasil >= $terbesar) {
				$terbesar = $nilai[$i - $j]->hasil;
			}
			$count += 1;
			if ($nilai[$i - $j]->hasil < $ujian->kkm) {
				$countremed += 1;
			}
		}
		$rata = $jumlah / $count;
		$dayaserap = (($count - $countremed) / $count) * 100;
		for (
			$i;
			$i < 36 + $j;
			$i++
		) {
			$sheet3->setCellValue('A' . $i, $i - $j + 1);
		}

		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, 'JUMLAH :');
		$sheet3->setCellValue('I' . $i, $jumlah);
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
		$i++;
		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, 'TERKECIL :');
		$sheet3->setCellValue('I' . $i, $terkecil);
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
		$i++;
		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, 'TERBESAR :');
		$sheet3->setCellValue('I' . $i, $terbesar);
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
		$i++;
		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, 'RATA-RATA :');
		$sheet3->setCellValue('I' . $i, $rata);
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
		$i++;
		$arr = $this->M_nilai->getNilaiByIdUjian($id);
		//		var_dump($this->standarDeviasi($arr));exit;
		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, 'SIMPANGAN BAKU :');
		$sheet3->setCellValue('I' . $i, $this->standarDeviasi($arr));
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
		$i++;
		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, 'DAYA SERAP :');
		$sheet3->setCellValue('I' . $i, $dayaserap . '%');
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);
		$i++;
		$sheet3->mergeCells('A' . $i . ':H' . $i);
		$sheet3->setCellValue('A' . $i, '<KKM :');
		$sheet3->setCellValue('I' . $i, $countremed);
		$sheet3->getStyle('A' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
		$sheet3->getStyle('A' . $i . ':H' . $i)->applyFromArray($leadArray);
		$sheet3->getStyle('I' . $i)->applyFromArray($leadArray);

		// $sheet3->mergeCells('A'.$i.':D'.$i);
		// $sheet3->setCellValue('A'.$i , 'JUMLAH NILAI');
		// $sheet3->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal('center');
		// $sheet3->setCellValue('E'.$i , $tempJumlah);
		// $i+=1;
		// $sheet3->mergeCells('A'.$i.':D'.$i);
		// $sheet3->setCellValue('A'.$i , 'NILAI RATA-RATA KELAS');
		// $sheet3->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal('center');
		// $sheet3->setCellValue('E'.$i , $tempJumlah/$tempBanyakSiswa);
		// $i+=1;
		// $sheet3->mergeCells('A'.$i.':D'.$i);
		// $sheet3->setCellValue('A'.$i , 'DAYA SERAP');
		// $sheet3->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal('center');
		// $i+=1;
		// $sheet3->mergeCells('A'.$i.':D'.$i);
		// $sheet3->setCellValue('A'.$i , '<KKM');
		// $sheet3->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal('center');
		// $sheet3->setCellValue('E'.$i , $countSiswaRemed);
		// $i+=2;
		// $sheet3->setCellValue('D'.$i , 'Mengetahui,');
		// $sheet3->setCellValue('F'.$i , 'Bandung,'.$tgl2);
		// $i+=1;
		// $sheet3->setCellValue('D'.$i , 'Kepala Sekolah');
		// $sheet3->setCellValue('F'.$i , 'Guru Mata Pelajaran,');
		// $i+=3;
		// $sheet3->setCellValue('D'.$i , 'Yosep Yaya K, S.Pd');
		// $sheet3->setCellValue('F'.$i , $this->session->nama);
		// $i+=1;
		// $sheet3->setCellValue('D'.$i , 'NIP.'.$this->session->nik);
		// $sheet3->setCellValue('F'.$i , 'NIP.'.$this->session->nik);


		$writer = new Xlsx($spreadsheet);

		$filename = 'Daftar_analisis_kelas-' . $kelas;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	public function analisaExcel()
	{
		$kelas = $this->input->post('kelas');
		$id = $this->input->post('idUjian');
		$ujian = $this->M_ujian->getUjianById($id);
		$allKelas = $this->M_kelas->getKelasByClass2($kelas);
		$allUser = $this->M_user->getAllUserByKelas($kelas);
		// var_dump($allUser);exit;
		//$nilai = $this->M_nilai->getNilaiByIdUjianAndKelas($id,$kelas);
		$tgl = date('d-M');
		$tgl2 = date('d-M-Y');
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayOut = [
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$headingArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 14,
				'name'  => 'Arial',
			)
		);
		$contentArray = array(
			'font'  => array(
				'bold'  => false,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial',
			)
		);
		$leadArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial'
			)
		);
		// start sheet 3//
		$tempJumlah = 0;
		$tempBanyakSiswa = 0;
		$countSiswaRemed = 0;
		$spreadsheet = new Spreadsheet();
		$sheet1 = $spreadsheet->getActiveSheet(0);
		$sheet1->setTitle("DATA SISWA");
		$i = 1;
		$sheet1->getColumnDimension('A')->setWidth(10);
		$sheet1->getColumnDimension('B')->setWidth(10);
		$sheet1->getColumnDimension('C')->setWidth(10);
		$sheet1->getColumnDimension('D')->setWidth(30);
		$sheet1->getColumnDimension('E')->setWidth(10);
		$sheet1->setCellValue('D' . $i, 'Nama');
		$sheet1->setCellValue('E' . $i, 'L/P');
		$sheet1->setCellValue('F' . $i, 'L/P');
		$i += 1;
		$j = $i;
		$ct = 1;
		for ($i; $i < count($allUser) + $j; $i++) {
			$sheet1->setCellValue('A' . $i, $allUser[$i - $j]->kelas);
			$sheet1->setCellValue('B' . $i, $ct);
			$sheet1->setCellValue('C' . $i, strval($allUser[$i - $j]->kelas . $ct));
			$sheet1->setCellValue('D' . $i, $allUser[$i - $j]->nama);
			$sheet1->setCellValue('E' . $i, $allUser[$i - $j]->jenis_kelamin);
			$sheet1->setCellValue('F' . $i, $allUser[$i - $j]->nik);
			$ct += 1;
			if ($i - $j >= 0) {
				if ($allUser[$i - $j]->kelas != $allUser[$i - $j + 1]->kelas) {
					$ct = 1;
				}
			}
		}
		$sheet1->getStyle('A1' . ':E' . ($i - 1))->applyFromArray($styleArray);
		$sheet1 = $spreadsheet->createSheet();
		$sheet1 = $spreadsheet->setActiveSheetIndex(1);
		$sheet1->setTitle("ANALISA_" . $kelas);
		$i = 1;
		$sheet1->getDefaultColumnDimension()->setWidth(5);
		$sheet1->getColumnDimension('A')->setWidth(7);
		$sheet1->getColumnDimension('B')->setWidth(30);
		$sheet1->mergeCells('A' . $i . ':U' . $i);
		$sheet1->setCellValue('A' . $i, 'ANALISA SOAL ULANGAN HARIAN ' . $ujian->mata_pelajaran);
		$sheet1->getStyle('A' . $i)->applyFromArray($headingArray);
		$i += 2;
		$sheet1->setCellValue('B' . $i, 'NAMA SEKOLAH');
		$sheet1->setCellValue('C' . $i, ':');
		$sheet1->setCellValue('D' . $i, $this->session->unit . ' TALENTA');
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->setCellValue('B' . $i, 'MATA PELAJARAN');
		$sheet1->setCellValue('C' . $i, ':');
		$sheet1->setCellValue('D' . $i, $ujian->mata_pelajaran);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->setCellValue('B' . $i, 'KELAS');
		$sheet1->setCellValue('C' . $i, ':');
		$sheet1->setCellValue('D' . $i, $kelas);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->setCellValue('B' . $i, 'TANGGAL TES');
		$sheet1->setCellValue('C' . $i, ':');
		$sheet1->setCellValue('D' . $i, $tgl2);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->setCellValue('B' . $i, 'MATERI POKOK');
		$sheet1->setCellValue('C' . $i, ':');
		$sheet1->setCellValue('D' . $i, $ujian->materi_pokok);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 1;
		$sheet1->setCellValue('B' . $i, 'KKM');
		$sheet1->setCellValue('C' . $i, ':');
		$sheet1->setCellValue('D' . $i, $ujian->kkm);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$i += 2;
		$sheet1->setCellValue('A' . $i, 'KELAS');
		$sheet1->setCellValue('B' . $i, $kelas);
		$i += 1;
		$beginI = $i;
		$sheet1->mergeCells('A' . $i . ':A' . ($i + 1));
		$sheet1->mergeCells('B' . $i . ':B' . ($i + 1));
		$sheet1->setCellValue('A' . $i, 'NO');
		$sheet1->setCellValue('B' . $i, 'NAMA SISWA');
		$sheet1->setCellValue('C' . $i, 'NO. SOAL');
		$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('A' . $i . ':A' . ($i + 1))->applyFromArray($styleArray);
		$sheet1->getStyle('B' . $i . ':B' . ($i + 1))->applyFromArray($styleArray);
		$i += 1;
		if ($ujian->tipe == "Gabungan") {
			$soalPg = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
			$soalIsian = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
			$jumlahSoal = count($soalPg) + count($soalIsian);
			$noSoal = [];
			$jumlahSoalpg = count($soalPg);
			for ($h = 0; $h < $jumlahSoal; $h++) {
				if ($h < count($soalPg)) {
					$noSoal[$h] = $soalPg[$h];
				} else {
					$noSoal[$h] = $soalIsian[$h - count($soalPg)];
				}
			}
		} else {
			if ($ujian->jenis == "Pilihan Ganda") {
				$noSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			} else {
				$noSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			}
		}
		//var_dump($noSoal);exit;
		$arrJmlBenar = [];
		for ($ns = 0; $ns < count($noSoal); $ns++) {
			$arrJmlBenar[$ns] = 0;
		}
		$cellVal = 3;
		for ($ns = 0; $ns < count($noSoal); $ns++) {
			$sheet1->setCellValueByColumnAndRow($cellVal, $i, $ns + 1);
			$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$cellVal += 1;
		}
		$sheet1->mergeCellsByColumnAndRow(3, ($i - 1), ($cellVal - 1), ($i - 1));
		$sheet1->getStyle('C' . ($i - 1))->getAlignment()->setHorizontal('center');

		$sheet1->mergeCellsByColumnAndRow($cellVal, ($i - 1), $cellVal, $i);
		$sheet1->setCellValueByColumnAndRow($cellVal, ($i - 1), 'BENAR');
		$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
		$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
		$cellVal += 1;
		$sheet1->setCellValueByColumnAndRow($cellVal, ($i - 1), 'JUMLAH SOAL');
		$sheet1->mergeCellsByColumnAndRow($cellVal, ($i - 1), $cellVal, $i);
		$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
		$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
		$cellVal += 1;
		$sheet1->setCellValueByColumnAndRow($cellVal, ($i - 1), 'SALAH');
		$sheet1->mergeCellsByColumnAndRow($cellVal, ($i - 1), $cellVal, $i);
		$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
		$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);

		// var_dump($arrJmlBenar);exit;
		$i += 1;
		$jmlUser = 0;
		$beginI2 = $i;
		for ($ck = 0; $ck < count($allKelas); $ck++) {
			//start logic nomor soal//
			//end logic nomor soal//
			$userKelas = $this->M_nilai->getNilaiByIdUjianAndKelas($id, $allKelas[$ck]->nama);
			for ($us = 0; $us < count($userKelas); $us++) {
				$cellVal = 3;
				$bUser = 0;
				$sheet1->setCellValue('A' . $i, $userKelas[$us]->kelas . ($us + 1));
				$sheet1->setCellValue('B' . $i, $userKelas[$us]->nama);
				if ($ujian->tipe == "Gabungan") {
					$allSoal = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
					$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
					for ($z = 0; $z < count($allSoal); $z++) {
						$isAnswered = false;
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								$isAnswered = true;
								if (trim($jawabanSiswa[$js]->jawaban_asli) ==  trim($allSoal[$z]->kunci_PG)) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
								}
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
						if (!$isAnswered) {
							$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
							$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
							$cellVal += 1;
						}
					}
					$allSoal = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
					// var_dump($allSoal);exit;
					$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
					for ($z = 0; $z < count($allSoal); $z++) {
						$isAnswered = false;
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								$isAnswered = true;
								if (trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) ==  trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban1)))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) ==  trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban2)))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) ==  trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban3)))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) ==  trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban4)))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower(strip_tags($jawabanSiswa[$js]->jawaban))) ==  trim(strtolower(strip_tags($allSoal[$z]->kunci_jawaban5)))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
								}
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
						if (!$isAnswered) {
							$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
							$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
							$cellVal += 1;
						}
					}
				} else {
					if ($ujian->jenis == "Pilihan Ganda") {
						$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
						$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
						for ($z = 0; $z < count($allSoal); $z++) {
							$isAnswered = false;
							for ($js = 0; $js < count($jawabanSiswa); $js++) {
								if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
									$isAnswered = true;
									if ($jawabanSiswa[$js]->jawaban_asli ==  $allSoal[$z]->kunci_pg) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
									}
									$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
									$cellVal += 1;
								}
							}
							if (!$isAnswered) {
								$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
					} else {
						$allSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
						// var_dump($allSoal);exit;
						$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
						for ($z = 0; $z < count($allSoal); $z++) {
							$isAnswered = false;
							for ($js = 0; $js < count($jawabanSiswa); $js++) {
								if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
									$isAnswered = true;
									if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban1))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban2))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban3))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban4))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban5))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
									}
									$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
									$cellVal += 1;
								}
							}
							if (!$isAnswered) {
								$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
					}
				}
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, $bUser);
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, count($noSoal));
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, (count($noSoal) - $bUser));
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$i += 1;
				$jmlUser += 1;
			}
		}
		$sheet1->setCellValue('B' . $i, "NO SOAL");
		$cellVal = 3;
		for ($ns = 0; $ns < count($noSoal); $ns++) {
			$sheet1->setCellValueByColumnAndRow($cellVal, $i, $ns + 1);
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$cellVal += 1;
		}
		$i += 1;
		$sheet1->setCellValue('B' . $i, "JUMLAH BENAR");
		$cellVal = 3;
		$matrixRankSalah = [];
		for ($ns = 0; $ns < count($noSoal); $ns++) {
			$matrixRankSalah[$ns] = [];
		}
		//var_dump($arrJmlBenar);exit;
		for ($ns = 0; $ns < count($noSoal); $ns++) {
			$sheet1->setCellValueByColumnAndRow($cellVal, $i, $arrJmlBenar[$ns]);
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$cellVal += 1;
		}
		$i += 1;
		$sheet1->setCellValue('B' . $i, "p");
		$cellVal = 3;
		for ($ns = 0; $ns < count($noSoal); $ns++) {
			if ($jmlUser != 0) {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, $arrJmlBenar[$ns] / $jmlUser);
			} else {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
			}
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$cellVal += 1;
		}
		$sheet1->getStyle('A' . $beginI2 . ':B' . $i)->applyFromArray($styleArray);
		$i += 1;
		//$sheet1->setCellValue('B'.$i,"p");
		$cellVal = 3;
		//start analisa per kelas//
		for ($ck = 0; $ck < count($allKelas); $ck++) {
			$bUser = 0;
			$sheet1 = $spreadsheet->createSheet();
			$sheet1 = $spreadsheet->setActiveSheetIndex($ck + 2);
			$sheet1->setTitle("ANALISA_" . $allKelas[$ck]->nama);

			$i = 1;
			$sheet1->getDefaultColumnDimension()->setWidth(5);
			$sheet1->getColumnDimension('A')->setWidth(7);
			$sheet1->getColumnDimension('B')->setWidth(30);
			$sheet1->mergeCells('A' . $i . ':U' . $i);
			$sheet1->setCellValue('A' . $i, 'ANALISA SOAL ULANGAN HARIAN ' . $ujian->mata_pelajaran);
			$sheet1->getStyle('A' . $i)->applyFromArray($headingArray);
			$i += 2;
			$sheet1->setCellValue('B' . $i, 'NAMA SEKOLAH');
			$sheet1->setCellValue('C' . $i, ':');
			$sheet1->setCellValue('D' . $i, $this->session->unit . ' TALENTA');
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->setCellValue('B' . $i, 'MATA PELAJARAN');
			$sheet1->setCellValue('C' . $i, ':');
			$sheet1->setCellValue('D' . $i, $ujian->mata_pelajaran);
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->setCellValue('B' . $i, 'KELAS');
			$sheet1->setCellValue('C' . $i, ':');
			$sheet1->setCellValue('D' . $i, $allKelas[$ck]->nama);
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->setCellValue('B' . $i, 'TANGGAL TES');
			$sheet1->setCellValue('C' . $i, ':');
			$sheet1->setCellValue('D' . $i, $tgl2);
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->setCellValue('B' . $i, 'MATERI POKOK');
			$sheet1->setCellValue('C' . $i, ':');
			$sheet1->setCellValue('D' . $i, $ujian->materi_pokok);
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$i += 1;
			$sheet1->setCellValue('B' . $i, 'KKM');
			$sheet1->setCellValue('C' . $i, ':');
			$sheet1->setCellValue('D' . $i, $ujian->kkm);
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$i += 2;
			$sheet1->setCellValue('A' . $i, 'KELAS');
			$sheet1->setCellValue('B' . $i, $allKelas[$ck]->nama);
			$i += 1;
			$sheet1->mergeCells('A' . $i . ':A' . ($i + 1));
			$sheet1->mergeCells('B' . $i . ':B' . ($i + 1));
			$sheet1->setCellValue('A' . $i, 'NO');
			$sheet1->setCellValue('B' . $i, 'NAMA SISWA');
			$sheet1->setCellValue('C' . $i, 'NO. SOAL');
			$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
			$sheet1->getStyle('A' . $i . ':A' . ($i + 1))->applyFromArray($styleArray);
			$sheet1->getStyle('B' . $i . ':B' . ($i + 1))->applyFromArray($styleArray);
			$i += 1;
			//start logic nomor soal//
			if ($ujian->tipe == "Gabungan") {
				$soalPg = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
				$soalIsian = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
				$jumlahSoal = count($soalPg) + count($soalIsian);
				$noSoal = [];
				$jumlahSoalpg = count($soalPg);
				for ($h = 0; $h < $jumlahSoal; $h++) {
					if ($h < count($soalPg)) {
						$noSoal[$h] = $soalPg[$h];
					} else {
						$noSoal[$h] = $soalIsian[$h - count($soalPg)];
					}
				}
			} else {
				if ($ujian->jenis == "Pilihan Ganda") {
					$noSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
				} else {
					$noSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
				}
			}
			//end logic nomor soal//
			$arrJmlBenar = [];
			for ($ns = 0; $ns < count($noSoal); $ns++) {
				$arrJmlBenar[$ns] = 0;
			}
			$cellVal = 3;
			for ($ns = 0; $ns < count($noSoal); $ns++) {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, $ns + 1);
				$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
			}
			$sheet1->mergeCellsByColumnAndRow(3, ($i - 1), ($cellVal - 1), ($i - 1));
			$sheet1->getStyle('C' . ($i - 1))->getAlignment()->setHorizontal('center');
			$sheet1->getStyle('C' . ($i - 1))->getAlignment()->setHorizontal('center');
			$sheet1->mergeCellsByColumnAndRow($cellVal, ($i - 1), $cellVal, $i);
			$sheet1->setCellValueByColumnAndRow($cellVal, ($i - 1), 'BENAR');
			$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, ($i - 1), 'JUMLAH SOAL');
			$sheet1->mergeCellsByColumnAndRow($cellVal, ($i - 1), $cellVal, $i);
			$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, ($i - 1), 'SALAH');
			$sheet1->mergeCellsByColumnAndRow($cellVal, ($i - 1), $cellVal, $i);
			$sheet1->getCellByColumnAndRow($cellVal, ($i - 1))->getStyle()->applyFromArray($styleArray);
			$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
			$jmlUser = 0;
			$i += 1;
			$beginI2 = $i;
			$userKelas = $this->M_nilai->getNilaiByIdUjianAndKelas($id, $allKelas[$ck]->nama);
			for ($us = 0; $us < count($userKelas); $us++) {
				$bUser = 0;
				$cellVal = 3;
				$sheet1->setCellValue('A' . $i, $us + 1);
				$sheet1->setCellValue('B' . $i, $userKelas[$us]->nama);
				if ($ujian->tipe == "Gabungan") {
					$allSoal = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
					$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
					// var_dump($userKelas);exit;
					for ($z = 0; $z < count($allSoal); $z++) {
						$isAnswered = false;
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								$isAnswered = true;
								if (trim($jawabanSiswa[$js]->jawaban_asli) ==  trim($allSoal[$z]->kunci_pg)) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
								}
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
						if (!$isAnswered) {
							$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
							$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
							$cellVal += 1;
						}
					}
					$allSoal = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
					$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
					for ($z = 0; $z < count($allSoal); $z++) {
						$isAnswered = false;
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								$isAnswered = true;
								if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban1))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban2))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban3))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban4))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban5))) {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
									$arrJmlBenar[$cellVal - 3] += 1;
									$bUser += 1;
								} else {
									$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
								}
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
						if (!$isAnswered) {
							$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
							$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
							$cellVal += 1;
						}
					}
				} else {
					if ($ujian->jenis == "Pilihan Ganda") {
						$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
						$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
						// var_dump($userKelas);exit;
						for ($z = 0; $z < count($allSoal); $z++) {
							$isAnswered = false;
							for ($js = 0; $js < count($jawabanSiswa); $js++) {
								if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
									$isAnswered = true;
									if (trim($jawabanSiswa[$js]->jawaban_asli) ==  trim($allSoal[$z]->kunci_pg)) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
									}
									$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
									$cellVal += 1;
								}
							}
							if (!$isAnswered) {
								$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
					} else {
						$allSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
						$jawabanSiswa = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByKelas($userKelas[$us]->nik, $id, $allKelas[$ck]->nama);
						for ($z = 0; $z < count($allSoal); $z++) {
							$isAnswered = false;
							for ($js = 0; $js < count($jawabanSiswa); $js++) {
								if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
									$isAnswered = true;
									if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban1))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban2))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban3))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban4))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else if (trim(strtolower($jawabanSiswa[$js]->jawaban)) ==  trim(strtolower($allSoal[$z]->kunci_jawaban5))) {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 1);
										$arrJmlBenar[$cellVal - 3] += 1;
										$bUser += 1;
									} else {
										$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
									}
									$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
									$cellVal += 1;
								}
							}
							if (!$isAnswered) {
								$sheet1->setCellValueByColumnAndRow($cellVal, $i, '');
								$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
								$cellVal += 1;
							}
						}
					}
				}
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, $bUser);
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, count($noSoal));
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, (count($noSoal) - $bUser));
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$i += 1;
				$jmlUser += 1;
			}
			$sheet1->setCellValue('B' . $i, "NO SOAL");
			$cellVal = 3;
			for ($ns = 0; $ns < count($noSoal); $ns++) {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, $ns + 1);
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
			}
			$i += 1;
			$sheet1->setCellValue('B' . $i, "JUMLAH BENAR");
			$cellVal = 3;
			for ($ns = 0; $ns < count($noSoal); $ns++) {
				$sheet1->setCellValueByColumnAndRow($cellVal, $i, $arrJmlBenar[$ns]);
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
			}
			$i += 1;
			$sheet1->setCellValue('B' . $i, "p");
			$cellVal = 3;
			for ($ns = 0; $ns < count($noSoal); $ns++) {
				if ($jmlUser != 0) {
					$sheet1->setCellValueByColumnAndRow($cellVal, $i, $arrJmlBenar[$ns] / $jmlUser);
				} else {
					$sheet1->setCellValueByColumnAndRow($cellVal, $i, 0);
				}
				$sheet1->getCellByColumnAndRow($cellVal, $i)->getStyle()->applyFromArray($styleArray);
				$cellVal += 1;
			}
			$sheet1->getStyle('A' . $beginI2 . ':B' . $i)->applyFromArray($styleArray);
			// $sheet1->mergeCellsByColumnAndRow(3,($i-1),($cellVal-1),($i-1));
		}
		// end analisa per kelas//
		$writer = new Xlsx($spreadsheet);

		$filename = 'analisa ' . $ujian->nama;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	public function daftarNilaiExcel()
	{
		$kelas = $this->input->post('kelas');
		$id = $this->input->post('idUjian');
		$ujian = $this->M_ujian->getUjianById($id);
		$allKelas = $this->M_kelas->getKelasByClass2($kelas);
		$tempUser = $this->M_user->getAllUserByKelas($kelas);
		$nilai = $this->M_nilai->getNilaiByIdUjianAndKelas3($id, $kelas);
		// array_push($tempUser,'');
		$allUser = [];
		$tmp = $tempUser[0];
		$max = 100;
		$jumlahUser = count($tempUser);
		$counterKelas = 0;
		for ($ct = 0; $ct < $jumlahUser; $ct++) {
			for ($ct2 = $ct; $ct2 < $jumlahUser; $ct2++) {
				if ($tempUser[$ct]->kelas != $tempUser[$ct2]->kelas) {
					break;
				}
				if ($tempUser[$ct2]->no_absen <= $max) {
					$tmp = $tempUser[$ct];
					$tempUser[$ct] = $tempUser[$ct2];
					$tempUser[$ct2] = $tmp;
					$max = $tempUser[$ct]->no_absen;
				}
			}
			$max = 100;
		}
		$allUser = $tempUser;
		$tgl = date('d-M');
		$tgl2 = date('d-M-Y');
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$styleArrayOut = [
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$headingArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 14,
				'name'  => 'Arial',
			)
		);
		$contentArray = array(
			'font'  => array(
				'bold'  => false,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial',
			)
		);
		$leadArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial'
			)
		);
		// start sheet 1//
		$bFirst = 0;
		$tempJumlah = 0;
		$tempBanyakSiswa = 0;
		$countSiswaRemed = 0;
		$spreadsheet = new Spreadsheet();
		$sheet1 = $spreadsheet->getActiveSheet(0);
		$sheet1->setTitle("NILAI" . $kelas);
		$sheet1->getColumnDimension('A')->setWidth(10);
		$sheet1->getColumnDimension('B')->setWidth(30);
		$sheet1->getColumnDimension('C')->setWidth(10);
		$sheet1->getColumnDimension('D')->setWidth(10);
		$i = 1;
		$sheet1->mergeCells('A' . $i . ':L' . $i);
		$sheet1->setCellValue('A' . $i, 'DAFTAR NILAI KELAS ' . $kelas . ' ' . $ujian->nama . ' - TA ' . $ujian->tahun_ajaran);
		$sheet1->getStyle('A' . $i)->applyFromArray($headingArray);
		$sheet1->getStyle('A' . $i . ':J' . $i)->getAlignment()->setHorizontal('center');
		$i += 1;
		$beginI = $i;
		$sheet1->setCellValue('B' . $i, 'Nama');
		$sheet1->setCellValue('C' . $i, 'Nilai');
		$sheet1->setCellValue('D' . $i, 'R');
		$sheet1->setCellValue('E' . $i, 'KKM');
		$sheet1->setCellValue('F' . $i, $ujian->kkm);
		$i += 1;
		$j = $i;
		$ct = 1;
		$lastClass = $allUser[0]->kelas;
		$minimum = 100;
		$maximum = 0;
		$totalScore = 0;
		$countAllUser = 0;
		$passStudent = 0;
		$notPass = 0;
		for ($i; $i < count($allUser) + $j; $i++) {
			if ($lastClass != $allUser[$i - $j]->kelas) {
				$ct = 1;
				$lastClass = $allUser[$i - $j]->kelas;
			}
			$tmpNilai = "-";
			$tmpRemed = "REMED";
			for ($nj = 0; $nj < count($nilai); $nj++) {
				if ($allUser[$i - $j]->nik == $nilai[$nj]->nik) {
					$tmpNilai = $nilai[$nj]->hasil;
					if ($tmpNilai >= $ujian->kkm) {
						$tmpRemed = " ";
						$passStudent += 1;
					} else {
						if ($tmpNilai != "-") {
							$notPass += 1;
						}
					}
					break;
				}
			}
			if ($tmpNilai <= $minimum && $tmpNilai != "-") {
				$minimum = $tmpNilai;
			}
			if ($tmpNilai >= $maximum && $tmpNilai != "-") {
				$maximum = $tmpNilai;
			}
			if ($tmpNilai != "-") {
				$totalScore += $tmpNilai;
			}
			$sheet1->setCellValue('A' . $i, $allUser[$i - $j]->kelas . $ct . " ");
			$sheet1->setCellValue('B' . $i, $allUser[$i - $j]->nama);
			$sheet1->setCellValue('C' . $i, $tmpNilai);
			$sheet1->setCellValue('D' . $i, $tmpRemed);
			$ct += 1;
			$countAllUser += 1;
		}
		$sheet1->setCellValue('A' . $i, "Minimum");
		$sheet1->setCellValue('B' . $i, "");
		$sheet1->setCellValue('C' . $i, $minimum);
		$i += 1;
		$sheet1->setCellValue('A' . $i, "Maximum");
		$sheet1->setCellValue('B' . $i, "");
		$sheet1->setCellValue('C' . $i, $maximum);
		$i += 1;
		$sheet1->setCellValue('A' . $i, "Average");
		$sheet1->setCellValue('B' . $i, "");
		$sheet1->setCellValue('C' . $i, round($totalScore / $countAllUser, 2));
		$i += 1;
		$sheet1->setCellValue('A' . $i, "Daya serap");
		$sheet1->setCellValue('B' . $i, "");
		$sheet1->setCellValue('C' . $i, strval(round($passStudent / $countAllUser, 2) * 100) . "%");
		$i += 1;
		$sheet1->setCellValue('A' . $i, "< KKM");
		$sheet1->setCellValue('B' . $i, "");
		$sheet1->setCellValue('C' . $i, $notPass);
		$i += 1;
		$sheet1->getStyle('A' . $beginI . ':D' . ($i - 1))->applyFromArray($styleArray);
		// end sheet 1//
		// start sheet 2//
		$sheet1 = $spreadsheet->createSheet();
		$sheet1 = $spreadsheet->setActiveSheetIndex(1);
		$sheet1->setTitle("DNILAI" . $kelas);
		$i = 1;
		$sheet1->getDefaultColumnDimension()->setWidth(5);
		$sheet1->getColumnDimension('A')->setWidth(7);
		$sheet1->getColumnDimension('B')->setWidth(30);
		$sheet1->getColumnDimension('C')->setWidth(7);
		$sheet1->getColumnDimension('D')->setWidth(15);
		$sheet1->getColumnDimension('E')->setWidth(7);
		$sheet1->getColumnDimension('F')->setWidth(30);
		$sheet1->getColumnDimension('G')->setWidth(7);
		$sheet1->getColumnDimension('H')->setWidth(15);
		$sheet1->getColumnDimension('I')->setWidth(7);
		$sheet1->getColumnDimension('J')->setWidth(30);
		$sheet1->getColumnDimension('K')->setWidth(7);
		$sheet1->getColumnDimension('L')->setWidth(15);
		$sheet1->mergeCells('A' . $i . ':L' . $i);
		$sheet1->setCellValue('A' . $i, 'DAFTAR NILAI KELAS ' . $kelas . ' ' . $ujian->nama . ' - TA ' . $ujian->tahun_ajaran);
		$sheet1->getStyle('A' . $i)->applyFromArray($headingArray);
		$sheet1->getStyle('A' . $i . ':J' . $i)->getAlignment()->setHorizontal('center');
		$i += 1;
		$sheet1->setCellValue('A' . $i, 'KELAS');
		$sheet1->setCellValue('B' . $i, 'NAMA');
		$sheet1->setCellValue('C' . $i, 'NILAI');
		$sheet1->setCellValue('D' . $i, 'TTD');
		$sheet1->setCellValue('E' . $i, 'KELAS');
		$sheet1->setCellValue('F' . $i, 'NAMA');
		$sheet1->setCellValue('G' . $i, 'NILAI');
		$sheet1->setCellValue('H' . $i, 'TTD');
		$sheet1->setCellValue('I' . $i, 'KELAS');
		$sheet1->setCellValue('J' . $i, 'NAMA');
		$sheet1->setCellValue('K' . $i, 'NILAI');
		$sheet1->setCellValue('L' . $i, 'TTD');
		$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('D' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('E' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('F' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('J' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('K' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('L' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('A' . $i . ':L' . $i)->applyFromArray($styleArray);
		$i += 1;
		$j = $i;
		$first = $i;
		$ct = 1;
		$cellVal = 1;
		$iterasiCell = 1;
		$lastClass = $allUser[0]->kelas;
		$tempI = $i;
		$imax = 0;
		$beginI = $i;
		for ($i; $i < count($allUser) + $j; $i++) {
			if ($lastClass != $allUser[$i - $j]->kelas) {
				$iterasiCell += 4;
				if ($tempI >= $imax) {
					$imax = $tempI;
				}
				if ($iterasiCell >= 12) {
					$sheet1->getStyle('A' . $beginI . ':L' . ($imax - 1))->applyFromArray($styleArray);
					$iterasiCell = 1;
					$first = $imax + 3;
					$bFirst = $first - 1;
					// $imax = 0;
					$sheet1->setCellValue('A' . $bFirst, 'KELAS');
					$sheet1->setCellValue('B' . $bFirst, 'NAMA');
					$sheet1->setCellValue('C' . $bFirst, 'NILAI');
					$sheet1->setCellValue('D' . $bFirst, 'TTD');
					$sheet1->setCellValue('E' . $bFirst, 'KELAS');
					$sheet1->setCellValue('F' . $bFirst, 'NAMA');
					$sheet1->setCellValue('G' . $bFirst, 'NILAI');
					$sheet1->setCellValue('H' . $bFirst, 'TTD');
					$sheet1->setCellValue('I' . $bFirst, 'KELAS');
					$sheet1->setCellValue('J' . $bFirst, 'NAMA');
					$sheet1->setCellValue('K' . $bFirst, 'NILAI');
					$sheet1->setCellValue('L' . $bFirst, 'TTD');
					$sheet1->getStyle('A' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('B' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('C' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('D' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('E' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('F' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('G' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('H' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('I' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('J' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('K' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('L' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('A' . $bFirst . ':L' . $bFirst)->applyFromArray($styleArray);
				}
				$tempI = $first;
				$ct = 1;
				$lastClass = $allUser[$i - $j]->kelas;
			}
			$cellVal = $iterasiCell;
			$tmpNilai = "-";
			$tmpRemed = "REMED";
			for ($nj = 0; $nj < count($nilai); $nj++) {
				if ($allUser[$i - $j]->nik == $nilai[$nj]->nik) {
					$tmpNilai = $nilai[$nj]->hasil;
					if ($tmpNilai >= $ujian->kkm) {
						$tmpRemed = " ";
					}
					break;
				}
			}
			$tmpKelas = $allUser[$i - $j]->kelas . "" . $ct . " ";
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpKelas);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $allUser[$i - $j]->nama);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpNilai);
			$cellVal += 1;
			$tempI += 1;
			$ct += 1;
		}
		$sheet1->getStyle('A' . $bFirst . ':L' . ($tempI - 1))->applyFromArray($styleArray);
		//end sheet 2//
		//start sheet 3//
		$sheet1 = $spreadsheet->createSheet();
		$sheet1 = $spreadsheet->setActiveSheetIndex(2);
		$sheet1->setTitle("PNILAI" . $kelas);
		$i = 1;
		$sheet1->getDefaultColumnDimension()->setWidth(5);
		$sheet1->getColumnDimension('A')->setWidth(7);
		$sheet1->getColumnDimension('B')->setWidth(30);
		$sheet1->getColumnDimension('C')->setWidth(7);
		$sheet1->getColumnDimension('D')->setWidth(7);
		$sheet1->getColumnDimension('E')->setWidth(30);
		$sheet1->getColumnDimension('F')->setWidth(7);
		$sheet1->getColumnDimension('G')->setWidth(7);
		$sheet1->getColumnDimension('H')->setWidth(30);
		$sheet1->getColumnDimension('I')->setWidth(7);
		$sheet1->mergeCells('A' . $i . ':L' . $i);
		$sheet1->setCellValue('A' . $i, 'DAFTAR NILAI KELAS ' . $kelas . ' ' . $ujian->nama . ' - TA ' . $ujian->tahun_ajaran);
		$sheet1->getStyle('A' . $i)->applyFromArray($headingArray);
		$sheet1->getStyle('A' . $i . ':J' . $i)->getAlignment()->setHorizontal('center');
		$i += 1;
		$sheet1->setCellValue('A' . $i, 'KELAS');
		$sheet1->setCellValue('B' . $i, 'NAMA');
		$sheet1->setCellValue('C' . $i, 'NILAI');
		$sheet1->setCellValue('D' . $i, 'KELAS');
		$sheet1->setCellValue('E' . $i, 'NAMA');
		$sheet1->setCellValue('F' . $i, 'NILAI');
		$sheet1->setCellValue('G' . $i, 'KELAS');
		$sheet1->setCellValue('H' . $i, 'NAMA');
		$sheet1->setCellValue('I' . $i, 'NILAI');
		$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('D' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('E' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('F' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
		$i += 1;
		$j = $i;
		$first = $i;
		$ct = 1;
		$cellVal = 1;
		$iterasiCell = 1;
		$lastClass = $allUser[0]->kelas;
		$tempI = $i;
		$imax = 0;
		for ($i; $i < count($allUser) + $j; $i++) {
			if ($lastClass != $allUser[$i - $j]->kelas) {
				$iterasiCell += 3;
				if ($tempI >= $imax) {
					$imax = $tempI;
				}
				if ($iterasiCell >= 9) {
					$sheet1->getStyle('A' . $beginI . ':I' . ($imax - 1))->applyFromArray($styleArray);
					$iterasiCell = 1;
					$first = $imax + 3;
					$bFirst = $first - 1;
					$sheet1->setCellValue('A' . $bFirst, 'KELAS');
					$sheet1->setCellValue('B' . $bFirst, 'NAMA');
					$sheet1->setCellValue('C' . $bFirst, 'NILAI');
					$sheet1->setCellValue('D' . $bFirst, 'KELAS');
					$sheet1->setCellValue('E' . $bFirst, 'NAMA');
					$sheet1->setCellValue('F' . $bFirst, 'NILAI');
					$sheet1->setCellValue('G' . $bFirst, 'KELAS');
					$sheet1->setCellValue('H' . $bFirst, 'NAMA');
					$sheet1->setCellValue('I' . $bFirst, 'NILAI');
					$sheet1->getStyle('A' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('B' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('C' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('D' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('E' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('F' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('G' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('H' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('I' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('A' . $bFirst . ':I' . $bFirst)->applyFromArray($styleArray);
				}
				$tempI = $first;
				$ct = 1;
				$lastClass = $allUser[$i - $j]->kelas;
			}
			$sheet1->getStyle('A' . $bFirst . ':I' . ($tempI - 1))->applyFromArray($styleArray);
			$cellVal = $iterasiCell;
			$tmpNilai = "-";
			$tmpRemed = "REMED";
			for ($nj = 0; $nj < count($nilai); $nj++) {
				if ($allUser[$i - $j]->nik == $nilai[$nj]->nik) {
					$tmpNilai = $nilai[$nj]->hasil;
					if ($tmpNilai >= $ujian->kkm) {
						$tmpRemed = " ";
					}
					break;
				}
			}
			$tmpKelas = $allUser[$i - $j]->kelas . "" . $ct . " ";
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpKelas);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $allUser[$i - $j]->nama);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpNilai);
			if ($tmpNilai < $ujian->kkm) {
				$sheet1->getCellByColumnAndRow($cellVal, $tempI)->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
			}
			$cellVal += 1;
			$tempI += 1;
			$ct += 1;
		}
		$sheet1->getStyle('A' . $bFirst . ':I' . ($tempI - 1))->applyFromArray($styleArray);
		//end sheet 3//
		// start sheet 4//
		$sheet1 = $spreadsheet->createSheet();
		$sheet1 = $spreadsheet->setActiveSheetIndex(3);
		$sheet1->setTitle("ANILAI" . $kelas);
		$i = 1;
		$sheet1->getDefaultColumnDimension()->setWidth(5);
		$sheet1->getColumnDimension('A')->setWidth(7);
		$sheet1->getColumnDimension('B')->setWidth(30);
		$sheet1->getColumnDimension('C')->setWidth(7);
		$sheet1->getColumnDimension('D')->setWidth(9);
		$sheet1->getColumnDimension('E')->setWidth(7);
		$sheet1->getColumnDimension('F')->setWidth(7);
		$sheet1->getColumnDimension('G')->setWidth(30);
		$sheet1->getColumnDimension('H')->setWidth(7);
		$sheet1->getColumnDimension('I')->setWidth(9);
		$sheet1->getColumnDimension('J')->setWidth(7);
		$sheet1->getColumnDimension('K')->setWidth(7);
		$sheet1->getColumnDimension('L')->setWidth(30);
		$sheet1->getColumnDimension('M')->setWidth(7);
		$sheet1->getColumnDimension('N')->setWidth(9);
		$sheet1->getColumnDimension('O')->setWidth(7);
		$sheet1->mergeCells('A' . $i . ':L' . $i);
		$sheet1->setCellValue('A' . $i, 'DAFTAR NILAI KELAS ' . $kelas . ' ' . $ujian->nama . ' - TA ' . $ujian->tahun_ajaran);
		$sheet1->getStyle('A' . $i)->applyFromArray($headingArray);
		$sheet1->getStyle('A' . $i . ':J' . $i)->getAlignment()->setHorizontal('center');
		$i += 1;
		$sheet1->setCellValue('A' . $i, 'KELAS');
		$sheet1->setCellValue('B' . $i, 'NAMA');
		$sheet1->setCellValue('C' . $i, 'NILAI');
		$sheet1->setCellValue('D' . $i, 'REMED');
		$sheet1->setCellValue('E' . $i, 'TTD');
		$sheet1->setCellValue('F' . $i, 'KELAS');
		$sheet1->setCellValue('G' . $i, 'NAMA');
		$sheet1->setCellValue('H' . $i, 'NILAI');
		$sheet1->setCellValue('I' . $i, 'REMED');
		$sheet1->setCellValue('J' . $i, 'TTD');
		$sheet1->setCellValue('K' . $i, 'KELAS');
		$sheet1->setCellValue('L' . $i, 'NAMA');
		$sheet1->setCellValue('M' . $i, 'NILAI');
		$sheet1->setCellValue('N' . $i, 'REMED');
		$sheet1->setCellValue('O' . $i, 'TTD');
		$sheet1->getStyle('A' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('B' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('C' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('D' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('E' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('F' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('G' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('H' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('I' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('J' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('K' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('L' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('M' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('N' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('O' . $i)->applyFromArray($leadArray);
		$sheet1->getStyle('A' . $i . ':O' . $i)->applyFromArray($styleArray);
		$i += 1;
		$j = $i;
		$first = $i;
		$ct = 1;
		$cellVal = 1;
		$iterasiCell = 1;
		$lastClass = $allUser[0]->kelas;
		$tempI = $i;
		$imax = 0;
		for ($i; $i < count($allUser) + $j; $i++) {
			if ($lastClass != $allUser[$i - $j]->kelas) {
				$iterasiCell += 5;
				if ($tempI >= $imax) {
					$imax = $tempI;
				}
				if ($iterasiCell >= 12) {
					$sheet1->getStyle('A' . $beginI . ':O' . ($imax - 1))->applyFromArray($styleArray);
					$iterasiCell = 1;
					$first = $imax + 3;
					$bFirst = $first - 1;
					$sheet1->setCellValue('A' . $bFirst, 'KELAS');
					$sheet1->setCellValue('B' . $bFirst, 'NAMA');
					$sheet1->setCellValue('C' . $bFirst, 'NILAI');
					$sheet1->setCellValue('D' . $bFirst, 'TTD');
					$sheet1->setCellValue('E' . $bFirst, 'REMED');
					$sheet1->setCellValue('F' . $bFirst, 'KELAS');
					$sheet1->setCellValue('G' . $bFirst, 'NAMA');
					$sheet1->setCellValue('H' . $bFirst, 'NILAI');
					$sheet1->setCellValue('I' . $bFirst, 'TTD');
					$sheet1->setCellValue('J' . $bFirst, 'REMED');
					$sheet1->setCellValue('K' . $bFirst, 'KELAS');
					$sheet1->setCellValue('L' . $bFirst, 'NAMA');
					$sheet1->setCellValue('M' . $bFirst, 'NILAI');
					$sheet1->setCellValue('N' . $bFirst, 'TTD');
					$sheet1->setCellValue('O' . $bFirst, 'REMED');
					$sheet1->getStyle('A' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('B' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('C' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('D' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('E' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('F' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('G' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('H' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('I' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('J' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('K' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('L' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('M' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('N' . $bFirst)->applyFromArray($leadArray);
					$sheet1->getStyle('O' . $bFirst)->applyFromArray($leadArray);
				}
				$tempI = $first;
				$ct = 1;
				$lastClass = $allUser[$i - $j]->kelas;
			}
			$cellVal = $iterasiCell;
			$tmpNilai = "-";
			$tmpRemed = "REMED";
			for ($nj = 0; $nj < count($nilai); $nj++) {
				if ($allUser[$i - $j]->nik == $nilai[$nj]->nik) {
					$tmpNilai = $nilai[$nj]->hasil;
					if ($tmpNilai >= $ujian->kkm) {
						$tmpRemed = "TUNTAS";
					}
					break;
				}
			}
			$sheet1->getStyle('A' . $bFirst . ':O' . ($tempI - 1))->applyFromArray($styleArray);
			$tmpKelas = $allUser[$i - $j]->kelas . "" . $ct . " ";
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpKelas);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $allUser[$i - $j]->nama);
			$cellVal += 1;
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpNilai);
			$cellVal += 1;
			if ($tmpNilai < $ujian->kkm) {
				$sheet1->getCellByColumnAndRow($cellVal, $tempI)->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
			}
			$sheet1->setCellValueByColumnAndRow($cellVal, $tempI, $tmpRemed);
			$cellVal += 1;
			$tempI += 1;
			$ct += 1;
		}
		$sheet1->getStyle('A' . $bFirst . ':O' . ($tempI - 1))->applyFromArray($styleArray);
		//end sheet 4//
		$writer = new Xlsx($spreadsheet);

		$filename = $ujian->nama . '-' . $kelas;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
	public function cobaDownload()
	{
		$this->isAnyLogin();
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];
		$headingArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 14,
				'name'  => 'Arial',
			)
		);
		$contentArray = array(
			'font'  => array(
				'bold'  => false,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial',
			)
		);
		$leadArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 9,
				'name'  => 'Arial'
			)
		);
		$spreadsheet = new Spreadsheet();
		$sheet1 = $spreadsheet->getActiveSheet(0);
		$sheet1->setTitle('REKAP ABSEN PER KELAS SEMUA');
		$sheet1->getColumnDimension('E')->setWidth(50);
		$sheet1->getColumnDimension('F')->setWidth(30);
		$sheet1->mergeCells('A1:E1');
		$sheet1->setCellValue('A1', 'REKAP KEHADIRAN ULANGAN');
		$sheet1->getStyle('A1')->applyFromArray($headingArray);
		$sheet1->mergeCells('B3:C3');
		$sheet1->setCellValue('B3', 'Mata Pelajaran');
		$sheet1->setCellValue('D3', ':');
		$sheet1->setCellValue('E3', '');
		$sheet1->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('B3')->applyFromArray($leadArray);
		$sheet1->mergeCells('B4:C4');
		$sheet1->setCellValue('B4', 'Guru Pengampu');
		$sheet1->setCellValue('D4', ':');
		$sheet1->setCellValue('E4', $this->session->nama);
		$sheet1->getStyle('B4')->applyFromArray($leadArray);
		$sheet1->mergeCells('B5:C5');
		$sheet1->setCellValue('B5', 'Kelas');
		$sheet1->setCellValue('D5', ':');
		$sheet1->setCellValue('E5', '');
		$sheet1->getStyle('B5')->applyFromArray($leadArray);
		$sheet1->setCellValue('B6', 'KD');
		$sheet1->setCellValue('C6', '');
		$sheet1->setCellValue('D6', ':');
		$sheet1->setCellValue('E6', '');
		$sheet1->getStyle('B6')->applyFromArray($leadArray);
		$sheet1->mergeCells('B7:C7');
		$sheet1->setCellValue('B7', 'Guru Pengampu');
		$sheet1->setCellValue('D7', ':');
		$sheet1->setCellValue('E7', '');
		$sheet1->getStyle('B7')->applyFromArray($leadArray);
		$sheet1->mergeCells('D9:E9');
		$sheet1->setCellValue('B9', 'No');
		$sheet1->setCellValue('C9', 'No Induk');
		$sheet1->setCellValue('D9', 'Nama Siswa');
		$sheet1->setCellValue('F9', 'Kehadiran');
		$sheet1->getStyle('D9:E9')->getAlignment()->setHorizontal('center');
		$sheet1->getStyle('B9:F9')->applyFromArray($contentArray);
		$sheet1->getStyle('B9:F9')->applyFromArray($styleArray);
		for ($i = 1 + 9; $i <= 36 + 9; $i++) {
			$sheet1->setCellValue('B' . $i, $i - 9);
			$sheet1->setCellValue('C' . $i, '');
			$sheet1->setCellValue('D' . $i, '');
			$sheet1->setCellValue('F' . $i, '');
		}
		$sheet1->mergeCells('B46:E46');
		$sheet1->setCellValue('B46', 'JUMLAH SISWA');
		$sheet1->getStyle('B46')->getAlignment()->setHorizontal('right');
		$sheet1->getStyle('B9:F9')->applyFromArray($leadArray);
		$sheet1->setCellValue('E48', 'Mengetahui');
		$sheet1->setCellValue('F48', 'Bandung, ..... ');
		$sheet1->setCellValue('E49', 'Kepala Sekolah');
		$sheet1->setCellValue('F49', 'Guru Mata Pelajaran,');
		$sheet1->setCellValue('E52', 'Yosep Yaya K, S.Pd');
		$sheet1->setCellValue('F52', $this->session->nama);
		$sheet1->setCellValue('E53', 'NIP. 10413');
		$sheet1->setCellValue('F53', 'NIP.' . $this->session->nik);



		// $sheet2 = $spreadsheet->createSheet();
		// $sheet2 = $spreadsheet->setActiveSheetIndex(1);
		// $sheet2->setCellValue('A1', 'More data');
		// $sheet2->setTitle('Second sheet');

		$writer = new Xlsx($spreadsheet);

		$filename = 'test download';
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');

		// for ($i = 51; $i <= 80; $i++) {
		// 	$spreadsheet->getActiveSheet()->setCellValue('A' . $i, "FName $i");
		// 	$spreadsheet->getActiveSheet()->setCellValue('B' . $i, "LName $i");
		// 	$spreadsheet->getActiveSheet()->setCellValue('C' . $i, "PhoneNo $i");
		// 	$spreadsheet->getActiveSheet()->setCellValue('D' . $i, "FaxNo $i");
		// 	$spreadsheet->getActiveSheet()->setCellValue('E' . $i, true);
		// 	$spreadsheet->getActiveSheet()->getRowDimension($i)->setOutlineLevel(1);
		// 	$spreadsheet->getActiveSheet()->getRowDimension($i)->setVisible(false);
		// }
	}
	//End Report
	//Start Gambar
	public function dataGambar()
	{
		$this->isAnyLogin();
		$data['gambar'] = $this->M_gambar->getGambarByNik($this->session->nik);
		$this->load->view('guru/dataGambar', $data);
	}
	public function tambahGambar()
	{
		$this->isAnyLogin();
		$this->load->view('guru/tambahGambar');
	}
	public function tambahGambarDb()
	{
		$this->isAnyLogin();
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'link' => $this->input->post('link', TRUE),
			'nik' => $this->session->nik,
		);
		$this->M_gambar->add($data);
		redirect('guru/dataGambar', 'refresh');
	}
	public function uploadGambar()
	{
		$this->isAnyLogin();
		$config['upload_path'] = './assets/img';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['quality'] = '80%';
		$config['max_size'] = 4196;
		$config['width'] = 150;
		$config['heigth'] = 150;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('berkas')) {
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('guru/testSkill', $error);
		} else {
			// $data = array('upload_data' => $this->upload->data());
			$temp = $this->upload->data();
			$data = array(
				'nama' => $this->input->post('nama', TRUE),
				'link' => $temp['file_name'],
				'nik' => $this->session->nik,
			);
			$this->M_gambar->add($data);
			redirect('guru/dataGambar', 'refresh');
		}
	}
	public function hapusGambar($id)
	{
		$this->isAnyLogin();
		$row = $this->M_gambar->getGambarByID($id);
		if ($row) {
			$this->M_gambar->delete($id);
			$this->session->set_flashdata('message', '<div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <strong>Heads up!</strong>
                Delete Record Success.
                <br>
            </div>');
			redirect('guru/dataGambar');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('guru/dataGambar');
		}
	}
	public function ubahGambar($id)
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelasByID($id);
		$this->load->view('guru/ubahKelas', $data);
	}
	public function simpanUbahGambar()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id');
		$data = array(
			'id' => $this->input->post('id', TRUE),
			'nama' => $this->input->post('nama', TRUE),
			'unit' => $this->input->post('unit', TRUE),
		);

		if ($this->M_kelas->edit_kelas($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('guru/dataKelas', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('guru/ubahKelas/' . $id, 'refresh');
		}
	}
	//End Gambar
	public function nilaiUjian($id, $nik)
	{
		$this->isAnyLogin();
		// $nik = $this->session->nik;
		$data['idUjian'] = $id;
		$data['nik'] = $nik;
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['soal'] = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
		$data['jawaban'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($id, $nik);
		$soalIsian = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjianIsian($id);
		$data['soalGabungan'] = $soalIsian;
		$soalPg = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjian($id);
		$jawabanPg = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nik, $id);
		$jmlSoalPg = count($soalPg);
		$jmlJawabPg = count($jawabanPg);
		$jmlSoalIsian = count($soalIsian);
		$betul = 0;
		for ($i = 0; $i < $jmlSoalPg; $i++) {
			for ($j = 0; $j < $jmlJawabPg; $j++) {
				if ($soalPg[$i]->id_soal == $jawabanPg[$j]->id_soal) {
					if ($soalPg[$i]->kunci_pg == $jawabanPg[$j]->jawaban_asli) {
						$betul += 1;
					}
				}
			}
		}
		$maximumIsian = 0;
		for($i = 0;$i< $jmlSoalIsian; $i++){
			$maximumIsian += $soalIsian[$i]->bobot;
		}
		$nilaiMax = round((1 / ($jmlSoalPg + $jmlSoalIsian))*100,2);
		$data['jmlSoalPg'] = $jmlSoalPg;
		$data['jmlSoalIsian'] = $jmlSoalIsian;
		$data['jmlJawabPg'] = $jmlJawabPg;
		$data['jmlBetul'] = $betul;
		$data['nilaiMax'] = $nilaiMax;
		$data['nilaiMaxIsian'] = $maximumIsian;
		$this->load->view('guru/nilaiUjian', $data);
	}

	public function submitScore($score, $idSoal, $idUjian, $nik)
	{
		$this->isAnyLogin();
		$data = [
			'nilai_point' => $score,
			'status' => 'Sudah dinilai',
		];
		$this->M_jawaban_siswa_isian->editJawabanSiswaIsian($idSoal, $data);
		redirect('guru/nilaiUjian/' . $idUjian . '/' . $nik, 'refresh');
	}

	public function submitLastScore($score, $idUjian, $nik)
	{
		$this->isAnyLogin();
		$data = [
			'hasil' => $score,
		];
		$this->M_nilai->editNilaiByNikAndIdUjian($nik, $idUjian, $data);
		redirect('guru/detailReport/' . $idUjian . '/Isian', 'refresh');
	}
	public function calculateLastScore($id, $nik)
	{
		$this->isAnyLogin();
		$ujian = $this->M_ujian->getUjianById($id);
		if($ujian->tipe == "Gabungan"){
			$soalPg = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjian($id);
			$jawabanPg = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nik, $id);
			$jmlSoalPg = count($soalPg);
			$jmlJawabPg = count($jawabanPg);
			$betul = 0;
			$persentasePg= $ujian->persentase_pg;
			$persentaseIsian= $ujian->persentase_isian;
			for ($i = 0; $i < $jmlSoalPg; $i++) {
				for ($j = 0; $j < $jmlJawabPg; $j++) {
					if ($soalPg[$i]->id_soal == $jawabanPg[$j]->id_soal) {
						if ($soalPg[$i]->kunci_pg == $jawabanPg[$j]->jawaban_asli) {
							$betul += 1;
						}
					}
				}
			}
			$soalIsian = $this->M_ujian_gabungan_has_soal->getUjianGabunganHasSoalByIdUjianIsian($id);
			$jawabanIsian = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($id, $nik);
			$jmlSoalIsian = count($soalIsian);
			$maximumIsian = 0;
			for($i = 0;$i< $jmlSoalIsian; $i++){
				$maximumIsian += $soalIsian[$i]->bobot;
			}
			$nilaiIsian = 0;
			for($i = 0; $i< count($jawabanIsian); $i++){
				$nilaiIsian += $jawabanIsian[$i]->nilai_point;
			}
			$nilaiAkhirPg = ($betul / $jmlSoalPg * 100) * ($persentasePg / 100);
			$nilaiAkhirIsian = ($nilaiIsian / $maximumIsian * 100) * ($persentaseIsian / 100);
			$nilaiAkhir = round($nilaiAkhirPg + $nilaiAkhirIsian, 2);
			$data = [
				'hasil' => $nilaiAkhir,
			];
			$this->M_nilai->editNilaiByNikAndIdUjian($nik, $id, $data);
			redirect('guru/detailReport/' . $id . '/Gabungan', 'refresh');
		}else{
			$soalIsian = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			$jawabanIsian = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($id, $nik);
			$jmlSoalIsian = count($soalIsian);
			$maximumIsian = 0;
			for($i = 0;$i< $jmlSoalIsian; $i++){
				$maximumIsian += $soalIsian[$i]->bobot;
			}
			$nilaiIsian = 0;
			for($i = 0; $i< count($jawabanIsian); $i++){
				$nilaiIsian += $jawabanIsian[$i]->nilai_point;
			}
			$nilaiAkhir = ($nilaiIsian / $maximumIsian * 100);
			$data = [
				'hasil' => $nilaiAkhir,
			];
			$this->M_nilai->editNilaiByNikAndIdUjian($nik, $id, $data);
			redirect('guru/detailReport/' . $id . '/Isian', 'refresh');
		}
	}
}
