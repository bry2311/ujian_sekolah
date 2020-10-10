<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Siswa extends CI_Controller
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
		$this->load->model('M_soalpg');
		$this->load->model('M_ujian');
		$this->load->model('M_ujian_has_soal');
		$this->load->model('M_jawaban_siswa');
		$this->load->model('M_jawaban_siswa_isian');
		$this->load->model('M_ujian_gabungan_has_soal');
		$this->load->model('M_nilai');
		$this->load->model('M_user');
		$this->load->model('M_soalisian');
		$this->load->library('session');
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
		$data['nilai'] = $this->M_nilai->getNilaiByNik($this->session->nik);
		$data['ujian'] = $this->M_ujian->getUjianAktif();
		$this->load->view('siswa/home.php', $data);
	}

	public function informasiUjian()
	{
		$idUjian = $this->input->post('ujian', TRUE);
		if ($idUjian == "-") {
			echo "<script type='text/javascript'>alert('Maaf, silahkan pilih ujian anda!');</script>";
			$lenKelas = strlen($this->session->kelas);
			$kelas = "";
			if ($lenKelas > 2) {
				$kelas = $this->session->kelas[0] . $this->session->kelas[1];
			} else {
				$kelas = $this->session->kelas[0];
			}
			$data['daftarUjian'] = $this->M_ujian->getUjianAktifByKelas($kelas);
			$this->load->view('siswa/pilihUjian.php', $data);
		} else {
			$ujian = $this->M_ujian->getUjianById($idUjian);
			$data['ujian'] = $ujian;
			$nilai = $this->M_nilai->getNilaiByNik3($this->session->nik, $idUjian);
			echo "<script type='text/javascript'>
			var cek = confirm('Apakah benar ujian yang akan anda lakukan adalah = " . $ujian->nama . " ?');
			if(!cek){
				window.location.href = '" . base_url() . "Login/logout';
			}
			</script>";
			if ($nilai == null || $nilai->ujian_ulang == '1') {
				$this->M_user->updateStatusUjian($this->session->nik, $idUjian);
				$this->load->view('siswa/informasiUjian2.php', $data);
			} else {
				echo "<script type='text/javascript'>alert('Maaf, " . $this->session->nama . " kamu tidak dapat mengikuti " . $ujian->nama . " karena sudah pernah mengikuti sebelumnya.');</script>";
				$this->index();
			}
		}
	}

	//Start Join Ujian
	public function joinUjian($id)
	{
		$this->isAnyLogin();
		$data['ujian'] = $this->M_ujian->getUjianById($id);
		$this->M_user->updateStatusUjian($this->session->nik, $id);
		$this->load->view('siswa/informasiUjian', $data);
	}
	public function startUjian()
	{
		$this->isAnyLogin();
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";

		$id = $this->input->post('id');
		date_default_timezone_set('Asia/Jakarta');
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->tipe == "Tunggal") {
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
			$this->load->view('siswa/ujianSoal', $data);
		} else {
		}
	}
	public function startUjian2()
	{
		$this->isAnyLogin();
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";

		$id = $this->input->post('id');
		date_default_timezone_set('Asia/Jakarta');
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->tipe == "Tunggal") {
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
			$this->load->view('siswa/ujianSoal2', $data);
		} else {
			$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
			$isi = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
			$max = 0;
			foreach ($temp as $t) {
				$max += 1;
			}
			$data['soal'] = $this->M_soalpg->getSoalpg();
			//batas pg
			$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
			$isi2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
			$max2 = 0;
			foreach ($temp2 as $t) {
				$max2 += 1;
			}
			$data['soal2'] = $this->M_soalisian->getSoalIsian();
			//batas
			$data['max'] = $max;
			$data['max2'] = $max2;
			$data['soal_ujian'] = $temp;
			$data['soal_ujian2'] = $temp2;
			$data['index'] = 0;
			$data['index2'] = 0;
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
			$tpz2 = [];
			$jml = count($isi2);
			for ($i = 0; $i < $jml; $i++) {
				$tpz2[$i] = 0;
			}
			$indexRandom = 0;
			$random = rand(0, $jml - 1);
			while ($indexRandom < $jml) {
				if ($tpz2[$random] == 0) {
					$tpz2[$random] = $isi2[$indexRandom]->id_soal;
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
			$this->session->soal_ujian_random2 = $tpz2;
			$this->load->view('siswa/ujianSoalGabungan', $data);
		}
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
		$this->load->view('siswa/ujianSoal2', $data);
	}
	public function back2($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$this->session->soal_ujian_random;
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
		$this->load->view('siswa/ujianSoal2', $data);
	}
	public function next($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getCountSoalByIdUjian($id);
		$max = $temp;
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
		if ($tempIndex >= $max - 1) {
			echo "<script type='text/javascript'>alert('Sudah soal terakhir');</script>";
			$this->load->view('siswa/ujianSoal', $data);
		} else {
			$this->load->view('siswa/ujianSoal', $data);
		}
	}
	public function next2($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_has_soal->getCountSoalByIdUjian($id);
		$max = $temp;
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
		if ($tempIndex >= $max - 1) {
			echo "<script type='text/javascript'>alert('Sudah soal terakhir');</script>";
			$this->load->view('siswa/ujianSoal2', $data);
		} else {
			$this->load->view('siswa/ujianSoal2', $data);
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
		$ja = $this->input->post('ja', TRUE);
		$jb = $this->input->post('jb', TRUE);
		$jc = $this->input->post('jc', TRUE);
		$jd = $this->input->post('jd', TRUE);
		$pilihanJawaban = substr($jawaban, 0, 1);
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
					'ja' => $ja,
					'jb' => $jb,
					'jc' => $jc,
					'jd' => $jd,
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
					'ja' => $ja,
					'jb' => $jb,
					'jc' => $jc,
					'jd' => $jd,
				);
				$this->M_jawaban_siswa->editJawabanSiswa($check->id, $data);
			}
			$this->next($id_ujian, $tempIndex);
		} else {
			$this->next($id_ujian, $tempIndex);
		}
	}
	public function addJawabanSiswa2()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal', TRUE);
		$id_ujian = $this->input->post('id_ujian', TRUE);
		$waktu = $this->input->post('tanggal', TRUE);
		$tempIndex = $this->input->post('tempIndex', TRUE);
		$jawaban = $this->input->post('answer', TRUE);
		$ja = $this->input->post('ja', TRUE);
		$jb = $this->input->post('jb', TRUE);
		$jc = $this->input->post('jc', TRUE);
		$jd = $this->input->post('jd', TRUE);
		$pilihanJawaban = substr($jawaban, 0, 1);
		$jawabanAsli = substr($jawaban, 2, 1);
		$hasilJawaban = substr($jawaban, 4);
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
					'jawaban_asli' => $jawabanAsli,
					'ja' => $ja,
					'jb' => $jb,
					'jc' => $jc,
					'jd' => $jd,
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
					'jawaban_asli' => $jawabanAsli,
					'ja' => $ja,
					'jb' => $jb,
					'jc' => $jc,
					'jd' => $jd,
				);
				$this->M_jawaban_siswa->editJawabanSiswa($check->id, $data);
			}
			$this->next2($id_ujian, $tempIndex);
		} else {
			$this->next2($id_ujian, $tempIndex);
		}
	}
	public function addJawabanSiswa3()
	{
		$this->isAnyLogin();
		$id_soal = $this->input->post('id_soal', TRUE);
		$id_ujian = $this->input->post('id_ujian', TRUE);
		$waktu = $this->input->post('tanggal', TRUE);
		$tempIndex = $this->input->post('tempIndex', TRUE);
		$jawaban = $this->input->post('answer', TRUE);
		$ja = $this->input->post('ja', TRUE);
		$jb = $this->input->post('jb', TRUE);
		$jc = $this->input->post('jc', TRUE);
		$jd = $this->input->post('jd', TRUE);
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
					'ja' => $ja,
					'jb' => $jb,
					'jc' => $jc,
					'jd' => $jd,
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
					'ja' => $ja,
					'jb' => $jb,
					'jc' => $jc,
					'jd' => $jd,
				);
				$this->M_jawaban_siswa->editJawabanSiswa($check->id, $data);
			}
			$this->next3($id_ujian, $tempIndex);
		} else {
			$this->next3($id_ujian, $tempIndex);
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
			$this->next($id_ujian, $tempIndex);
		} else {
			$this->next($id_ujian, $tempIndex);
		}
	}
	public function addJawabanSiswaIsian2()
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
			$this->next2($id_ujian, $tempIndex);
		} else {
			$this->next2($id_ujian, $tempIndex);
		}
	}
	public function addJawabanSiswaIsian3()
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
			$this->next4($id_ujian, $tempIndex);
		} else {
			$this->next4($id_ujian, $tempIndex);
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
	public function checkData($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian3($id);
			$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nik, $id);
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian3($id);
			$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nik, $id);
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}
		$jmlSoal = count($allSoal);
		$jmlJawaban = count($jawabanSiswa);
		$jmlYangBelumDijawab = $jmlSoal - $jmlJawaban;
		if ($jmlYangBelumDijawab > 0) {
			echo "<script type='text/javascript'>
			var cek = confirm('Masih terdapat " . $jmlYangBelumDijawab . " soal yang belum dijawab, Apakah anda yakin ingin mengumpulkan?');
			if(cek){
				window.location.href = '" . base_url() . "Siswa/terminate2/" . $id . "';
			}
			</script>";
		} else {
			$this->terminate2($id);
		}
		$this->back2($id, 1);
	}
	public function terminate2($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$a = $this->M_ujian->getUjianByID($id);
		if ($a->jenis == "Pilihan Ganda") {
			$allSoal = $this->M_ujian_has_soal->getUjianHasSoalByIdUjian($id);
			$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nik, $id);
			$data['soal'] = $this->M_soalpg->getSoalpg();
		} else {
			$allSoal = $this->M_ujian_has_soal->getUjianHasSoalIsianByIdUjian($id);
			$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nik, $id);
			$data['soal'] = $this->M_soalisian->getSoalIsian();
		}
		$jmlSoal = count($allSoal);
		$count = 0;
		if ($a->jenis == "Pilihan Ganda") {
			for ($i = 0; $i < $jmlSoal; $i++) {
				foreach ($jawabanSiswa as $js) {
					if ($js->id_soal == $allSoal[$i]->id_soal) {
						if ($js->jawaban_asli == $allSoal[$i]->kunci_pg) {
							$count += 1;
							break;
						}
					}
				}
			}
		} else {
			for ($i = 0; $i < $jmlSoal; $i++) {
				foreach ($jawabanSiswa as $js) {
					if ($js->id_soal == $allSoal[$i]->id_soal) {
						if (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal[$i]->kunci_jawaban1)))) {
							$count += 1;
							break;
						} else if (isset($allSoal[$i]->kunci_jawaban2) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal[$i]->kunci_jawaban2))))) {
							$count += 1;
							break;
						} else if (isset($allSoal[$i]->kunci_jawaban3) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal[$i]->kunci_jawaban3))))) {
							$count += 1;
							break;
						} else if (isset($allSoal[$i]->kunci_jawaban4) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal[$i]->kunci_jawaban4))))) {
							$count += 1;
							break;
						} else if (isset($allSoal[$i]->kunci_jawaban5) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal[$i]->kunci_jawaban5))))) {
							$count += 1;
							break;
						}
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
		$this->session->soal_ujian_random = "";
		$this->session->waktu = "";
		echo "<script type='text/javascript'>
		var cek = confirm('Terimakasih sudah mengerjakan ujian ini!');
		if(cek){
			window.location.href = '" . base_url() . "/Siswa/logout';
		}else{
			window.location.href = '" . base_url() . "/Siswa/logout';
		}
		</script>";
	}
	public function terminate3($id)
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$a = $this->M_ujian->getUjianByID($id);
		$allSoal = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$jawabanSiswa = $this->M_jawaban_siswa->getJawabanSiswaByNik2($nik, $id);
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$allSoal2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$jawabanSiswa2 = $this->M_jawaban_siswa->getJawabanSiswaIsianByNik2($nik, $id);
		$data['soal2'] = $this->M_soalisian->getSoalIsian();
		$jmlSoal = count($allSoal);
		$count = 0;
		for ($i = 0; $i < $jmlSoal; $i++) {
			foreach ($jawabanSiswa as $js) {
				if ($js->id_soal == $allSoal[$i]->id_soal) {
					if (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal[$i]->kunci_jawaban)))) {
						$count += 1;
						break;
					}
				}
			}
		}
		$jmlSoal2 = count($allSoal2);
		for ($i = 0; $i < $jmlSoal2; $i++) {
			foreach ($jawabanSiswa2 as $js) {
				if ($js->id_soal == $allSoal2[$i]->id_soal) {
					if (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal2[$i]->kunci_jawaban1)))) {
						$count += 1;
						break;
					} else if (isset($allSoal2[$i]->kunci_jawaban2) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal2[$i]->kunci_jawaban2))))) {
						$count += 1;
						break;
					} else if (isset($allSoal2[$i]->kunci_jawaban3) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal2[$i]->kunci_jawaban3))))) {
						$count += 1;
						break;
					} else if (isset($allSoal2[$i]->kunci_jawaban4) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal2[$i]->kunci_jawaban4))))) {
						$count += 1;
						break;
					} else if (isset($allSoal2[$i]->kunci_jawaban5) && (trim(strtolower(strip_tags($js->jawaban))) == trim(strtolower(strip_tags($allSoal2[$i]->kunci_jawaban5))))) {
						$count += 1;
						break;
					}
				}
			}
		}
		$totalSoal = $jmlSoal + $jmlSoal2;
		$n = $this->M_nilai->getNilaiByNikAndIdUjian($nik, $id);
		if ($n == null) {
			$nilaiAkhir = ($count * 100 / $totalSoal);
			$data = array(
				'id_ujian' => $id,
				'nik' => $nik,
				'hasil' => $nilaiAkhir,
				'tampil' => "non-aktif",
				'tipe' => "Gabungan",
				'ujian_ulang' => '0',
			);
			$this->M_nilai->add($data);
		} else {
			$nilaiAkhir = ($count * 100 / $totalSoal);
			$data = array(
				'id_ujian' => $id,
				'nik' => $nik,
				'hasil' => $nilaiAkhir,
				'tampil' => "non-aktif",
				'tipe' => "Gabungan",
				'ujian_ulang' => '0',
			);
			$this->M_nilai->editnilai($n[0]->id, $data);
		}
		$data['nilai'] = $this->M_nilai->getNilaiByNik($nik);
		$this->session->soal_ujian_random = "";
		$this->session->soal_ujian_random2 = "";
		$this->session->waktu = "";
		echo "<script type='text/javascript'>
		var cek = confirm('Terimakasih sudah mengerjakan ujian ini!');
		if(cek){
			window.location.href = '" . base_url() . "/Siswa/logout';
		}else{
			window.location.href = '" . base_url() . "/Siswa/logout';
		}
		</script>";
	}
	public function ujianSoalGabungan($id, $tempIndex)
	{
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$max = count($temp);
		$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$max2 = count($temp2);
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$data['max'] = $max;
		$data['max2'] = $max2;
		$data['soal_ujian'] = $temp;
		$data['soal_ujian2'] = $temp2;
		$data['index'] = $tempIndex;
		$data['index2'] = 0;
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('siswa/ujianSoalGabungan', $data);
	}
	public function ujianSoalIsianGabungan($id, $tempIndex)
	{
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$max = count($temp);
		$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$max2 = count($temp2);
		$data['soal'] = $this->M_soalisian->getsoalisian();
		$data['max'] = $max;
		$data['max2'] = $max2;
		$data['soal_ujian'] = $temp;
		$data['soal_ujian2'] = $temp2;
		$data['index'] = 0;
		$data['index2'] = $tempIndex;
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('siswa/ujianSoalIsianGabungan', $data);
	}
	public function next3($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$max = count($temp);
		if ($tempIndex >= $max - 1) {
			$data['index'] = $max - 1;
		} else {
			$data['index'] = $tempIndex + 1;
		}
		$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$max2 = count($temp2);
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$data['max'] = $max;
		$data['max2'] = $max2;
		$data['soal_ujian'] = $temp;
		$data['soal_ujian2'] = $temp2;
		$data['index2'] = 0;
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		if ($tempIndex >= $max - 1) {
			echo "<script type='text/javascript'>alert('Sudah soal terakhir');</script>";
			$this->load->view('siswa/ujianSoalGabungan', $data);
		} else {
			$this->load->view('siswa/ujianSoalGabungan', $data);
		}
	}
	public function next4($id, $tempIndex2)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$max2 = count($temp2);
		if ($tempIndex2 >= $max2 - 1) {
			$data['index2'] = $max2 - 1;
		} else {
			$data['index2'] = $tempIndex2 + 1;
		}
		$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$max = count($temp);
		$data['soal'] = $this->M_soalisian->getsoalisian();
		$data['max'] = $max;
		$data['max2'] = $max2;
		$data['soal_ujian'] = $temp;
		$data['soal_ujian2'] = $temp2;
		$data['index'] = 0;
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		if ($tempIndex2 >= $max2 - 1) {
			echo "<script type='text/javascript'>alert('Sudah soal terakhir');</script>";
			$this->load->view('siswa/ujianSoalIsianGabungan', $data);
		} else {
			$this->load->view('siswa/ujianSoalIsianGabungan', $data);
		}
	}
	public function back3($id, $tempIndex)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$max = count($temp);
		if ($tempIndex <= 0) {
			$data['index'] = 0;
		} else {
			$data['index'] = $tempIndex - 1;
		}
		$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$max2 = 0;
		foreach ($temp2 as $t) {
			$max2 += 1;
		}
		$data['soal'] = $this->M_soalpg->getSoalpg();
		$data['max'] = $max;
		$data['max2'] = $max2;
		$data['soal_ujian'] = $temp;
		$data['soal_ujian2'] = $temp2;
		$data['index2'] = 0;
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('siswa/ujianSoalGabungan', $data);
	}
	public function back4($id, $tempIndex2)
	{
		$this->isAnyLogin();
		date_default_timezone_set('Asia/Jakarta');
		$temp2 = $this->M_ujian_gabungan_has_soal->getSoalIsianByIdUjian($id);
		$max2 = count($temp2);
		if ($tempIndex2 <= 0) {
			$data['index2'] = 0;
		} else {
			$data['index2'] = $tempIndex2 - 1;
		}
		$temp = $this->M_ujian_gabungan_has_soal->getSoalPgByIdUjian($id);
		$max = count($temp);
		$data['soal'] = $this->M_soalisian->getsoalisian();
		$data['max'] = $max;
		$data['max2'] = $max2;
		$data['soal_ujian'] = $temp;
		$data['soal_ujian2'] = $temp2;
		$data['index'] = 0;
		$data['ujian'] = $this->M_ujian->getUjianByID($id);
		$nik = $this->session->nik;
		$data['jawaban'] = $this->M_jawaban_siswa->getJawabanSiswaByNik($nik, $id);
		$data['jawaban_isian'] = $this->M_jawaban_siswa_isian->getJawabanSiswaIsianByNik($nik, $id);
		$this->load->view('siswa/ujianSoalIsianGabungan', $data);
	}
	//End Join Ujian

	//Start Report
	public function dataReport()
	{
		$this->isAnyLogin();
		$nik = $this->session->nik;
		$data['nilai'] = $this->M_nilai->getNilaiByNik($nik);
		$data['ujian'] = $this->M_ujian->getUjian();
		$this->load->view('siswa/dataReport', $data);
	}
	//End Report
}
