<?php
/**
 * Created by PhpStorm. 
 * User: Irman
 * Date: 1/3/2019
 * Time: 1:58 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class M_iuran extends CI_Model{
    /** Perubahan */
    public function insertSPP($nik,$thn)
    {
        $this->db->query("insert into spp (nik,thn_ajaran) value ('$nik','$thn')");
    }

    public function checkLastSpp($nik,$thn)
    {
        $query = $this->db->query("select * from spp where nik = '$nik' AND thn_ajaran= '$thn' limit 1");
        $row = $query->row_array();
        if(!$row){
            return false;
        }
        for($i = 1; $i<=12; $i++){
            if($row['month_'.$i] != 1){
                return 'month_'.$i;
                break;
            }

        }
        return false;
    }
    public function updateSpp($array)
    {
        $count = count($array['spp']);
        $select = $this->db->query("select * from spp where nik = '$array[nik]' order by id desc limit 1 offset 1")
            ->row_array();
        $whole = [];
        if($select){
            for($i = 1; $i<=12; $i++){
                if($select['month_'.$i] == 0){
                    array_push($whole,'month_'.$i);
                }
            }
        }
        if(count($whole)){
            $exp = explode("/",$select['thn_ajaran']);
            $array1 = [
                1 => $exp[0].'-07-01', 2 => $exp[0].'-08-01', 3 => $exp[0].'-09', 4 => $exp[0].'-10-01', 5 => $exp[0].'-11-01', 6 => $exp[0].'-12-01',
                7 => $exp[1].'-01-01', 8 => $exp[1].'-02-01', 9 => $exp[1].'-03', 10 => $exp[1].'-04-01', 11 => $exp[1].'-05-01', 12 => $exp[1].'-06-01'
            ];
            foreach($array['spp'] as $x => $spp){
                $this->db->query("update spp set $whole[$x] = 1 where id = ".$select['id']);
                $choose = $array1[explode("_",$whole[$x])[1]];
                $credit = $this->db->query("select * from tagihan where nik_siswa = '$array[nik]' AND MONTH(untuk_tanggal) = MONTH('$choose') AND YEAR(untuk_tanggal) = YEAR('$choose') AND dibayar_tanggal is null AND status = 0")
                    ->row();
                if($credit){
                    $this->db->query("update tagihan set dibayar_tanggal = '$array[tanggal]',status = 1 where id = ".$credit->id);
                }
                $count = $count - 1;
            }
        }
        if($count > 0){
            $exp = explode("/",$array['thn']);
            $array1 = [
                1 => $exp[0].'-07-01', 2 => $exp[0].'-08-01', 3 => $exp[0].'-09-01', 4 => $exp[0].'-10-01', 5 => $exp[0].'-11-01', 6 => $exp[0].'-12-01',
                7 => $exp[1].'-01-01', 8 => $exp[1].'-02-01', 9 => $exp[1].'-03-01', 10 => $exp[1].'-04-01', 11 => $exp[1].'-05-01', 12 => $exp[1].'-06-01'
            ];
            for($y = 0; $y<$count; $y++){
                $spp = $array['spp'][$y];
                $this->db->query("update spp set $spp = 1 where nik = '$array[nik]' AND thn_ajaran = '$array[thn]'");
                $choose = $array1[explode("_",$spp)[1]];
                $credit = $this->db->query("select * from tagihan where nik_siswa = '$array[nik]' AND MONTH(untuk_tanggal) = MONTH('$choose') AND YEAR(untuk_tanggal) = YEAR('$choose') AND status = 0 limit 1")
                    ->row();
                if($credit){
                    $this->db->query("update tagihan set dibayar_tanggal = '$array[tanggal]',status = 1 where id = ".$credit->id);
                }
            }
            //foreach ($array['spp'] as $spp){
            //    //echo $spp.' ';
            //}
        }
    }

    public function getListSpp($thn,$kelas)
    {
        $query = $this->db->query("select * from spp join siswa on siswa.nik = spp.nik where spp.thn_ajaran = '$thn' AND siswa.kelas = '$kelas' order By siswa.nama_lengkap asc");
        return $query->result();
    }


    public function insert($array)
    {
        $this->db->query("insert into iuran_siswa (`nik_siswa`, `tanggal_iuran`, `untuk_tanggal`, `jumlah`,`titipan`,`credit`) VALUES ('$array[nik_siswa]','$array[tanggal_iuran]','$array[untuk_tanggal]','$array[jumlah]','$array[titipan]','$array[credit]')");
    }

    public function kelas($unit)
    {
        $query = $this->db->query("select * from kelas where unit = '$unit'");

        return $query->result();
    }

    public function getSiswaByKelas($kelas)
    {
        $query = $this->db->query("select * from siswa where kelas = '$kelas'");

        return $query->result();
    }

    public function getNilaiIuran($unit)
    {
        $query = $this->db->query("select * from uang_sekolah where unit = '$unit'");
        return $query->row();
    }

    public function findSiswa($nik)
    {
        $query = $this->db->query("select * from siswa where nik = '$nik'");
        return $query->row();
    }

    public function checkIuran($nik,$tanggal)
    {
        $month = date('m',strtotime($tanggal));
        $query = $this->db->query("select * from iuran_siswa where nik_siswa = '$nik' AND MONTH(untuk_tanggal) = '$month'");
        return $query->row();
    }

    public function masukTunggakan($nik,$tanggal)
    {
        $month = date('m',strtotime($tanggal));
        $year = date('Y',strtotime($tanggal));
        $check = $this->db->query("SELECT * FROM `tagihan` WHERE nik_siswa = '$nik' AND MONTH(untuk_tanggal) = '$month' AND YEAR(untuk_tanggal) = '$year'");
        $row = $check->row();

        if(!$row){
            $this->insertTunggakan([
                'nik' => $nik,
                'untuk_tanggal' => $tanggal
            ]);
        }
    }

    public function insertTunggakan($array)
    {
        $query = $this->db->query("select * from tagihan where nik_siswa = '$array[nik]' AND MONTH(untuk_tanggal) = MONTH('$array[untuk_tanggal]') AND YEAR(untuk_tanggal) = YEAR('$array[untuk_tanggal]')");
        if(!$query->row()){
            $this->db->query("insert into tagihan (`nik_siswa`, `untuk_tanggal`,`ammount`) values ('$array[nik]','$array[untuk_tanggal]','$array[ammount]')");
        }
        //method check sudah bayar / tidak
    }

    public function checkTunggakan($nik)
    {
        //$check2 = $this->db->query("SELECT * FROM `tagihan` WHERE nik_siswa = '$nik' ORDER By id ASC limit 18446744073709551610 offset 1");
        $check = $this->db->query("SELECT * FROM `tagihan` WHERE nik_siswa = '$nik' AND status = 0 ORDER by id asc ");
        $row = $check->result();
        //$select = $check->row();
        return $row;
    }

    public function TunggakanBlnIni($data)
    {
        $total = 0;
        $check = $this->db->query("SELECT * FROM `tagihan` WHERE MONTH(untuk_tanggal) ='$data[month]' AND YEAR(untuk_tanggal) = '$data[year]' AND status = 0 ORDER by id asc ");
        $rows = $check->result();
        if($rows){
            foreach ($rows as $row) {
                $total = $total + $this->checkTunggakanOrNot($row->untuk_tanggal,$row->nik_siswa);
            }
        }
        //$select = $check->row();
        return $total;
    }

    public function checkTunggakanOrNot($date,$nik)
    {
        $select = $this->db->query("select * from iuran_siswa where nik_siswa = '$nik' AND  MONTH(untuk_tanggal) = MONTH('$date') AND YEAR(untuk_tanggal) = YEAR('$date') order by id desc limit 1")
            ->row();
        if(($select) && $select->credit){
            return $select->credit;
        }

        return $this->getNilaiIuran($this->session->unit)->jumlah;
    }

    public function TunggakanSampaiBlnLalu($date)
    {
        $total = 0;
        $data = $this->db->query("select * from tagihan where DATE(untuk_tanggal) < '$date' AND status = 0")
            ->result();
        if($data){
            foreach ($data as $row) {
                $total = $total + $this->checkTunggakanOrNot($row->untuk_tanggal,$row->nik_siswa);
            }
        }
        //$select = $check->row();
        return $total;
    }

    public function WajibBulanIni()
    {
        $total = 0;
        $diskon = 0;
        $sd = $this->db->query("select count(nik) as total_sd from siswa where siswa_unit = 'SD' AND status = 1")
            ->row()
            ->total_sd;
        $smp = $this->db->query("select count(nik) as total_smp from siswa where siswa_unit = 'SMP' AND status = 1")
            ->row()
            ->total_smp;
        $sma = $this->db->query("select count(nik) as total_sma from siswa where siswa_unit = 'SMA' AND status = 1")
            ->row()
            ->total_sma;

        $array = [$sd,$smp,$sma];
        $array_unit = ['SD','SMP','SMA'];
        foreach($array as $i => $val){
            $total = $total + ($this->getNilaiIuran($array_unit[$i])->jumlah * $val);
        }

        $disc = $this->db->query("SELECT *,MAX(created_at) as last_ FROM `riwayat_diskon` GROUP by nik_siswa ORDER by last_ desc")
            ->result();
        if($disc){
            foreach ($disc as $dis) {
                if($this->findSiswa($dis->nik_siswa)->is_diskon){
                    $diskon = $diskon + $dis->nilai_diskon;
                }
            }
        }

        return $total - $diskon;

    }
    public function checkNIlaiIuran($nik,$month_select,$thn,$month)
    {
        //$check2 = $this->db->query("SELECT * FROM `tagihan` WHERE nik_siswa = '$nik' ORDER By id ASC limit 18446744073709551610 offset 1");
        $split = explode("-",$month_select);
        $check = $this->db->query("SELECT sum(jumlah) as jumlah FROM `iuran_siswa` WHERE nik_siswa = '$nik' AND MONTH(tanggal_iuran) = '$split[1]' AND YEAR(tanggal_iuran) = '$split[0]' ORDER by id asc ");
        //$row = $check->result();
        $select = $check->row();
        $check_spp = $this->db->query("select * from spp where nik = '$nik' AND thn_ajaran = '$thn'")
            ->row();
        if($select->jumlah < $this->getNilaiIuran($this->session->unit)->jumlah){
            $check_spp = $this->db->query("select * from spp where nik = '$nik' AND thn_ajaran = '$thn'")
                ->row_array();
            if($check_spp['month_'.$month]){
                $p = (number_format($select->jumlah)) ? number_format($select->jumlah) : '';
                return '<small>'.$p. '</small> &#10004;';
            }
        }
        return number_format($select->jumlah);
    }

    public function deleteTunggakan($i,$id)
    {
        $this->db->query("delete from tagihan where nik_siswa = '$i' AND id = ".$id);
    }


    public function updateSaldo($saldo,$i)
    {
        //$query = $this->db->query("select * from iuran_siswa where nik_siswa = '$i' order by id DESC");
        //$row = $query->row();
        //$this->db->query("update iuran_siswa set titipan = '$saldo' where nik_siswa = '$i' AND id = ".$row->id);
        $this->db->query("update siswa set saldo_us = '$saldo' where nik = '$i'");
    }

    /** Iuran View */

    public function getSiswa($kelas,$thn)
    {
        $query = $this->db->query("Select * from siswa where kelas = '$kelas' AND tahun_ajaran = '$thn'");
        return $query->result();
    }

    public function dataSetIuran($array)
    {
        $query = $this->db->query("select * from iuran_siswa where nik_siswa = '$array[nik]' AND MONTH(untuk_tanggal) = '$array[bulan]' AND YEAR(untuk_tanggal) = '$array[thn]'");
        $query2 = $this->db->query("select * from iuran_siswa where nik_siswa = '$array[nik]' AND YEAR(untuk_tanggal) = '$array[thn]' order by untuk_tanggal desc");
        return [
            'set1' => $query->row(),
            'set2' => $query2->row()
            ];
    }


    /** Tunggakan */
    public function SppTerakhir($nik)
    {
        $query = $this->db->query("select * from spp where nik = '$nik' order by id desc limit 1");
        $row = $query->row_array();
        if(!$row){
            return false;
        }
        for($i = 1; $i<=12; $i++){
            if($row['month_'.$i] != 1){
                return $i;
                break;
            }

        }
        return false;
    }

    protected function do_tunggakan($nik)
    {
        if($this->SppTerakhir());
    }
    public function getSiswaPunyaTunggakan($unit)
    {
        $query = $this->db->query("select siswa.nama_lengkap,siswa.nik,siswa.kelas from siswa 
        join tagihan on tagihan.nik_siswa = siswa.nik 
        WHERE siswa.siswa_unit = '$unit' AND tagihan.status = 0 GROUP by nik order by siswa.kelas");

        return $query->result();
    }

    public function getTunggakanPertama($unit)
    {
        $query = $this->db->query("select * from tagihan join siswa on siswa.nik = tagihan.nik_siswa where siswa.siswa_unit = '$unit' AND tagihan.status = 0  order by tagihan.untuk_tanggal asc");

        return $query->row();
    }

    public function cariTunggakan($nik,$date)
    {
        $array1 = ['Jul' => 1,'Aug' => 2,'Sep' => 3,'Oct' => 4,'Nov' => 5, 'Dec' => 6,'Jan' => 7,'Feb' => 8,'Mar' => 9,'Apr' => 10,'May' => 11,'Jun' => 12];
        if($nik){
            if(intval($date['month']) >= 7){
                $thn_ajaran = $date['year'].'/'.($date['year'] + 1);
                $bln = $array1[date('M',strtotime($date['year'].'-'.$date['month']))];
                $check_spp = $this->db->query("select * from spp where nik = '$nik' AND thn_ajaran = '$thn_ajaran' limit 1")
                    ->row_array();
                if($check_spp['month_'.$bln]){
                    //$this->db->query("delete from tagihan where nik_siswa = '$nik' AND MONTH(untuk_tanggal) = '$date[month]' AND YEAR(untuk_tanggal) = '$date[year]' AND status = 0 ");
                }
            }
            if(intval($date['month']) < 7){
                $bln = $array1[date('M',strtotime($date['year'].'-'.$date['month']))];
                $thn_ajaran = ($date['year'] - 1).'/'.$date['year'];
                $check_spp = $this->db->query("select * from spp where nik = '$nik' AND thn_ajaran = '$thn_ajaran' limit 1")
                    ->row_array();
                if($check_spp['month_'.$bln]){
                    //$this->db->query("delete from tagihan where nik_siswa = '$nik' AND MONTH(untuk_tanggal) = '$date[month]' AND YEAR(untuk_tanggal) = '$date[year]'");
                }
            }
            $query = $this->db->query("SELECT * FROM `tagihan` where nik_siswa = '$nik' AND MONTH(untuk_tanggal) = '$date[month]' AND YEAR(untuk_tanggal) = '$date[year]' AND status = 0 ");
            $import = ($query->row()) ? $this->getNilaiIuran($this->session->unit)->jumlah : "";
        }else{
            $unit = $this->session->unit;
            $query = $this->db->query("select count(tagihan.id) as jml from tagihan join siswa on siswa.nik = tagihan.nik_siswa where siswa.siswa_unit = '$unit' AND MONTH(tagihan.untuk_tanggal) = '$date[month]' AND YEAR(tagihan.untuk_tanggal) = '$date[year]' And tagihan.status = 0");
            $import = ($query->row()->jml) ? ($this->getNilaiIuran($this->session->unit)->jumlah * $query->row()->jml)  : 0;

        }
        return $import;
    }
    public function getTunggakan($thn,$unit)
    {
        $query = $this->db->query("SELECT * FROM `tagihan` join siswa on siswa.nik = tagihan.nik_siswa WHERE siswa.siswa_unit = '$unit' AND siswa.tahun_ajaran = '$thn' AND status = 0 order by siswa.kelas");

        return $query->result();
    }

    /** BAnk */

    public function sum_us($tgl,$titipan = FALSE)
    {
        if(!$titipan){
            $query = $this->db->query("SELECT sum(jumlah) as total FROM `iuran_siswa` where tanggal_iuran = '$tgl'");
        }else{
            $query = $this->db->query("SELECT sum(titipan) as total FROM `iuran_siswa` where tanggal_iuran = '$tgl'");
        }
        return $query->row();
    }

    /** Siswa Keluar Masuk */

    public function getSiswaInOut()
    {
        $query = $this->db->query("select siswa_unit,kelas,CAST(kelas as SIGNED) AS kelas_order  from siswa  group by kelas order by kelas_order asc, kelas asc");
        return $query->result();
    }

    public function countSiswa($kelas,$bulan,$opsi)
    {
        if($opsi){
            $query = $this->db->query("select count(nik) as jumlah from siswa where kelas = '$kelas' and MONTH(tanggal_masuk) != '$bulan' AND MONTH(tanggal_keluar) != '$bulan'");
        }else{
            $query = $this->db->query("select count(nik) as jumlah from siswa where kelas = '$kelas' ");
        }

        return $query->row();
    }

    public function countSiswaMasukKeluar($kelas,$bulan,$opsi)
    {
        if($opsi){
            $query = $this->db->query("select count(nik) as jumlah from siswa where kelas = '$kelas' and MONTH(tanggal_masuk) = '$bulan' ");
        }else{
            $query = $this->db->query("select count(nik) as jumlah from siswa where status = 0 AND kelas = '$kelas' AND MONTH(tanggal_keluar) = '$bulan'");
        }

        return $query->row();
    }

    public function getRiwayatDiskon($kelas,$status,$bulan)
    {
        $query1 = $this->db->query("SELECT count(riwayat_diskon.id) as total_siswa FROM `riwayat_diskon` join siswa on siswa.nik = riwayat_diskon.nik_siswa where siswa.status = 1 AND siswa.is_diskon = 1 AND siswa.kelas = '$kelas' AND riwayat_diskon.status = '$status' group by riwayat_diskon.nik_siswa order by riwayat_diskon.id desc");
        $query2 = $this->db->query("SELECT sum(riwayat_diskon.nilai_diskon) as total_diskon_siswa FROM `riwayat_diskon` join siswa on siswa.nik = riwayat_diskon.nik_siswa where siswa.status = 1 AND siswa.is_diskon = 1 AND siswa.kelas = '$kelas' AND riwayat_diskon.status = '$status' group by riwayat_diskon.nik_siswa order by riwayat_diskon.id desc");
        $query3 = $this->db->query("SELECT sum(riwayat_diskon.nilai_diskon) as total_diskon_siswa_all FROM `riwayat_diskon` join siswa on siswa.nik = riwayat_diskon.nik_siswa where siswa.status = 1 AND siswa.is_diskon = 1 AND siswa.kelas = '$kelas' AND MONTH(riwayat_diskon.created_at) < '$bulan' group by riwayat_diskon.nik_siswa order by riwayat_diskon.id desc");

        return [
            'total_jumlah' => ($query1->row()) ? $query1->row()->total_siswa : 0,
            'total_diskon_siswa' => ($query2->row()) ? $query2->row()->total_diskon_siswa : 0,
            'total_diskon_siswa_all' => ($query3->row()) ? $query3->row()->total_diskon_siswa_all : 0
        ];
    }

public function kelasall() 
    {
        $query = $this->db->query("select * from kelas");

        return $query->result();
    }
    
    public function getSiswaPunyaTunggakanAll()
    {
        $query = $this->db->query("select siswa.nama_lengkap,siswa.nik,siswa.kelas from siswa join tagihan on tagihan.nik_siswa = siswa.nik where tagihan.status = 0 GROUP by nik order by siswa.kelas");

        return $query->result();
    }
    public function getTunggakanPertamaAll()
    {
        $query = $this->db->query("select * from tagihan join siswa on siswa.nik = tagihan.nik_siswa where tagihan.status = 0  order by tagihan.untuk_tanggal asc");

        return $query->row();
    }
	
	public function cariTunggakanMgt($nik,$date)
    {
        if($nik){
            $query = $this->db->query("SELECT * FROM `tagihan` where nik_siswa = '$nik' AND MONTH(untuk_tanggal) = '$date[month]' AND YEAR(untuk_tanggal) = '$date[year]' AND status = 0 ");
            $kls = $this->db->query("select kelas from siswa where nik = '$nik'")->row()->kelas;
            $unit = $this->db->query("select unit from kelas where nama_kelas = '$kls'")->row()->unit;
            $import = ($query->row()) ? $this->getNilaiIuran($unit)->jumlah : "";
            return $import;
        }else{
            
         
        }
        
    }

    public function totalBlnIni()
    {
        return $this->db->query("SELECT sum(jumlah) as total FROM `iuran_siswa` where MONTH(tanggal_iuran) = MONTH(CURRENT_DATE) AND YEAR(tanggal_iuran) = YEAR(CURRENT_DATE)")
            ->row()
            ->total;
    }

    public function rincian_setoran()
    {
        return $this->db->query("SELECT * FROM `tagihan` where MONTH(dibayar_tanggal) = MONTH(CURRENT_DATE) AND YEAR(dibayar_tanggal) = YEAR(CURRENT_DATE) AND status = 1 group by untuk_tanggal")
            ->result();
        //$cari_tunggakan = $this->db->query("SELECT * FROM `tagihan` where MONTH(dibayar_tanggal) = MONTH(CURRENT_DATE) AND YEAR(dibayar_tanggal) = YEAR(CURRENT_DATE) AND status = 1")
        //    ->result();
        //if($cari_tunggakan){
        //    foreach ($cari_tunggakan as $item) {
//
        //    }
        //}
    }

    public function jumlah_rincian_setoran($date)
    {
        $cari = $this->db->query("select * from tagihan where untuk_tanggal = '$date'")
            ->result();
        $total = 0;
        if($cari){
            foreach ($cari as $item) {
                $siswa = $this->findSiswa($item->nik_siswa);
                $spp = $this->getNilaiIuran($siswa->siswa_unit)->jumlah;
                if($siswa->is_diskon){
                    $spp = $spp - $this->nilai_diskon_siswa($item->nik_siswa);
                }
                $total = ($total + $spp);
                if($this->checkTunggakan($item->nik_siswa)){
                    $total = $total - (($siswa->saldo_us) ? $siswa->saldo_us : 0);
                }
            }
        }
        return $total;
    }

    private function nilai_diskon_siswa($nik)
    {
        $query = $this->db->query("SELECT * FROM `riwayat_diskon` where nik_siswa = '$nik' ORDER by id desc limit 1")
            ->row();
        return ($query) ? $query->nilai_diskon : 0;
    }

}