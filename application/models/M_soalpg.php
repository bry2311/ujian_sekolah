<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_soalpg extends CI_Model {
    public function add($data){      
        return $this->db->insert('soalpg',$data); 
    }
    public function getSoalpg(){
        $query = $this->db->query("SELECT * FROM soalpg");
        return $query->result();
    }
    public function getSoalpgByNik($nik){
      $query = $this->db->query("SELECT * FROM soalpg WHERE nik = $nik");
      return $query->result();
  }
    
    public function getSoalpgByID($id_soalpg){ 
        $this->db->where('id',$id_soalpg);
        return $this->db->get('soalpg')->row(0);
    }
    public function editSoalpg($id_soalpg,$data)
    {
        $this->db->where('id',$id_soalpg);
        return $this->db->update('soalpg',$data);
    }

    public function delete($id_soalpg)
    {
        $this->db->where("id", $id_soalpg);
        $this->db->delete("soalpg");
    }
    public function upload_file($filename)
		{
        $this->load->library('upload'); // Load librari upload
        
        $config['upload_path'] = './excel/';
        $config['allowed_types'] = 'xls';
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
			$this->db->insert_batch('soalpg', $data);
		}   
}
?>