<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_menu extends CI_Model
{
	public function makeMenu($name)
	{
		$arrayMenu = [
			'aktif' => false,
			'ujian' => false,
			'kelas' => false,
			'user' => false,
			'report' => false,
		];
		foreach ($arrayMenu as $key => $value) {
			if ($key == $name) {
				$arrayMenu[$key] = true;
			}
		}
		return $arrayMenu;
	}
}
