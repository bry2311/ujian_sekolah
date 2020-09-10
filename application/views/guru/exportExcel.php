<?php
/**
 * Created by PhpStorm.
 * User: Irman
 * Date: 1/3/2019
 * Time: 2:13 PM
 */
header("Content-type: application/vnd-ms-excel");

header("Content-Disposition: attachment; filename=report.xls");

header("Pragma: no-cache");

header("Expires: 0");
 
?>
<table>
    <tr>
        <td colspan="3"><?php echo $this->session->unit;?> - TALENTA</td>
    </tr>
</table>
<table border="1" width="150%">
<?php $no = 1;?>
<thead>
<tr>
    <th rowspan="2">No</th>
    <th rowspan="2">Kelas</th>
    <th rowspan="2">Nik</th>
    <th rowspan="2">Nilai</th>
</tr>
<tr>        
</tr>
</thead>
<tbody>
    <?php foreach($nilai as $n){?>
        <tr>
            <td><?php echo $no;?></td>
            <td><?php echo $n->kelas;?></td>
            <td><?php echo $n->nik;?></td>
            <td><?php echo $n->hasil;?></td>
        </tr>
    <?php $no ++; } ?>
</tbody>
</table>