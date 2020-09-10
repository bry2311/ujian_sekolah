<?php if (isset($menu['aktif'])) {
?>
	<li class="nav-item <?= $menu['aktif'] ? 'active' : ''; ?>">
		<a class="nav-link" href="<?= base_url(); ?>StatusUser/index">
			<i class="fas fa-fw fa-cog"></i>
			<span>User Aktif</span>
		</a>
	</li>
<?php
}
?>
<?php if (isset($menu['ujian'])) {
?>
	<li class="nav-item <?= $menu['ujian'] ? 'active' : ''; ?>">
		<a class="nav-link" href="<?= base_url(); ?>admin/dataUjian">
			<i class="fas fa-fw fa-cog"></i>
			<span>Data Ujian</span>
		</a>
	</li>
<?php
}
?>
<?php if (isset($menu['kelas'])) {
?>
	<li class="nav-item <?= $menu['kelas'] ? 'active' : ''; ?>">
		<a class="nav-link" href="<?= base_url(); ?>admin/dataKelas">
			<i class="fas fa-fw fa-book"></i>
			<span>Data Kelas</span>
		</a>
	</li>
<?php
}
?>
<?php if (isset($menu['user'])) {
?>
	<li class="nav-item <?= $menu['user'] ? 'active' : ''; ?>">
		<a class="nav-link" href="<?= base_url(); ?>admin/dataUser">
			<i class="fas fa-fw fa-address-card"></i>
			<span>Data User</span>
		</a>
	</li>
<?php
}
?>
<?php if (isset($menu['report'])) {
?>
	<li class="nav-item <?= $menu['report'] ? 'active' : ''; ?>">
		<a class="nav-link" href="<?= base_url(); ?>admin/dataReport">
			<i class="fas fa-fw fa-archive"></i>
			<span>Data Report</span>
		</a>
	</li>
<?php
}
?>
