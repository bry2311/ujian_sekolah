<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_ujian_has_soal extends CI_Model {
    public function add($data){      
        return $this->db->insert('ujian_has_soal',$data); 
    }
    public function getUjianHasSoal(){      
        $query = $this->db->query("SELECT * FROM ujian_has_soal");
        return $query->result();
    } 
    
    public function getUjianHasSoalById($id){ 
        $this->db->where('id',$id);
        return $this->db->get('ujian_has_soal')->row(0);
    }
    public function getUjianHasSoalByIdUjian2($id){  
        $query = $this->db->query("SELECT * FROM ujian_has_soal WHERE id_ujian = $id");
        return $query->result();
    }
    public function getUjianHasSoalByIdUjian($id){  
        $query = $this->db->query("SELECT us.*, s.materi, s.kd, s.soal, s.a, s.b , s.c, s.d, s.e, s.kunci_jawaban, s.kunci_pg, s.checkSoal, s.checkA, s.checkB, s.checkC, s.checkD, s.checkE FROM ujian_has_soal us JOIN soalpg s ON us.id_soal = s.id WHERE us.id_ujian = $id ORDER BY us.no_soal");
        return $query->result();
    }
    public function getUjianHasSoalByIdUjian3($id){  
        $query = $this->db->query("SELECT DISTINCT id_soal FROM ujian_has_soal WHERE id_ujian = $id");
        return $query->result();
    }
    public function getCountSoalByIdUjian($id){
        $query = $this->db->query("SELECT id FROM ujian_has_soal WHERE id_ujian = $id");
        return $query->num_rows();
    }
    public function getUjianHasSoalIsianByIdUjian($id){ 
        $query = $this->db->query("SELECT us.*, s.materi, s.kd, s.soal, s.bobot FROM ujian_has_soal us JOIN soalisian s ON us.id_soal = s.id WHERE us.id_ujian = $id ORDER BY us.no_soal");
        return $query->result();
    }
    public function editUjianHasSoal($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('ujian_has_soal',$data);
    }

    public function deleteByIdUjian($id)
    {
        $this->db->where("id_ujian", $id);
        $this->db->delete("ujian_has_soal");
    }

    public function delete($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("ujian_has_soal");
    }
}
