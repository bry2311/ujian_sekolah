<?php
public function add($data){      
        return $this->db->insert('gambar',$data);
    }