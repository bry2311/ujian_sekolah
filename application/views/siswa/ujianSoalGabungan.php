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
	<link href="<?= base_url() ?>/css/font.css" rel="stylesheet">

	<!-- Custom styles for this template-->
	<link href="<?= base_url() ?>/css/sb-admin-2.min.css" rel="stylesheet">
	<?php
	date_default_timezone_set('Asia/Jakarta');
	$tmpId = $ujian->id;
	?>
	<script src="<?= base_url() ?>/vendor/ckeditorF/ckeditor.js"></script>

	<script type="text/javascript">
		// Set the date we're counting down to 
		var wktu = new Date("<?php echo $this->session->waktu; ?>").getTime();
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
			document.getElementById("demo").innerHTML = hours + "h " +
				minutes + "m " + seconds + "s ";
			// If the count down is over, write some text 
			if (distance < 0) {
				clearInterval(x);
				document.getElementById("demo").innerHTML = "EXPIRED";
				document.getElementById("visible").style.visibility = "hidden";
			}
		}, 1000);
	</script>
	<style type="text/css">
		.pic {
			width: 150px;
			height: 120px;
			margin-bottom: 20px;
			margin-right: 20px;
		}

		.picbig {
			position: absolute;
			width: 0px;
			-webkit-transition: width 0.3s linear 0s;
			transation: width 0.3s linear 0s;
			z-index: 10;
		}

		.pic:hover+.picbig {
			width: 250px;
		}

		#myImg {
			border-radius: 5px;
			cursor: pointer;
			transition: 0.3s;
		}

		/* The Modal (background) */
		.modal {
			display: none;
			/* Hidden by default */
			position: fixed;
			/* Stay in place */
			z-index: 1;
			/* Sit on top */
			padding-top: 100px;
			/* Location of the box */
			left: 0;
			top: 0;
			width: 100%;
			/* Full width */
			height: 100%;
			/* Full height */
			overflow: auto;
			/* Enable scroll if needed */
			background-color: rgb(0, 0, 0);
			/* Fallback color */
			background-color: rgba(0, 0, 0, 0.9);
			/* Black w/ opacity */
		}

		/* Modal Content (image) */
		.modal-content {
			margin: auto;
			display: block;
			width: 80%;
			max-width: 700px;
		}

		/* Caption of Modal Image */
		#caption {
			margin: auto;
			display: block;
			width: 80%;
			max-width: 700px;
			text-align: center;
			color: #ccc;
			padding: 10px 0;
			height: 150px;
		}

		/* Add Animation */
		.modal-content,
		#caption {
			-webkit-animation-name: zoom;
			-webkit-animation-duration: 0.6s;
			animation-name: zoom;
			animation-duration: 0.6s;
		}

		@-webkit-keyframes zoom {
			from {
				-webkit-transform: scale(0)
			}

			to {
				-webkit-transform: scale(1)
			}
		}

		@keyframes zoom {
			from {
				transform: scale(0)
			}

			to {
				transform: scale(1)
			}
		}

		/* The Close Button */
		.close {
			position: absolute;
			top: 15px;
			right: 35px;
			color: #f1f1f1;
			font-size: 40px;
			font-weight: bold;
			transition: 0.3s;
		}

		/* 100% Image Width on Smaller Screens */
		@media only screen and (max-width: 700px) {
			.modal-content {
				width: 100%;
			}
		}
	</style>
	<?php
	$tempIndex = $index;
	?>
</head>

<body id="page-top">

	<!-- Page Wrapper -->
	<div id="wrapper">

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">
			<!-- Main Content -->
			<div id="content">
				<!-- Topbar -->
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
					<ul class="navbar-nav ml-auto">
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
						<h1 class="h3 mb-0 text-gray-800" style="font-size:12pt;">Data Ujian</h1>
					</div>
					<?php echo $this->session->waktu; ?>
					<!-- Content Row -->
					<div class="row">
						<!-- Approach -->
						<div class="card shadow mb-4" style="width:100%">
							<div class="card-header py-3">
								<ul class="nav nav-second-level">
									<li>
										<a href="#">
											<div class="card-header py-3">
												<h6 class="m-0 font-weight-bold text-primary">Pilihan Ganda (<?php echo $max; ?>) + Isian (<?php echo $max2; ?>)</h6>
											</div>
										</a>
									</li>
								</ul>
							</div>
							<h2 id="demo" align="center" style="font-size:12pt;"></h2>
							<div class="card-body">
								<p>
									<?php
									if ($tempIndex < $max) {
										for ($i = 0; $i < $max; $i++) {
											foreach ($soal as $s) {
												if ($s->id == $this->session->soal_gabungan[$i] && $tempIndex == $i) {
									?>
													<h2 align="center" style="font-size:12pt;"><?php echo $s->materi; ?></h2>
													<br>
													<div style="white-space: pre-wrap; /* CSS3 */ white-space: -moz-pre-wrap; /* Firefox */ white-space: -pre-wrap; /* Opera <7 */ white-space: -o-pre-wrap;/* Opera 7 */word-wrap: break-word;/* IE */">
														<h2 style="font-size:12pt;"><?php echo $tempIndex + 1 . ". &nbsp;" . $s->soal; ?></h2>
													</div>
													<?php if (isset($s->gambarSoal)) { ?>
														<img id="myImg1" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarSoal; ?>" class="pic">
													<?php } ?>
													<form class="user" action="<?php echo base_url(); ?>siswa/addJawabanSiswaGabungan" method="post">
														<div class="input-group">
															<?php
															$tempJawaban = -1;
															if ($jawaban != null) {
																for ($j = 0; $j < count($jawaban); $j++) {
																	if ($jawaban[$j]->id_soal == $s->id) {
																		$tempJawaban = $j;
																	}
																}
															}
															$ja = "";
															$jb = "";
															$jc = "";
															$jd = "";
															?>
															<?php
															if ($jawaban != null and $tempJawaban != -1) {
																if ($this->session->nik % 2 == 0) {
															?>
																	<div style="white-space: pre-wrap; /* CSS3 */ white-space: -moz-pre-wrap; /* Firefox */ white-space: -pre-wrap; /* Opera <7 */ white-space: -o-pre-wrap;/* Opera 7 */word-wrap: break-word;/* IE */">
																		<table style="table-layout:fixed; width:100%">
																			<?php if ($s->kunci_pg == "E") {
																			?>
																				<tr>
																					<?php $ja = $s->e; ?>
																					<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "E."; ?> <?php echo strval($s->e); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->e)))) {
																																																																							echo "checked";
																																																																						} ?>></td>
																					<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->e; ?></label></td>
																					<td>
																						<?php if (isset($s->gambarE)) { ?>
																							<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarE; ?>" class="pic">
																						<?php } ?>
																					</td>
																				</tr>
																			<?php
																			} else {
																			?>
																				<tr>
																					<?php $ja = $s->a; ?>
																					<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "A."; ?> <?php echo strval($s->a); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->a)))) {
																																																																							echo "checked";
																																																																						} ?>></td>
																					<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->a; ?></label></td>
																					<td>
																						<?php if (isset($s->gambarA)) { ?>
																							<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarA; ?>" class="pic">
																						<?php } ?>
																					</td>
																				</tr>
																			<?php
																			} ?>
																			<tr>
																				<?php $jb = $s->b; ?>
																				<td><label label style="font-size:12pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='B.<?php echo "B."; ?> <?php echo strval($s->b); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->b)))) {
																																																																						echo "checked";
																																																																					} ?>></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->b; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarB)) { ?>
																						<img id="myImg3" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarB; ?>" class="pic">
																					<?php
																					} ?>
																				</td>
																			</tr>
																			<tr>
																				<?php $jc = $s->c; ?>
																				<td><label label style="font-size:12pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='C.<?php echo "C."; ?> <?php echo strval($s->c); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->c)))) {
																																																																						echo "checked";
																																																																					} ?>></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->c; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarC)) { ?>
																						<img id="myImg4" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarC; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																			<tr>
																				<?php $jd = $s->d; ?>
																				<td><label label style="font-size:12pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='D.<?php echo "D."; ?> <?php echo strval($s->d); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->d)))) {
																																																																						echo "checked";
																																																																					} ?>></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->d; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarD)) { ?>
																						<img id="myImg5" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarD; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																			<tr>
																				<td>
																					<div id="visible">
																						<input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
																					</div>
																				</td>
																			</tr>
																		</table>
																	</div>
																<?php
																} else {
																?>
																	<table>
																		<?php if ($s->kunci_pg == "E") {
																		?>
																			<tr>
																				<?php $ja = $s->e; ?>
																				<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "E."; ?> <?php echo strval($s->e); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->e)))) {
																																																																						echo "checked";
																																																																					} ?>></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->e; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarE)) { ?>
																						<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarE; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																		<?php
																		} else {
																		?>
																			<tr>
																				<?php $ja = $s->d; ?>
																				<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "D."; ?> <?php echo strval($s->d); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->d)))) {
																																																																						echo "checked";
																																																																					} ?>></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->d; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarD)) { ?>
																						<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarD; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																		<?php
																		} ?>
																		<tr>
																			<?php $jb = $s->a; ?>
																			<td><label label style="font-size:12pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='B.<?php echo "A."; ?> <?php echo strval($s->a); ?>' <?php if (trim(strtolower(strip_tags($jawaban[$tempJawaban]->jawaban))) == trim(strtolower(strip_tags($s->a)))) {
																																																																					echo "checked";
																																																																				} ?>></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->a; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarA)) { ?>
																					<img id="myImg3" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarA; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<?php $jc = $s->b; ?>
																			<td><label label style="font-size:12pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='C.<?php echo "B."; ?> <?php echo strval($s->b); ?>' <?php if (trim($jawaban[$tempJawaban]->jawaban) == trim($s->b)) {
																																																																					echo "checked";
																																																																				} ?>></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->b; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarB)) { ?>
																					<img id="myImg4" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarB; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<?php $jd = $s->c; ?>
																			<td><label label style="font-size:12pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='D.<?php echo "C."; ?> <?php echo strval($s->c); ?>' <?php if (trim($jawaban[$tempJawaban]->jawaban) == trim($s->c)) {
																																																																					echo "checked";
																																																																				} ?>></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->c; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarC)) { ?>
																					<img id="myImg5" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarC; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<td>
																				<div id="visible">
																					<input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
																				</div>
																			</td>
																		</tr>
																	</table>
																<?php
																}
															} else {
																if ($this->session->nik % 2 == 0) {
																?>
																	<table>
																		<?php if ($s->kunci_pg == "E") {
																		?>
																			<tr>
																				<?php $ja = $s->e; ?>
																				<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "E."; ?> <?php echo strval($s->e); ?>'></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->e; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarE)) { ?>
																						<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarE; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																		<?php
																		} else {
																		?>
																			<tr>
																				<?php $ja = $s->a; ?>
																				<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "A."; ?> <?php echo strval($s->a); ?>'></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->a; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarA)) { ?>
																						<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarA; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																		<?php } ?>
																		<tr>
																			<?php $jb = $s->b; ?>
																			<td><label label style="font-size:12pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='B.<?php echo "B."; ?> <?php echo strval($s->b); ?>'></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->b; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarB)) { ?>
																					<img id="myImg3" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarB; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<?php $jc = $s->c; ?>
																			<td><label label style="font-size:12pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='C.<?php echo "C."; ?> <?php echo strval($s->c); ?>'></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->c; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarC)) { ?>
																					<img id="myImg3" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarC; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<?php $jd = $s->d; ?>
																			<td><label label style="font-size:12pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='D.<?php echo "D."; ?> <?php echo strval($s->d); ?>'></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->d; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarD)) { ?>
																					<img id="myImg4" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarD; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<td>
																				<div id="visible">
																					<input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
																				</div>
																			</td>
																		</tr>
																	</table>
																<?php
																} else {
																?>
																	<table>
																		<?php if ($s->kunci_pg == "E") {
																		?>
																			<tr>
																				<?php $ja = $s->e; ?>
																				<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "E."; ?> <?php echo strval($s->e); ?>'></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->e; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarE)) { ?>
																						<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarE; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																		<?php
																		} else {
																		?>
																			<tr>
																				<?php $ja = $s->d; ?>
																				<td><label label style="font-size:12pt;"><b>A.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='A.<?php echo "D."; ?> <?php echo strval($s->d); ?>'></td>
																				<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->d; ?></label></td>
																				<td>
																					<?php if (isset($s->gambarD)) { ?>
																						<img id="myImg2" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarD; ?>" class="pic">
																					<?php } ?>
																				</td>
																			</tr>
																		<?php
																		} ?>
																		<tr>
																			<?php $jb = $s->a; ?>
																			<td><label label style="font-size:12pt;"><b>B.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='B.<?php echo "A."; ?> <?php echo strval($s->a); ?>'></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->a; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarA)) { ?>
																					<img id="myImg3" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarA; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<?php $jc = $s->b; ?>
																			<td><label label style="font-size:12pt;"><b>C.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='C.<?php echo "B."; ?> <?php echo strval($s->b); ?>'></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->b; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarB)) { ?>
																					<img id="myImg4" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarB; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<?php $jd = $s->c; ?>
																			<td><label label style="font-size:12pt;"><b>D.</b></label> &nbsp; <input type="radio" style="width: 1.5em; height: 1.5em;" name="answer" value='D.<?php echo "C."; ?> <?php echo strval($s->c); ?>'></td>
																			<td>&nbsp;<label style="font-size:12pt;"><?php echo $s->c; ?></label></td>
																			<td>
																				<?php if (isset($s->gambarC)) { ?>
																					<img id="myImg5" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarC; ?>" class="pic">
																				<?php } ?>
																			</td>
																		</tr>
																		<tr>
																			<td>
																				<div id="visible">
																					<input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
																				</div>
																			</td>
																		</tr>
																	</table>
															<?php
																}
															}
															?>
														</div>
														<input type="hidden" name="ja" value='<?php echo $ja; ?>'>
														<input type="hidden" name="jb" value='<?php echo $jb; ?>'>
														<input type="hidden" name="jc" value='<?php echo $jc; ?>'>
														<input type="hidden" name="jd" value='<?php echo $jd; ?>'>
														<input type="hidden" name="id_soal" value="<?php echo $s->id; ?>">
														<input type="hidden" name="id_ujian" value="<?php echo $tmpId; ?>">
														<input type="hidden" name="tempIndex" value="<?php echo $tempIndex; ?>">
													</form>
												<?php
												}
											}
										}
									} else {
										for ($j = 0; $j < $max2; $j++) {
											foreach ($soal2 as $s) {
												if ($s->id == $this->session->soal_gabungan[$j + $max] && $tempIndex == $j + $max) {
												$tempJawaban = -1;
												if ($jawaban_isian != null) {
													for ($k = 0; $k < count($jawaban_isian); $k++) {
														if ($jawaban_isian[$k]->id_soal == $s->id) {
															$tempJawaban = $k;
														}
													}
												}
												?>
													<h2 align="center"><?php echo $s->materi; ?></h2>
													<br>
													<h2><?php echo $tempIndex + 1 . ". &nbsp;" . $s->soal; ?></h2>
													<?php if (isset($s->gambarSoal)) {
													?>
														<img id="myImg1" src="<?php echo base_url(); ?>/assets/img/<?php echo $s->gambarSoal; ?>" class="pic">
													<?php
													}
													?>
													<br>
													<form class="user" action="<?php echo base_url(); ?>siswa/addJawabanSiswaGabungan" method="post" enctype="multipart/form-data">
														<textarea name="jawabanSiswa" style="width:80%" placeholder="Tulis jawaban disini .."><?php if ($jawaban_isian != null and $tempJawaban != -1) {
																																					echo $jawaban_isian[$tempJawaban]->jawaban . "";
																																				} ?></textarea>
														<div id="visible" style="margin-top:20px">
														<h3>Upload Gambar (jika perlu): </h3><input type="file" class="btn btn-warning pull-right" name="gambar" value="Upload"></br></br>
															<?php
															if($tempJawaban !== -1){
																if($jawaban_isian != null and $jawaban_isian[$tempJawaban]->gambar != null){
															?>
															<a href="../../../assets/img/<?php echo $jawaban_isian[$tempJawaban]->gambar; ?>" target="_blank">File</a>
															</br></br>					
															<?php
																}
															}
															?>
														<input class="btn btn-primary pull-right" type="Submit" value="Ok" name="btnSubmit">
														</div>
														<input type="hidden" name="id_soal" value="<?php echo $s->id; ?>">
														<input type="hidden" name="id_ujian" value="<?php echo $tmpId; ?>">
														<input type="hidden" name="tempIndex" value="<?php echo $tempIndex; ?>">
													</form>
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
							<a class="btn btn-primary btn-xs" href="<?= base_url('siswa/newBack/' . $tmpId . "/" . $tempIndex); ?>">
								<i class="fa fa-pencil">Back</i></a> &nbsp;
							<a class="btn btn-primary btn-xs" href="<?= base_url('siswa/newNext/' . $tmpId . "/" . $tempIndex); ?>">
								<i class="fa fa-pencil">Next</i></a> &nbsp;
						</div>
						<a class="btn btn-danger btn-xs" href="<?= base_url('siswa/terminateGabungan/' . $tmpId); ?>" onclick="return confirm('Yakin akan mengumpulkan ujian ini?');">
							<i class="fa fa-pencil">Kumpulkan</i></a>
					</div>
				</div>
				<!-- /.container-fluid -->

			</div>
			<!-- End of Main Content -->
			<div id="myModal" class="modal">
				<span class="close">&times;</span>
				<img class="modal-content" id="img01">
				<div id="caption"></div>
			</div>
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
	// Get the modal

	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var img1 = document.getElementById("myImg1");
	var img2 = document.getElementById("myImg2");
	var img3 = document.getElementById("myImg3");
	var img4 = document.getElementById("myImg4");
	var img5 = document.getElementById("myImg5");
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");
	// Get the <span> element that closes the modal
	var modal = document.getElementById("myModal");
	var span = document.getElementsByClassName("close")[0];

	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
		modal.style.display = "none";
	}
	if (img1 != null) {
		img1.onclick = function() {
			modal.style.display = "block";
			modalImg.src = this.src;
		}
	}
	if (img2 != null) {
		img2.onclick = function() {
			modal.style.display = "block";
			modalImg.src = this.src;
		}
	}
	if (img3 != null) {
		img3.onclick = function() {
			modal.style.display = "block";
			modalImg.src = this.src;
		}
	}
	if (img4 != null) {
		img4.onclick = function() {
			modal.style.display = "block";
			modalImg.src = this.src;
		}
	}
	if (img5 != null) {
		img5.onclick = function() {
			modal.style.display = "block";
			modalImg.src = this.src;
		}
	}
	CKEDITOR.replace('jawabanSiswa');
	CKEDITOR.config.height = 600;
	CKEDITOR.config.width = 1000;
</script>

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
