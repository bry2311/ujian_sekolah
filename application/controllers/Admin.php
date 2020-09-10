<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Admin extends CI_Controller
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
		$this->load->model('M_menu');
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
		$data['menu'] = $this->M_menu->makeMenu('kelas');
		$this->load->view('admin/dataKelas', $data);
	}
	public function tambahKelas()
	{
		$this->isAnyLogin();
		$data['menu'] = $this->M_menu->makeMenu('kelas');
		$this->load->view('admin/tambahKelas');
	}
	public function tambahKelasDb()
	{
		$this->isAnyLogin();
		$data = array(
			'nama' => $this->input->post('nama', TRUE),
			'unit' => $this->input->post('unit', TRUE),
		);
		$this->M_kelas->add($data);
		redirect('admin/dataKelas', 'refresh');
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
			redirect('admin/dataKelas');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('admin/dataKelas');
		}
	}
	public function ubahKelas($id)
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelasByID($id);
		$data['menu'] = $this->M_menu->makeMenu('kelas');
		$this->load->view('admin/ubahKelas', $data);
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
			redirect('admin/dataKelas', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('admin/ubahKelas/' . $id, 'refresh');
		}
	}
	//End Kelas

	//Start User
	public function dataUser()
	{
		$this->isAnyLogin();
		$data['user'] = $this->M_user->getUser();
		$data['menu'] = $this->M_menu->makeMenu('user');
		$this->load->view('admin/dataUser', $data);
	}
	public function tambahUser()
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelas();
		$data['menu'] = $this->M_menu->makeMenu('user');
		$this->load->view('admin/tambahUser', $data);
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
				);
				$this->M_user->edit_user($nik, $data);
			} else {
				$data = array(
					'nik' => $nik,
					'password' => MD5($this->input->post('password', TRUE)),
					'nama' => $this->input->post('nama', TRUE),
					'unit' => $this->input->post('unit', TRUE),
					'kelas' => $kelas,
					'role' => ucwords($this->input->post('role', TRUE)),
				);
				$this->M_user->add($data);
			}
			redirect('admin/dataUser', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Gagal Menambah Data.</div>");
			redirect('admin/tambahUser', 'refresh');
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
			redirect('admin/dataUser');
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect('admin/dataUser');
		}
	}
	public function ubahUser($id)
	{
		$this->isAnyLogin();
		$data['kelas'] = $this->M_kelas->getKelas();
		$data['user'] = $this->M_user->getUserByID($id);
		$data['menu'] = $this->M_menu->makeMenu('user');
		$this->load->view('admin/ubahUser', $data);
	}
	public function simbahUbahUser()
	{
		$this->isAnyLogin();
		$id = $this->input->post('id');
		$pass = $this->input->post('password', TRUE);
		$repass = $this->input->post('repassword', TRUE);
		if ($pass == $repass) {
			$kelas =  $this->input->post('kelas', TRUE);
			if ($kelas == "") {
				$kelas = NULL;
			}
			$data = array(
				'no_absen' => $this->input->post('no_absen', TRUE),
				'nik' => $this->input->post('nik', TRUE),
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
			redirect('admin/dataUser', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('admin/ubahUser/' . $id, 'refresh');
		}
	}
	public function importUser()
	{
		$this->isAnyLogin();
		$data['menu'] = $this->M_menu->makeMenu('user');
		$this->load->view('admin/importUser');
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

		redirect("admin/dataUser");
	}
	//End User

	//Start Ujian
	public function dataUjian()
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjian();
		$data['menu'] = $this->M_menu->makeMenu('ujian');
		$this->load->view('admin/dataUjian', $data);
	}
	public function dataUjianGabungan()
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjian();
		$data['menu'] = $this->M_menu->makeMenu('ujian');
		$this->load->view('admin/dataUjianGabungan', $data);
	}
	public function aktifUjian($id)
	{
		$this->isAnyLogin();
		$data = array(
			'status' => 'aktif',
		);
		if ($this->M_ujian->editUjian($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('admin/dataUjian', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('admin/dataUjian', 'refresh');
		}
	}
	public function nonAktifUjian($id)
	{
		$this->isAnyLogin();
		$data = array(
			'status' => 'non-aktif',
		);
		if ($this->M_ujian->editUjian($id, $data)) {
			$this->session->set_flashdata('msg', "<div class='alert alert-success'> Berhasil Mengubah Data.</div>");
			redirect('admin/dataUjian', 'refresh');
		} else {
			$this->session->set_flashdata('msg', "<div class='alert alert-danger'> Gagal Mengubah Data.</div>");
			redirect('admin/dataUjian', 'refresh');
		}
	}

	//End Ujian

	//Start Report
	public function dataReport()
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjian();
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/dataReport', $data);
	}

	public function detailReport($id)
	{
		$this->isAnyLogin();
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/detailReport', $data);
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
		$dataNilai = $this->M_nilai->getNilaiByID($nilaiId);
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/detailReport', $data);
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
		$dataNilai = $this->M_nilai->getNilaiByID($nilaiId);
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/detailReport', $data);
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
		if ($ujian->jenis == "Pilihan Ganda") {
			$this->M_jawaban_siswa->deleteJawabanSiswaByNik($nik, $id);
		} else if ($ujian->jenis == "Isian") {
			$this->M_jawaban_siswa_isian->deleteJawabanSiswaByNik($nik, $id);
		}
		$this->M_nilai->editnilai($nilaiId, $data);
		$data['nilai'] = $this->M_nilai->getNilaiByIdUjian($id);
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$dataNilai = $this->M_nilai->getNilaiByID($nilaiId);
		redirect('admin/detailReport/' . $id, 'refresh');
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
							$sheet1->setCellValue('B' . $tempI, $soalPg[$i - $j]->soal);
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
							if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) != trim(strtolower($js->jawaban))) {
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
							$sheet1->setCellValue('B' . $tempI, $soalIsian[$i - $j]->soal);
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, $js->jawaban);
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
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->ja))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($js->jb));
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jb))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($js->jc));
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jc))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($js->jd));
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jd))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
								$tempI += 1;
								$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
								$sheet1->setCellValue('I' . $beginI, $js->pilihan_jawaban);
								$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
								$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
								$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) != trim(strtolower($js->jawaban))) {
									$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
								}
							}
						}
						if (!$isJawab) {
							$beginI = $tempI;
							$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
							//$sheet1->setCellValue('B'.$tempI, $allSoal[$i-$j]->soal);
							$tempI += 1;
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->e))) {
								$sheet1->setCellValue('B' . $tempI, 'A. ' . $allSoal[$i - $j]->e);
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->e))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
							} else {
								$sheet1->setCellValue('B' . $tempI, 'A. ' . $allSoal[$i - $j]->a);
								if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->a))) {
									$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
								}
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'B. ' . $allSoal[$i - $j]->b);
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->b))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'C. ' . $allSoal[$i - $j]->c);
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->c))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'D. ' . $allSoal[$i - $j]->d);
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->d))) {
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
								$sheet1->setCellValue('B' . $tempI, $allSoal[$i - $j]->soal);
								$tempI += 1;
								$sheet1->setCellValue('B' . $tempI, $js->jawaban);
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
	public function createPdf($id, $nis, $kelas)
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
			for ($i; $i < count($soalPg) + $j; $i++) {
				$isJawab = false;
				foreach ($jawabanSiswaPg as $js) {
					if ($js->id_soal == $allSoal[$i - $j]->id_soal) {
						$isJawab = true;
						$beginI = $tempI;
						$sheet1->setCellValue('A' . $tempI, $soalPg[$i - $j]->no_soal . '.');
						$sheet1->setCellValue('B' . $tempI, $soalPg[$i - $j]->soal);
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
						if (trim(strtolower($soalPg[$i - $j]->kunci_jawaban)) != trim(strtolower($js->jawaban))) {
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
						$sheet1->setCellValue('B' . $tempI, $soalIsian[$i - $j]->soal);
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, $js->jawaban);
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
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->ja))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'B. ' . strip_tags($js->jb));
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jb))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'C. ' . strip_tags($js->jc));
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jc))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, 'D. ' . strip_tags($js->jd));
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($js->jd))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
							$tempI += 1;
							$sheet1->mergeCells('I' . $beginI . ':I' . $tempI);
							$sheet1->setCellValue('I' . $beginI, $js->pilihan_jawaban);
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setHorizontal('center');
							$sheet1->getStyle('I' . $beginI)->getAlignment()->setVertical('center');
							$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->applyFromArray($styleArray);
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) != trim(strtolower($js->jawaban))) {
								$sheet1->getStyle('I' . $beginI . ':I' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
							}
						}
					}
					if (!$isJawab) {
						$beginI = $tempI;
						$sheet1->setCellValue('A' . $tempI, $allSoal[$i - $j]->no_soal . '.');
						//$sheet1->setCellValue('B'.$tempI, $allSoal[$i-$j]->soal);
						$tempI += 1;
						if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->e))) {
							$sheet1->setCellValue('B' . $tempI, 'A. ' . $allSoal[$i - $j]->e);
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->e))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
						} else {
							$sheet1->setCellValue('B' . $tempI, 'A. ' . $allSoal[$i - $j]->a);
							if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->a))) {
								$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
							}
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'B. ' . $allSoal[$i - $j]->b);
						if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->b))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'C. ' . $allSoal[$i - $j]->c);
						if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->c))) {
							$sheet1->getStyle('B' . $tempI)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ADFF2F');
						}
						$tempI += 1;
						$sheet1->setCellValue('B' . $tempI, 'D. ' . $allSoal[$i - $j]->d);
						if (trim(strtolower($allSoal[$i - $j]->kunci_jawaban)) == trim(strtolower($allSoal[$i - $j]->d))) {
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
							$sheet1->setCellValue('B' . $tempI, $allSoal[$i - $j]->soal);
							$tempI += 1;
							$sheet1->setCellValue('B' . $tempI, $js->jawaban);
							$sheet1->getStyle('A' . $beginI . ':H' . $tempI)->applyFromArray($styleArrayOut);
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
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['kelas'] = $this->M_kelas->getKelas();
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/excelReport', $data);
	}
	public function excelReport2($id)
	{
		$this->isAnyLogin();
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['kelas'] = $this->M_kelas->getKelas();
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/excelReport2', $data);
	}
	public function excelReport3($id)
	{
		$this->isAnyLogin();
		$data['nilai'] = $this->M_nilai->getNilai();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$data['kelas'] = $this->M_kelas->getKelas();
		$data['menu'] = $this->M_menu->makeMenu('report');
		$this->load->view('admin/excelReport3', $data);
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
							if (trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban)) {
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
						for ($js = 0; $js < count($jawabanSiswa); $js++) {
							if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
								if (trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban)) {
									$resultD = $resultD . "1";
									$jmlBenar += 1;
								} else {
									$resultD = $resultD . "0";
									$jmlSalah += 1;
								}
							}
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
								if (trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban1) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban2) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban3) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban4) || trim($jawabanSiswa[$js]->jawaban) == trim($allSoal[$z]->kunci_jawaban5)) {
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
		$i += 1;
		$j = $i;
		$ct = 1;
		for ($i; $i < count($allUser) + $j; $i++) {
			$sheet1->setCellValue('A' . $i, $allUser[$i - $j]->kelas);
			$sheet1->setCellValue('B' . $i, $ct);
			$sheet1->setCellValue('C' . $i, strval($allUser[$i - $j]->kelas . $ct));
			$sheet1->setCellValue('D' . $i, $allUser[$i - $j]->nama);
			$sheet1->setCellValue('E' . $i, $allUser[$i - $j]->jenis_kelamin);
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
								if (trim($jawabanSiswa[$js]->jawaban) ==  trim($allSoal[$z]->kunci_jawaban)) {
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
						for ($z = 0; $z < count($allSoal); $z++) {
							$isAnswered = false;
							for ($js = 0; $js < count($jawabanSiswa); $js++) {
								if ($allSoal[$z]->id_soal == $jawabanSiswa[$js]->id_soal) {
									$isAnswered = true;
									if (trim($jawabanSiswa[$js]->jawaban) ==  trim($allSoal[$z]->kunci_jawaban)) {
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
								if (trim($jawabanSiswa[$js]->jawaban) ==  trim($allSoal[$z]->kunci_jawaban)) {
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
									if (trim($jawabanSiswa[$js]->jawaban) ==  trim($allSoal[$z]->kunci_jawaban)) {
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
					}
					break;
				}
			}
			$sheet1->setCellValue('A' . $i, $allUser[$i - $j]->kelas . $ct . " ");
			$sheet1->setCellValue('B' . $i, $allUser[$i - $j]->nama);
			$sheet1->setCellValue('C' . $i, $tmpNilai);
			$sheet1->setCellValue('D' . $i, $tmpRemed);
			$ct += 1;
		}
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
}
