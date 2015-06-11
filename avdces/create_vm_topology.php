<?php

require('bsheader.php');
require('bssidebar.php');
require('mysql_config.php');
?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 



<h4 class="page-header">Create VM Topology</h4>
<?php

# test area
/*
$query_net = "select net_name, net_network from net";
$result_net = mysql_query($query_net)or die("can't select net");

for ($i=0; $i<mysql_num_rows($result_net);$i++){
$row_net = mysql_fetch_row($result_net);
list($vm_net, $net_network) = $row_net;
echo $vm_net.$net_network;
}
*/
?>


<form class="navbar-form navbar-left" method="post" action="vm_create.php">
<p>
<input type="hidden" name="act" value="template" />

<input type="text" class="form-control" placeholder="Topology VM Name"
		id="input_vm_name" name="vm_name" required data-fv-notempty-message />
</p>

<p>
<label>VM Number &nbsp &nbsp </label>
<select name="vm_number">
<?php

for ($i=2; $i <=50;$i+=1){
	
	echo "<option value='".$i."'>".$i."</option>";
}

?>
</select>
</p>

<p>
<label>CPU Number &nbsp &nbsp </label>
<select name="vm_cpu">
<option value="1">1</option>
<option value="2">2</option>
<!-- <option value="4">4</option> -->
</select>
</p>
<p>
<label>Memory &nbsp &nbsp </label>
<select name="vm_mem">
<option value="524288">512 MB</option>
<option value="1048576">1024 MB</option>

<!--
<option value="2097152">2048 MB</option>
<option value="4194304">4069 MB</option>
-->
</select>
</p>
<p>
	<label>Template &nbsp &nbsp </label>
<select name="t_name">
<?php
$query_template = "select t_name from template";
$result_template = mysql_query($query_template)or die("can't select template");

for ($i=0; $i<mysql_num_rows($result_template);$i++){
$row_template = mysql_fetch_row($result_template);
list($t_name) = $row_template;
echo "<option value=".$t_name.">".$t_name."</option>";
}
?>

</select>
</p>

<p>
<label>VM Network &nbsp &nbsp </label>
<select name="vm_net">

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


<!--
<p>
<input type="text" class="form-control" placeholder="IP ( 2 ~ 254 )"
		id="input_vm_ip" name="vm_ip" required data-fv-notempty-message />
</p>
-->
<!--
<p>
<label>IP &nbsp &nbsp </label>
<select name="vm_ip">


<?php
/*

for ($i=2; $i<=254; $i++){
	echo "<option value='".$i."'>".$i."</option>";
	
}
*/
?>

</select>
</p>

-->
<p><button  type="submit" class="btn btn-primary">Create VM</button> 


</form>


</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');
?>