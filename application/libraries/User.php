<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class User extends CI_Model {
		
		public function add($data){
			// $this->db->insert('user',$data);
			return $this->db->insert('user',$data);
		}
		
		public function get(){
			return $this->db->get('user')->result();
		}
		
		public function get_for_login($id){
			$this->db->where('nik',$id);
			$cek=$this->db->get('user')->row(0);
			return $cek;
		}
		
		public function get_user_data($id){
			$this->db->where('nik',$id);
			return $cek=$this->db->get('user')->row(0);
		}


	}