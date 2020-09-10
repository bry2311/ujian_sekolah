<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_ujian extends CI_Model
{
	public function add($data)
	{
		return $this->db->insert('ujian', $data);
	}
	public function getUjian()
	{
		$query = $this->db->query("SELECT * FROM ujian");
		return $query->result();
	}

	public function getUjianAktifByKelas($kelas)
	{
		$query = $this->db->query("SELECT * FROM ujian WHERE kelas = $kelas AND status = 'aktif' ");
		return $query->result();
	}

	public function getUjianByNik($nik)
	{
		$query = $this->db->query("SELECT * FROM ujian WHERE nik = $nik");
		return $query->result();
	}

	public function getUjianTunggalByNik($nik)
	{
		$query = $this->db->query("SELECT * FROM ujian WHERE nik = $nik AND tipe = 'Tunggal' ");
		return $query->result();
	}

	public function getUjianGabunganByNik($nik)
	{
		$query = $this->db->query("SELECT * FROM ujian WHERE nik = $nik AND tipe = 'Gabungan' ");
		return $query->result();
	}

	public function getUjianAktif()
	{
		$query = $this->db->query("SELECT * FROM ujian WHERE status='aktif'");
		return $query->result();
	}

	public function getUjianById($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('ujian')->row(0);
	}
	public function editUjian($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('ujian', $data);
	}
	public function delete($id)
	{
		$this->db->where("id", $id);
		$this->db->delete("ujian");
	}
}
