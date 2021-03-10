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
	<style>
		table,
		td,
		th {
			border: 1px solid #ddd;
			text-align: left;
		}

		table {
			border-collapse: collapse;
			width: 100%;
			overflow-x: auto;
		}

		th,
		td {
			padding: 15px;
		}
	</style>
</head>

<body id="page-top">

	<!-- Page Wrapper -->
	<div id="wrapper">
		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">
			<!-- Main Content -->
			<div id="content">

				<!-- Begin Page Content -->
				<div class="container-fluid">
					<!-- Page Heading -->
					<div class="d-sm-flex align-items-center justify-content-between mb-4">
						<h1 class="h3 mb-0 text-gray-800"></h1>
					</div>
					<!-- Content Row -->
					<div class="row">
						<!-- Approach -->
						<div class="card shadow mb-4" style="width:100%">
							<div class="card-header py-3">
								<h6 class="m-0 font-weight-bold text-primary">Informasi Ujian <?= $ujian->nama; ?></h6>
							</div>
							<div class="card-body">
								<dl>
									<dt>Mata Pelajaran</dt>
									<dd><?= $ujian->mata_pelajaran; ?></dd>
									<dt>Materi Pokok</dt>
									<dd><?= $ujian->materi_pokok; ?></dd>
									<dt>Kelas</dt>
									<dd><?= $ujian->kelas == null ? '-' : $ujian->kelas ?></dd>
									<dt>Tahun Ajaran</dt>
									<dd><?= $ujian->tahun_ajaran; ?></dd>
									<dt>Soal</dt>
								</dl>
								<div style="width: 100%; overflow-x:auto;">
										<table style="width:100%;">
											<thead>
												<th>No</th>
												<th>Materi</th>
												<th>K.d.</th>
												<th>Soal</th>
												<th>A</th>
												<th>B</th>
												<th>C</th>
												<th>D</th>
												<th>E</th>
												<th>Kunci</th>
											</thead>
											<tbody>
												<?php
												$no = 1;
												foreach ($soal_ujian as $su) {
												?>
													<tr>
														<td><?php echo $no; ?></td>
														<td><?php echo $su->materi; ?></td>
														<td><?php echo $su->kd; ?></td>
														<td><?php echo $su->checkSoal ? htmlspecialchars($su->soal) : $su->soal; ?></td>
														<td><?php echo $su->checkA ? htmlspecialchars($su->a) : $su->a; ?></td>
														<td><?php echo $su->checkB ? htmlspecialchars($su->b) : $su->b; ?></td>
														<td><?php echo $su->checkC ? htmlspecialchars($su->c) : $su->c; ?></td>
														<td><?php echo $su->checkD ? htmlspecialchars($su->d) : $su->d; ?></td>
														<td><?php echo $su->checkE ? htmlspecialchars($su->e) : $su->e; ?></td>
														<td><?php echo $su->kunci_jawaban; ?></td>
													</tr>
												<?php
													$no++;
												}
												?>
											</tbody>
										</table>
								</div>
								<table class="table table-bordered"  width="100%" cellspacing="0">
									<thead>
										<tr>
											<th>No Soal</th>
											<th>Materi</th>
											<th>K.d.</th>
											<th>Soal</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$no = 1;
										foreach ($soal_ujian_isian as $su) {
										?>
											<tr>
												<td><?php echo $no; ?></td>
												<td><?php echo $su->materi; ?></td>
												<td><?php echo $su->kd; ?></td>
												<td><?php echo $su->soal; ?></td>
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
<script src="<?= base_url() ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url() ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url() ?>/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url() ?>/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url() ?>/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url() ?>/js/demo/datatables-demo.js"></script>
