<?php

require('bsheader.php');
require('bssidebar.php');
require('mysql_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h4 class="page-header">Create VM From ISO Image</h4>

<?php

$iso_path = "/var/lib/libvirt/images";

echo "<p>iso_path is:  <font color=blue>".$iso_path."</font><p>"

?>


<form class="navbar-form navbar-left" method="post" action="vm_create.php">
<p>
<input type="hidden" name="act" value="iso" />

<input type="text" class="form-control" placeholder="VM Name"
		id="input_vm_name" name="vm_name" required data-fv-notempty-message />
</p>
<p>
<label>CPU Number &nbsp &nbsp </label>
<select name="vm_cpu">
<option value="1">1</option>
<option value="2">2</option>
<option value="4">4</option>
<option value="8">8</option>
<option value="16">16</option>
</select>
</p>
<p>
<label>Memory &nbsp &nbsp </label>
<select name="vm_mem">
<option value="524288">512 MB</option>
<option value="1048576">1024 MB</option>
<option value="2097152">2048 MB</option>
<option value="4194304">4 GB</option>
<option value="8388608">8 GB</option>

</select>
</p>

<p>



</p>
<p>
<label>Disk Size &nbsp &nbsp </label>
<select name="vm_disk_size">
<?php
for ($i=8;$i<=20;$i++){
	echo "<option value='$i'>$i GB</option>";
}
?>
</select>
<p>

<label>ISO &nbsp &nbsp </label>


<select name="iso">
<?php

exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'cd $iso_path;ls *.iso'",$chk);

foreach( $chk as $iso ){
	echo "<option value='$iso'>$iso</option>";
}
?>

</select>
</p>

<p>
<label>VM Network &nbsp &nbsp </label>
<select name="vm_net" id="vm_net">

<?php
$query_net = "select net_name, net_network from net order by net_name";
$result_net = mysql_query($query_net)or die("can't select net");
for ($i=0; $i<mysql_num_rows($result_net);$i++){
$row_net = mysql_fetch_row($result_net);
list($vm_net, $net_network) = $row_net;
echo "<option value=".$vm_net.">".$vm_net." ".$net_network."</option>";
}
?>

</select>
</p>

<p>
<input type="text" class="form-control" placeholder="IP ( 2 ~ 254 )"
		id="input_vm_ip" name="vm_ip" required data-fv-notempty-message />
</p>

<p><button  type="submit" class="btn btn-success">New VM Install</button> 

</form>

</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');?>
