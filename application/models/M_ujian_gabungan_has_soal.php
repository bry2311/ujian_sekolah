<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_ujian_gabungan_has_soal extends CI_Model {
    public function add($data){      
        return $this->db->insert('ujian_gabungan_has_soal',$data);
    }
    public function getUjianGabunganHasSoal(){
        $query = $this->db->query("SELECT * FROM ujian_gabungan_has_soal");
        return $query->result();
    } 
    
    public function getUjianGabunganHasSoalById($id){ 
        $this->db->where('id',$id);
        return $this->db->get('ujian_gabungan_has_soal')->row(0);
    }
    public function getUjianGabunganHasSoalByIdUjian($id){ 
        $query = $this->db->query("SELECT * FROM ujian_gabungan_has_soal WHERE id_ujian = $id AND tipe ='Pilihan Ganda'");
        return $query->result();
    }
    public function getCountSoalByIdUjian($id){ 
        $query = $this->db->query("SELECT id FROM ujian_gabungan_has_soal WHERE id_ujian = $id");
        return $query->num_rows();
    }

    public function getUjianGabunganHasSoalByIdUjianIsian($id){ 
        $query = $this->db->query("SELECT * FROM ujian_gabungan_has_soal WHERE id_ujian = $id AND tipe ='Isian'");
        return $query->result();
    }

    public function editUjianGabunganHasSoal($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('ujian_gabungan_has_soal',$data);
    }
    public function delete($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("ujian_gabungan_has_soal");
    }
    public function getSoalPgByIdUjian($id){  
        $query = $this->db->query("SELECT us.*, s.materi, s.kd, s.soal, s.a, s.b , s.c, s.d, s.e, s.kunci_jawaban FROM ujian_gabungan_has_soal us JOIN soalpg s ON us.id_soal = s.id WHERE us.id_ujian = $id AND us.tipe = 'Pilihan Ganda' ORDER BY us.no_soal");
        return $query->result();
    }
    public function getSoalIsianByIdUjian($id){ 
        $query = $this->db->query("SELECT us.*, s.materi, s.kd, s.soal, s.kunci_jawaban1 ,s.kunci_jawaban2,s.kunci_jawaban3,s.kunci_jawaban4,s.kunci_jawaban5 FROM ujian_gabungan_has_soal us JOIN soalisian s ON us.id_soal = s.id WHERE us.id_ujian = $id AND us.tipe = 'Isian' ORDER BY us.no_soal");
        return $query->result();
    }
    public function getUjianPGByIdUjian($id)
	{
		$query = $this->db->query("SELECT us.*, s.materi, s.kd, s.soal, s.a, s.b , s.c, s.d, s.e, s.kunci_jawaban FROM ujian_gabungan_has_soal us JOIN soalpg s ON us.id_soal = s.id WHERE us.id_ujian = $id AND us.tipe = 'Pilihan Ganda'");
		return $query->result();
	}
    public function getUjianIsianByIdUjian($id)
    {
        $query = $this->db->query("SELECT us.*, s.materi, s.kd, s.soal,s.kunci_jawaban1 ,s.kunci_jawaban2,s.kunci_jawaban3,s.kunci_jawaban4,s.kunci_jawaban5  FROM ujian_gabungan_has_soal us JOIN soalisian s ON us.id_soal = s.id WHERE us.id_ujian = $id AND us.tipe = 'Isian'");
        return $query->result();
    }   
}
?>