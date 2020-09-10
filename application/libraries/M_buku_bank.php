<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class M_buku_bank extends CI_Model {
		public function insertBukuBank($array){
      		$this->db->query("insert into buku_bank (`user_id`, `bulan`, `tahun`, `tanggal_penyerahan`,`jumlah`) VALUES ('$array[user_id]','$array[bulan]','$array[tahun]','$array[tanggal]','$array[jumlah]')");
		}
		public function getBukuBank(){
			$query = $this->db->query("SELECT * FROM buku_bank");
			return $query->result();
		} 
		public function getBukuBankByTahun($tahun){ 
			$query = $this->db->query("SELECT * FROM buku_bank WHERE tahun = '$tahun'");
			return $query->result();
		}
		public function editBukuBank($id,$data)
		{
			$this->db->where('id',$id);
			return $this->db->update('buku_bank',$data);
		}
		
	}