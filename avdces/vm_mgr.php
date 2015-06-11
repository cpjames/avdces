<?php

require('bsheader.php');
require('bssidebar.php');
require('config.php');
require('mysql_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h3 class="page-header">List Virtual Machine</h3>


<?php
/*
<pre>
$ci  = $lv->get_connect_information();
if ($ci['hypervisor_maxvcpus'])
echo "Max Virtual CPU : ".$ci['hypervisor_maxvcpus']."    ";

$tmp = $lv->host_get_node_info();
echo "Memory Installed : ".number_format(($tmp['memory'] / 1048576), 2, '.', ' ')."GB    ";

$totdisk = number_format(disk_total_space("/")/1024/1024/1024, 2);
echo "Total Disk : ".$totdisk."GB   ";
</pre>
*/

?>




<form class="navbar-form navbar-left" method="post" action="vm_delete.php">
<div class="table-responsive">
<table class="table table-striped">
<thead>
                <tr>
				  <th></th>
                  <th>vm_name</th>
                  <th>vm_mem</th>
                  <th>vm_cpu</th>
				  <th>vm_disk_size</th>
				  <th>vm_disk_file</th>
				  <th>vm_ip</th>
				  <th>vm_net</th>
				  <th>vm_os</th>
                </tr>
</thead>
<tbody>

<?php

$query = "select vm_name, vm_mem, vm_cpu, vm_disk_size, 
vm_disk_file, vm_ip, vm_net, vm_os from vm";

$result = mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++){
	$row = mysql_fetch_row($result);
	list($vm_name, $vm_mem, $vm_cpu, $vm_disk_size, $vm_disk_file,
	$vm_ip, $vm_net, $vm_os) = $row;
	$ci['hypervisor_maxvcpus'] = $ci['hypervisor_maxvcpus'] - $vm_cpu;
	$tmp['memory'] = $tmp['memory'] - $vm_mem;
	
	echo "<tr>";
	
	$sql_chk = "select * from haproxy where h_vm_name = '$vm_name'";
	$res_chk = mysql_query($sql_chk);
	$row_chk = mysql_fetch_row($res_chk);
	
	if (!$row_chk){
		echo "<td><input type='checkbox' name='del_vm_list[]' value='$vm_name' /></td>";
	}else{
		echo "<tr><td></td>";
	}
	
	echo "<td>".$vm_name."</td><td>".number_format($vm_mem/1024/1024, 2)." GB</td><td>".$vm_cpu."</td><td>".$vm_disk_size.
	" GB</td><td>".$vm_disk_file."</td><td>".$vm_ip."</td><td>".$vm_net."</td><td>".$vm_os."</td>";
	echo "</tr>";
}

?>

</tbody>
</table>


<!--
	   <input type="text" class="form-control" placeholder="VM Name to Delete"
			  id="input_vm_name" name="vm_name" 
			  required data-fv-notempty-message />
-->
 &nbsp   &nbsp                      
<button type="submit" class="btn btn-danger">Delete VM</button>
</form>
</div>

<hr>

<br>


<div class="navbar-form navbar-left">
<?php
/*
echo "<pre>";
echo "CPU Free : ".$ci['hypervisor_maxvcpus']."    ";
echo "Memory Free : ".number_format($tmp['memory'] / 1048576,2). "GB    ";
$df = number_format(disk_free_space("/")/1024/1024/1024, 2);
echo "Disk Free : ".$df."GB<br>";
echo "</pre>";
*/
?>

<a href="create_vm_iso.php">
<button type="button" class="btn btn-success"> Create A VM From ISO </button>
</a>
&nbsp 
<a href="create_vm_temp.php">
<button type="button" class="btn btn-primary">
 Create A VM From Template  </button>
</a>

</div>



</div>
</div>


<?php 

mysql_close();
require('bsfooter.php');

?>
