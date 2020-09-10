<!DOCTYPE html>
<html lang="en">

<head> 

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>CBT Talenta School</title>

  <!-- Custom fonts for this template-->
  <link href="<?= base_url()?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="<?= base_url()?>/css/font.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?= base_url()?>/css/sb-admin-2.min.css" rel="stylesheet">
  <?php 
  date_default_timezone_set('Asia/Jakarta');
  $tmpId = $ujian->id;
  ?>
	
<script src="<?= base_url()?>/vendor/ckeditorF/ckeditor.js"></script>
  <script type="text/javascript">
    // Set the date we're counting down to 
    var wktu = new Date("<?php echo $this->session->waktu;?>").getTime();
    var countDownDate = wktu; 

    // Update the count down every 1 second
    var x = setInterval(function() {

      // Get today's date and time
      var now = new Date().getTime();
        
      // Find the distance between now and the count down date
      var distance = countDownDate - now;
        
      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
      // Output the result in an element with id="demo"
      document.getElementById("demo").innerHTML = hours + "h "
      + minutes + "m " + seconds + "s ";  
      // If the count down is over, write some text 
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("demo").innerHTML = "EXPIRED";
        document.getElementById("visible").style.visibility = "hidden";
      }
    }, 1000);
  </script>
    <style type="text/css">
    .pic{
      width:150px;
      height:150px;
      margin-bottom:20px;
      margin-right:20px;
    }
    .picbig{
      position:absolute;
      width:0px;
      -webkit-transition:width 0.3s linear 0s;
      transation:width 0.3s linear 0s;
      z-index:10;
    }

    .pic:hover +.picbig{
      width:250px;
    }
  </style>
  <?php
    $tempIndex = $index;
    for($i=1;$i<=20;$i++){
      $arraySoal[$i] = $i;
    }
  ?>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">TALENTA</div>
      </a>
      <!-- Divider -->
      <hr class="sidebar-divider my-0">
      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="<?= base_url();?>siswa">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Home</span></a>
      </li>

        <li class="nav-item">
        <a class="nav-link" href="<?= base_url();?>siswa/dataReport">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Report</span>
        </a>
        </li>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">
      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->


    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="<?= base_url();?>login/logout" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="<?= base_url();?>login/logout">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $this->session->nama;?></span>
                <i class="fas fa-sign-out-alt fa-sm"></i>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End of Topbar -->


        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Ujian</h1>
          </div>
          <?php echo $this->session->waktu;?>
          <!-- Content Row -->
          <div class="row">
              <!-- Approach -->
              <div class="card shadow mb-4" style="width:100%">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Ujian Soal (<?php echo $tempIndex+1 .' / '. $max;?>)</h6>
                </div>
                <h2 id="demo" align="center"></h2>
                <div class="card-body">
                    <p>
                    <?php
                    if($ujian->jenis == "Pilihan Ganda"){
                      for($i = 0; $i < count($this->session->soal_ujian_random);$i++){    
                      foreach($soal as $s){
                        if($s->id == $this->session->soal_ujian_random[$i] && $tempIndex == $i){
                          ?>
                          <div class="card-body">
                            <h2 align="center"><?php echo $s->materi;?></h2>
                            <br>
                            <h2><?php echo $tempIndex+1 .". &nbsp;" .$s->soal;?></h2>
                            <?php if(isset($s->gambarSoal)){ ?>
                              <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarSoal;?>" class="pic">
                              <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarSoal;?>" class="picbig">
                            <?php } ?>
                            <form class="user" action="<?php echo base_url();?>siswa/addJawabanSiswa" method="post">
                              <div class="input-group">         
                              <?php
                              $tempJawaban = -1;
                              if($jawaban != null){
                                for($j=0;$j<count($jawaban);$j++){
                                  if($jawaban[$j]->id_soal == $s->id){
                                    $tempJawaban = $j;
                                  }
                                }
                              }
                              ?>                   
                              <?php
                              if($jawaban != null AND $tempJawaban != -1){    
                                if($this->session->nik %2 == 0){          
                                ?>
                                  <table>
                                  <?php if(strtolower($s->e) == strtolower($s->kunci_jawaban)){
                                     ?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->e;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->e)){ echo "checked";}?>></td>                                     
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->e;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarE)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <?php 
                                    }else {
                                      ?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->a;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->a)){ echo "checked";}?>></td>                                     
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->a;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarA)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <?php } ?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="B. <?php echo $s->b;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->b)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->b;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarB)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="C. <?php echo $s->c;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->c)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->c;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarC)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="D. <?php echo $s->d;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->d)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->d;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarD)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr> 
                                    <tr>
                                      <td>
                                      <div id="visible">
                                        <input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
                                      <div>
                                      </td>
                                    </tr>                     
                                  </table>       
                              <?php 
                                }else{
                                  ?>
                                  <table>
                                    <?php if($s->e == $s->kunci_jawaban){
                                     ?>
                                     <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->e;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->e)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->e;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarE)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr> 
                                     <?php 
                                    }else {
                                      ?>
                                      <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->d;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->d)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->d;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarD)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr> 
                                      <?php
                                    }?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="B. <?php echo $s->a;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->a)){ echo "checked";}?>></td>                             
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->a;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarA)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="C. <?php echo $s->b;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->b)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->b;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarB)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="D. <?php echo $s->c;?>"
                                      <?php if(trim($jawaban[$tempJawaban]->jawaban) == trim($s->c)){ echo "checked";}?>></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->c;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarC)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td>
                                      <div id="visible">
                                        <input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
                                      <div>
                                      </td>
                                    </tr>                     
                                  </table>   
                                  <?php
                                }
                              }
                              else{
                                if($this->session->nik % 2 == 0){
                                  ?>
                                  <table>
                                  <?php if($s->e == $s->kunci_jawaban){
                                     ?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->e;?>"></td>                                  
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->e;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarE)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <?php 
                                    }else {
                                      ?>
                                      <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->a;?>"></td>                                 
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->a;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarA)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                      </tr>   
                                      <?php } ?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="B. <?php echo $s->b;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->b;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarB)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="C. <?php echo $s->c;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->c;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarC)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="D. <?php echo $s->d;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->d;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarD)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr> 
                                    <tr>
                                      <td>
                                      <div id="visible">
                                        <input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
                                      <div>
                                      </td>
                                    </tr>                     
                                  </table>       
                              <?php 
                                }else{
                                  ?>
                                  <table>
                                    <?php if($s->e == $s->kunci_jawaban){
                                     ?>
                                     <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->e;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->e;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarE)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarE;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr> 
                                     <?php 
                                    }else {
                                      ?>
                                      <tr>
                                      <td><label label style="font-size:22pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="A. <?php echo $s->d;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->d;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarD)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarD;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr> 
                                      <?php
                                    }?>
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="B. <?php echo $s->a;?>"></td>                                
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->a;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarA)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarA;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="C. <?php echo $s->b;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->b;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarB)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarB;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td><label label style="font-size:22pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value="D. <?php echo $s->c;?>"></td>
                                      <td>&nbsp;<label style="font-size:22pt;"><?php echo $s->c;?></label></td>
                                      <td>
                                      <?php if(isset($s->gambarC)){ ?>
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="pic">
                                        <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarC;?>" class="picbig">
                                      <?php } ?>
                                      </td>
                                    </tr>   
                                    <tr>
                                      <td>
                                      <div id="visible">
                                        <input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
                                      <div>
                                      </td>
                                    </tr>                     
                                  </table>
                                  <?php
                                  } 
                                } 
                              ?>           
                              </div>
                              <input type="hidden" name="id_soal" value="<?php echo $s->id;?>">
                              <input type="hidden" name="id_ujian" value="<?php echo $tmpId;?>">
                              <input type="hidden" name="tempIndex" value="<?php echo $tempIndex;?>">
                            </form>
                          </div>
                          <?php
                          }
                        }                     
                      } 
                    }else{
                      for($i = 0; $i < count($this->session->soal_ujian_random);$i++){                      
                        foreach($soal as $s){
                          if($s->id == $this->session->soal_ujian_random[$i] && $tempIndex == $i){
                              $tempJawaban = -1;
                              if($jawaban_isian != null){
                                for($j=0;$j<count($jawaban_isian);$j++){
                                  if($jawaban_isian[$j]->id_soal == $s->id){
                                    $tempJawaban = $j;
                                  }
                                }
                              }
                            ?>  
                            <div class="card-body">
                              <h2 align="center"><?php echo $s->materi;?></h2>
                              <br>
                              <h2><?php echo $tempIndex+1 .". &nbsp;" .$s->soal;?></h2>
                              <?php if(isset($s->gambarSoal)){
                              ?>
                              <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarSoal;?>" class="pic">
                              <img src="<?php echo base_url();?>/assets/img/<?php echo $s->gambarSoal;?>" class="picbig">
                              <?php
                              }
                              ?>
                              <br>
                              <form class="user" action="<?php echo base_url();?>guru/addJawabanSiswaIsian" method="post">
                              <textarea name="jawabanSiswa" style="width:80%" placeholder="Tulis jawaban disini .."> <?php
                                if($jawaban_isian != null AND $tempJawaban != -1){   
                                  echo $jawaban_isian[$tempJawaban]->jawaban;           
                                }
                                ?></textarea>
                              <div id="aaaa" style="margin-top:20px"><input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
                              </div>
                                <input type="hidden" name="id_soal" value="<?php echo $s->id;?>">
                                <input type="hidden" name="id_ujian" value="<?php echo $tmpId;?>">
                                <input type="hidden" name="tempIndex" value="<?php echo $tempIndex;?>">
                              </form>
                            </div>
                            <?php
                          }                     
                        }
                      } 
                    }
                    ?> 
                    </p>
                </div>
              </div>
              <div id="visible3">
                <a class="btn btn-primary btn-xs" href="<?= base_url('siswa/back/'.$tmpId."/".$tempIndex);?>">
                <i class="fa fa-pencil">Back</i></a> &nbsp;
                <a class="btn btn-primary btn-xs" href="<?= base_url('siswa/next/'.$tmpId."/".$tempIndex);?>">
                <i class="fa fa-pencil">Next</i></a> &nbsp;
              </div>
              <a class="btn btn-danger btn-xs" href="<?= base_url('siswa/terminate/'.$tmpId);?>" onclick="return confirm('Yakin akan mengumpulkan ujian ini?');">
              <i class="fa fa-pencil">Kumpulkan</i></a> 
          </div>
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Ujian Sekolah 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
        </div>
      </div>
    </div>
  </div>

</body>

</html>
<script>
		CKEDITOR.replace('jawabanSiswa');
		CKEDITOR.config.height = 600;
		CKEDITOR.config.width = 1000;
</script>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url()?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url()?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url()?>/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url()?>/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url()?>/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url()?>/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url()?>/js/demo/datatables-demo.js"></script>
