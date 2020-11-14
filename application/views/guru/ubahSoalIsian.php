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
	<link href="<?= base_url() ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

	<!-- Custom styles for this template-->
	<link href="<?= base_url() ?>/css/sb-admin-2.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>/selectator/fm.selectator.jquery.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:700,400,300" rel="stylesheet" type="text/css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/github-fork-ribbon-css/0.2.0/gh-fork-ribbon.min.css" rel="stylesheet">
	<style>
		#select1,
		#select1_ajax,
		#select2,
		#select3,
		#select7,
		#select5,
		#select6 {
			width: 350px;
			padding: 7px 10px;
		}
	</style>
	<script src="<?= base_url() ?>/vendor/ckeditorF/ckeditor.js"></script>
	<script src="<?= base_url() ?>/selectator/jquery-1.11.0.min.js"></script>
	<script src="<?= base_url() ?>/selectator/fm.selectator.jquery.js"></script>

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
				<a class="nav-link" href="<?= base_url(); ?>login/guru">
					<i class="fas fa-fw fa-tachometer-alt"></i>
					<span>Home</span></a>
			</li>
			<!-- Nav Item - Pages Collapse Menu -->
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url(); ?>guru/dataUser">
					<i class="fas fa-fw fa-address-book"></i>
					<span>Daftar User</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url(); ?>guru/dataGambar">
					<i class="fas fa-fw fa-cog"></i>
					<span>Daftar Gambar</span>
				</a>
			</li>
			<li class="nav-item active">
				<a class="nav-link" href="<?= base_url(); ?>guru/dataSoal">
					<i class="fas fa-fw fa-cog"></i>
					<span>Daftar Soal</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url(); ?>guru/dataUjian">
					<i class="fas fa-fw fa-database"></i>
					<span>Daftar Ujian</span>
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="<?= base_url(); ?>guru/dataReport">
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
							<a class="nav-link dropdown-toggle" href="<?= base_url(); ?>login/logout" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
							<a class="nav-link dropdown-toggle" href="<?= base_url(); ?>login/logout">
								<span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $this->session->nama; ?></span>
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
						<h1 class="h3 mb-0 text-gray-800">Ubah Kelas</h1>
					</div>
					<!-- Content Row -->
					<div class="row">
						<!-- Approach -->
						<div class="card shadow mb-4" style="width:100%">
							<div class="card-header py-3">
								<h6 class="m-0 font-weight-bold text-primary">Ubah Soal</h6>
							</div>
							<div class="card-body">
								<form class="user" action="<?php echo base_url(); ?>guru/simpanUbahSoalIsian" method="post">
									<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
										<tr>
											<td>Materi </td>
											<td><textarea name="materi" style="width:500px"><?php echo $soal->materi; ?></textarea></td>
										</tr>
										<tr>
											<td>Kd </td>
											<td><input type="text" name="kd" value="<?php echo $soal->kd; ?>"></td>
										</tr>
										<tr>
											<td>Soal </td>
											<td><textarea name="soal" style="width:500px"><?php echo $soal->soal; ?></textarea></td>
										</tr>
										<tr>
											<td>Kelas </td>
											<td>
												<select name="kelas">
													<?php if ($this->session->unit == "SMP") {
													?>
														<option value="7" <?php if ($soal->kelas == '7') {
																								echo 'selected';
																							} ?>>7</option>
														<option value="8" <?php if ($soal->kelas == '8') {
																								echo 'selected';
																							} ?>>8</option>
														<option value="9" <?php if ($soal->kelas == '9') {
																								echo 'selected';
																							} ?>>9</option>
													<?php
													} else if ($this->session->unit == "SMA") {
													?>
														<option value="10" <?php if ($soal->kelas == '10') {
																									echo 'selected';
																								} ?>>10</option>
														<option value="11" <?php if ($soal->kelas == '11') {
																									echo 'selected';
																								} ?>>11</option>
														<option value="12" <?php if ($soal->kelas == '12') {
																									echo 'selected';
																								} ?>>12</option>
													<?php
													} else if ($this->session->unit == "SD") {
													?>
														<option value="1" <?php if ($soal->kelas == '1') {
																								echo 'selected';
																							} ?>>1</option>
														<option value="2" <?php if ($soal->kelas == '2') {
																								echo 'selected';
																							} ?>>2</option>
														<option value="3" <?php if ($soal->kelas == '3') {
																								echo 'selected';
																							} ?>>3</option>
														<option value="4" <?php if ($soal->kelas == '4') {
																								echo 'selected';
																							} ?>>4</option>
														<option value="5" <?php if ($soal->kelas == '5') {
																								echo 'selected';
																							} ?>>5</option>
														<option value="6" <?php if ($soal->kelas == '6') {
																								echo 'selected';
																							} ?>>6</option>
													<?php
													} else {
													?>
														<option value="TK">TK</option>
													<?php
													}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td>Bobot </td>
											<td><input type="number" name="bobot" min="0" max="100" value="<?php echo $soal->bobot; ?>"></td>
										</tr>
										<tr>
											<td>Gambar Soal</td>
											<td>
												<select name="gambarSoal" id="select1">
													<option value="">--Tidak ada Gambar--</option>
													<?php
													foreach ($gambar as $g) {
													?>
														<option value="<?php echo $g->link; ?>" data-left="<?= base_url() ?>/assets/img/<?= $g->link; ?>" <?php if ($g->link == $soal->gambarSoal) {
																																																																echo 'selected';
																																																															} ?>><?php echo $g->nama; ?></option>
													<?php
													}
													?>
												</select>
												<input value="activate" id="activate_selectator1" type="button">
											<td>
										</tr>
										<script type="text/javascript">
											$(function() {
												var $activate_selectator = $('#activate_selectator1');
												$activate_selectator.click(function() {
													var $select = $('#select1');
													if ($select.data('selectator') === undefined) {
														$select.selectator({
															labels: {
																search: 'Search here...'
															},
															searchFields: 'value text subtitle right'
														});
														$activate_selectator.val('destroy');
													} else {
														$select.selectator('destroy');
														$activate_selectator.val('activate');
													}
												});
												$activate_selectator.trigger('click');
											});
										</script>
										<tr>
											<td colspan="2"><input class="btn btn-primary pull-right" type="submit" value="submit" name="btnSubmit"></td>
										</tr>
									</table>
									<input type="hidden" name="id" value="<?php echo $soal->id; ?>">
								</form>
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
<script>
	CKEDITOR.replace('soal');
	CKEDITOR.replace('kunci_jawaban1');
	CKEDITOR.replace('kunci_jawaban2');
	CKEDITOR.replace('kunci_jawaban3');
	CKEDITOR.replace('kunci_jawaban4');
	CKEDITOR.replace('kunci_jawaban5');
	CKEDITOR.config.height = 600;
	CKEDITOR.config.width = 1000;
</script>

<script src="<?= base_url() ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url() ?>/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url() ?>/js/sb-admin-2.min.js"></script>
