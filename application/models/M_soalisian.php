<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_soalisian extends CI_Model {
    public function add($data){      
        return $this->db->insert('soalisian',$data); 
    }
    public function getsoalisian(){
        $query = $this->db->query("SELECT * FROM soalisian");
        return $query->result();
    } 

    public function getSoalIsianByNik($nik){
      $query = $this->db->query("SELECT * FROM soalisian WHERE nik = $nik");
      return $query->result();
  } 
    
    public function getsoalisianByID($id_soalisian){ 
        $this->db->where('id',$id_soalisian);
        return $this->db->get('soalisian')->row(0);
    }
    public function editsoalisian($id_soalisian,$data)
    { 
        $this->db->where('id',$id_soalisian);
        return $this->db->update('soalisian',$data);
    }

    public function delete($id_soalisian)
    {
        $this->db->where("id", $id_soalisian);
        $this->db->delete("soalisian");
    }
    public function upload_file($filename)
		{
			$this->load->library('upload'); // Load librari upload
			
			$config['upload_path'] = './excel/';
			$config['allowed_types'] = 'xlsx';
			$config['max_size']	= '2048';
			$config['overwrite'] = true;
			$config['file_name'] = $filename;
		  
			$this->upload->initialize($config); // Load konfigurasi uploadnya
			if($this->upload->do_upload('file')){ // Lakukan upload dan Cek jika proses upload berhasil
			  // Jika berhasil :
			  $return = array('result' => 'success', 'file' => $this->upload->data(), 'error' => '');
			  return $return;
			}else{ 
			  // Jika gagal :
			  $return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
			  return $return;
			}
		  } 
		public function insert_multiple($data)
		{
			$this->db->insert_batch('soalisian', $data);
		}   
}
?>