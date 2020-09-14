<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_user extends CI_Model
{
	public function getUser()
	{
		$query = $this->db->query("SELECT * FROM user");
		return $query->result();
	}
	public function getAllUserOnline()
	{
		$query = $this->db->query("SELECT * FROM user WHERE last_status != 0");
		return $query->result();
	}
	public function getAllUserByKelas($kelas)
	{
		$query = $this->db->query("SELECT no_absen,nama,jenis_kelamin,kelas,nik FROM user WHERE kelas LIKE '$kelas%' AND aktif = 1 ORDER BY kelas, no_absen");
		return $query->result();
	}
	public function login($data)
	{
		$query = $this->db->query("SELECT * FROM user WHERE nik='$data[nik]' AND password='$data[password]' LIMIT 1");
		return $query->row();
	}
	public function updateStatusLogin($nik)
	{
		$this->db->set('last_status', '1');
		$this->db->set('last_active', @date('Y-m-d H:i:s'));
		$this->db->where('nik', $nik);
		$this->db->update('user');
	}
	public function updateStatusLogout($nik)
	{
		$this->db->set('last_status', '0');
		$this->db->set('last_ujian', '0');
		$this->db->set('last_active', @date('Y-m-d H:i:s'));
		$this->db->where('nik', $nik);
		$this->db->update('user');
	}
	public function updateStatusUjian($nik, $id)
	{
		$this->db->set('last_status', '2');
		$this->db->set('last_ujian', $id);
		$this->db->set('last_active', @date('Y-m-d H:i:s'));
		$this->db->where('nik', $nik);
		$this->db->update('user');
	}
	public function add($data)
	{
		return $this->db->insert('user', $data);
	}
	public function getUserById($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('user')->row(0);
	}
	public function getUserByNik($id)
	{
		$this->db->where('nik', $id);
		return $this->db->get('user')->row(0);
	}
	public function edit_user($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('user', $data);
	}
	public function edit_userByNIK($id, $data)
	{
		$this->db->where('nik', $id);
		return $this->db->update('user', $data);
	}
	public function delete($id)
	{
		$this->db->where("id", $id);
		$this->db->delete("user");
	}
	public function upload_file($filename)
	{
		$this->load->library('upload'); // Load librari upload

		$config['upload_path'] = './application/upload/';
		$config['allowed_types'] = 'xls';
		$config['max_size']	= '2048';
		$config['overwrite'] = true;
		$config['file_name'] = $filename;

		$this->upload->initialize($config); // Load konfigurasi uploadnya
		if ($this->upload->do_upload('file')) { // Lakukan upload dan Cek jika proses upload berhasil
			// Jika berhasil :
			$return = array('result' => 'success', 'file' => $this->upload->data(), 'error' => '');
			return $return;
		} else {
			// Jika gagal :
			$return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
			return $return;
		}
	}
	public function insert_multiple($data)
	{
		$this->db->insert_batch('user', $data);
	}
}
