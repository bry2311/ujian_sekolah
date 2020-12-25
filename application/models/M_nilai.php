<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_nilai extends CI_Model
{
	public function add($data)
	{
		return $this->db->insert('nilai', $data);
	}
	public function getnilai()
	{
		$query = $this->db->query("SELECT * FROM nilai");
		return $query->result();
	}

	public function getNilaiByNik2($nik)
	{
		$query = $this->db->query("SELECT n.*,u.nik,u.nama,u.tipe,u.jenis FROM nilai n JOIN ujian u on n.id_ujian = u.id WHERE n.nik = $nik");
		return $query->result();
	}

	public function getNilaiByNik($nik)
	{
		$query = $this->db->query("SELECT n.*,u.nik,u.nama,u.tipe,u.jenis,u.kelas FROM nilai n JOIN ujian u on n.id_ujian = u.id WHERE n.nik = $nik");
		return $query->result();
	}

	public function getNilaiByNik3($nik, $id)
	{
		$query = $this->db->query("SELECT n.*,u.nik,u.nama,u.tipe,u.jenis FROM nilai n JOIN ujian u on n.id_ujian = u.id WHERE n.nik = $nik AND u.id = $id");
		return $query->row();
	}

	public function getNilaiByID($id_nilai)
	{
		$this->db->where('id', $id_nilai);
		return $this->db->get('nilai')->row(0);
	}
	public function getNilaiByClass($class)
	{
		$query = $this->db->query("SELECT * FROM nilai WHERE kelas = '$class'");
		return $query->result();
	}
	public function getNilaiByIdUjianAndKelas($id, $kelas)
	{
		$query = $this->db->query("SELECT n.*,u.nama,u.kelas FROM nilai n JOIN user u ON n.nik = u.nik WHERE n.id_ujian = $id AND u.kelas= '$kelas' ORDER BY u.no_absen ASC");
		return $query->result();
	}
	public function getNilaiByIdUjianAndKelas3($id, $kelas)
	{
		$query = $this->db->query("SELECT n.*,u.nama,u.kelas FROM nilai n JOIN user u ON n.nik = u.nik WHERE n.id_ujian = $id AND u.kelas LIKE '$kelas%' AND u.role = 'Siswa' ");
		return $query->result();
	}
	public function getNilaiByIdUjian($id)
	{
		$query = $this->db->query("SELECT n.*,u.nama,u.kelas FROM nilai n JOIN user u ON n.nik = u.nik WHERE n.id_ujian = $id AND u.aktif = 1 ORDER BY u.kelas ASC");
		return $query->result();
	}
	public function getNilaiByIdUjian2($id)
	{
		$query = $this->db->query("SELECT * FROM nilai n WHERE id_ujian = $id");
		return $query->result();
	}
	public function getNilaiByNikAndIdUjian($nik, $id_ujian)
	{
		$query = $this->db->query("SELECT n.*,u.nama,u.kelas FROM nilai n JOIN user u ON n.nik = u.nik WHERE n.nik = $nik AND n.id_ujian = $id_ujian");
		return $query->result();
	}
	public function editnilai($id_nilai, $data)
	{
		$this->db->where('id', $id_nilai);
		return $this->db->update('nilai', $data);
	}

	public function editNilaiByNikAndIdUjian($nik, $idUjian, $data)
	{
		$this->db->where('nik', $nik);
		$this->db->where('id_ujian', $idUjian);
		return $this->db->update('nilai', $data);
	}

	public function deleteByIdUjian($id)
	{
		$this->db->where("id_ujian", $id);
		$this->db->delete("nilai");
	}

	public function delete($id_nilai)
	{
		$this->db->where("id", $id_nilai);
		$this->db->delete("nilai");
	}
}
