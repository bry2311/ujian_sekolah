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
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?= base_url()?>/css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    div.scrollmenu {
      overflow: auto;
      white-space: nowrap;
    }
    </style> 
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
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url();?>login/guru">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Home</span></a>
      </li>
      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url();?>guru/dataUser">
          <i class="fas fa-fw fa-address-book"></i>
          <span>Daftar User</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="<?= base_url();?>guru/dataGambar">
          <i class="fas fa-fw fa-cog"></i>
          <span>Daftar Gambar</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="<?= base_url();?>guru/dataSoal">
          <i class="fas fa-fw fa-cog"></i>
          <span>Daftar Soal</span>
        </a>
        </li>
        <li class="nav-item active">
        <a class="nav-link" href="<?= base_url();?>guru/dataUjian">
          <i class="fas fa-fw fa-database"></i>
          <span>Daftar Ujian</span>
        </a>
        </li>

        <li class="nav-item">
        <a class="nav-link" href="<?= base_url();?>guru/dataReport">
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
          <!-- Content Row -->
          <div class="row">
              <!-- Approach -->
              <div class="card shadow mb-4" style="width:100%">
              <div class="card-header py-3">
                  <ul class="nav nav-second-level">
                    <li>
                      <a href="<?= base_url('guru/dataUjian/');?>">
                        <div class="card-header py-3">
                          <h6 class="m-0 font-weight-bold text-primary">Data Ujian</h6>
                        </div>
                      </a>
                    </li>
                    <li>
                      <a href="<?= base_url('guru/dataUjianGabungan/');?>">
                        <div class="card-header py-3">
                          <h6 class="m-0 font-weight-bold text-primary">Data Ujian Gabungan</h6>
                        </div>
                      </a>
                    </li>
                  </ul> 
                </div>
                <a class="btn btn-primary pull-right" style="margin:20px 20px 0px 20px" href="<?= base_url('guru/tambahUjianGabungan/');?>">
                  <i class="fa fa-plus"></i> Tambah Ujian</a>                 
                <div class="card-body">
                <div class="scrollmenu">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>No</th>     
                      <th>Action</th>
                      <th>Nama</th>
                      <th>Mata Pelajaran</th>
                      <th>Materi Pokok</th>
                      <th>BAB</th>
                      <th>Waktu</th>
                      <th>KKM</th>           
                      <th>Persentase</th>           
                      <th>Status</th>
                      <th>Thn Ajaran</th> 
                    </tr> 
                  </thead>
                  <tbody>
                    <?php
                      $no = 1;
                      foreach($ujian as $u){
                    ?>
                    <tr>
                      <td><?php echo $no;?></td>
                      <td> 
                          <a class="btn btn-primary btn-xs" href="<?= base_url('guru/addSoalUjianGabungan/'.$u->id);?>">
                          <i class="fa fa-pencil">Add Soal</i></a> 
                          <a class="btn btn-primary btn-xs" href="<?= base_url('guru/detailUjianGabungan/'.$u->id);?>">
                          <i class="fa fa-pencil">Detail</i></a> 
													<a class="btn btn-warning btn-xs" href="<?= base_url('guru/testUjian/'.$u->id);?>">
                          <i class="fa fa-pencil">Test Ujian</i></a> 
                          <a class="btn btn-primary btn-xs" href="<?= base_url('guru/ubahUjianGabungan/'.$u->id);?>">
                          <i class="fa fa-pencil">Edit</i></a> 
                          <a class="btn btn-danger btn-xs" href="<?= base_url('guru/hapusUjianGabungan/'.$u->id);?>" onclick="return confirm('Yakin akan menghapus data ini?');">
                          <i class="fa fa-trash-o">Hapus</i></a>
                      </td>
                      <td><?php echo $u->nama;?></td>
                      <td><?php echo $u->mata_pelajaran;?></td>
                      <td><?php echo $u->materi_pokok;?></td>
                      <td><?php echo $u->bab;?></td>
                      <td><?php echo $u->waktu;?></td>
                      <td><?php echo $u->kkm;?></td>
                      <td><?php echo $u->persentase_pg . " : " . $u->persentase_isian;?></td>
                      <td><?php echo $u->status;?></td>
                      <td><?php echo $u->tahun_ajaran;?></td>
                    </tr>
                    <?php
                        $no++;
                      }
                    ?>
                  </tbody>
                </table>
                </div>
                </div>
              </div>
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
