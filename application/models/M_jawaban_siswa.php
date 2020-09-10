<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_jawaban_siswa extends CI_Model {
    public function add($data){      
        return $this->db->insert('jawaban_siswa',$data);
    }
    public function getJawabanSiswa(){
        $query = $this->db->query("SELECT * FROM jawaban_siswa");
        return $query->result();
    }
    public function getJawabanSiswaById($id){ 
        $this->db->where('id',$id);
        return $this->db->get('jawaban_siswa')->row(0);
    }
    public function getJawabanSiswaByIdUjian($id){ 
        $query = $this->db->query("SELECT * FROM jawaban_siswa WHERE id_ujian = $id");
        return $query->result();
    }
    public function getJawabanSiswaByNik($nik,$id){ 
        $query = $this->db->query("SELECT * FROM jawaban_siswa WHERE id_ujian = $id AND nik = $nik");
        return $query->result(); 
    }

    public function getJawabanSiswaByNik2($nik,$id){ 
        $query = $this->db->query("SELECT js.*,s.soal FROM jawaban_siswa js JOIN soalpg s ON js.id_soal = s.id WHERE js.id_ujian = $id AND js.nik = $nik");
        return $query->result(); 
    }
    public function getJawabanSiswaIsianByNik2($nik,$id){ 
        $query = $this->db->query("SELECT js.*,s.soal FROM jawaban_siswa_isian js JOIN soalisian s ON js.id_soal = s.id WHERE js.id_ujian = $id AND js.nik = $nik");
        return $query->result(); 
    }
    public function deleteJawabanSiswaByNik($nik,$id){ 
        // $query = $this->db->query("DELETE FROM jawaban_siswa WHERE id_ujian = $id AND nik= $nik");
        // $query->result(); 
        $this->db->where("id_ujian", $id);
        $this->db->where("nik", $nik);
        $this->db->delete("jawaban_siswa");
    }
    public function getJawabanSiswaByKelas($nik,$id,$kelas){ 
        $query = $this->db->query("SELECT js.*,u.kelas FROM jawaban_siswa js JOIN user u ON js.nik = u.nik WHERE js.id_ujian = $id AND js.nik= $nik AND u.kelas = '$kelas'");
        return $query->result(); 
    }
    public function getJawabanSiswaIsianByKelas($nik,$id,$kelas){ 
        $query = $this->db->query("SELECT js.*,u.kelas FROM jawaban_siswa js JOIN user u ON js.nik = u.nik WHERE js.id_ujian = $id AND js.nik= $nik AND u.kelas = '$kelas'");
        return $query->result(); 
    }
    public function checkJawabanSiswa($id_soal,$id_ujian,$nik){ 
        $query = $this->db->query("SELECT * FROM jawaban_siswa WHERE id_ujian = $id_ujian AND nik = $nik AND id_soal = $id_soal");
        return $query->row();
    }
    public function editJawabanSiswa($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('jawaban_siswa',$data);
    }
    public function deleteJawabanSiswaByIdUjian($id){
        $query = $this->db->query("DELETE FROM jawaban_siswa WHERE id_ujian = $id");
        return $query;
    }
    public function delete($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("jawaban_siswa");
    }
}
?>