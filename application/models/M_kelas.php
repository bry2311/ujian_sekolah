<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_kelas extends CI_Model {
    public function add($data){      
        return $this->db->insert('kelas',$data);
    } 
    public function getKelas(){
        $query = $this->db->query("SELECT * FROM kelas");
        return $query->result();
    }

    public function getKelasByUnit($unit){
        $query = $this->db->query("SELECT * FROM kelas WHERE unit = '$unit' ");
        return $query->result();
    }

    public function getKelasByClass($class){
        $query = $this->db->query("SELECT * FROM kelas WHERE kelas = '$class' ");
        return $query->result();
    } 

    public function getKelasByClass2($class){
        $query = $this->db->query("SELECT * FROM kelas WHERE nama LIKE '$class%' ORDER BY nama ");
        return $query->result();
    }

    public function getKelasByID($id_kelas){
        $this->db->where('id',$id_kelas);
        return $this->db->get('kelas')->row(0);
    }
    public function edit_kelas($id_kelas,$data)
    {
        $this->db->where('id',$id_kelas);
        return $this->db->update('kelas',$data);
    }
    public function delete($id_kelas)
    {
        $this->db->where("id", $id_kelas);
        $this->db->delete("kelas");
    }
}
?>