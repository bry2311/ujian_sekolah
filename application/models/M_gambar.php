<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_gambar extends CI_Model {
    public function add($data){      
        return $this->db->insert('gambar',$data);
    } 
    public function getGambar(){
        $query = $this->db->query("SELECT * FROM gambar");
        return $query->result();
    }
    public function getGambarByNik($nik){
        $query = $this->db->query("SELECT * FROM gambar WHERE nik = $nik");
        return $query->result();
    }
    public function getGambarByID($id_gambar){
        $this->db->where('id',$id_gambar);
        return $this->db->get('gambar')->row(0);
    }
    public function editGambar($id_gambar,$data)
    {
        $this->db->where('id',$id_gambar);
        return $this->db->update('gambar',$data);
    }
    public function delete($id_gambar)
    {
        $this->db->where("id", $id_gambar);
        $this->db->delete("gambar");
    }
}
?>