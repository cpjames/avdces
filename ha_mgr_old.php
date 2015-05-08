<?php

require('bsheader.php');
require('bssidebar.php');
require('mysql_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 

<h3 class="page-header">VM In HAProxy List</h3>

<!--

<a href="create_vm_temp.php">
<button type="button" class="btn btn-primary" >
 HAProxy Name Create</button>
</a>
-->

<div class="table-responsive">

<form class="navbar-form navbar-left" method="POST" action="haproxycfg.php">
<table class="table table-striped">
<thead>
                <tr>
				  <!-- <th><input type="checkbox" name="all value="checked" /></th> -->
				  <th></th>
					<th>h_id</th>
                  <th>h_name</th>
                 
                  <th>h_vm_name</th>
				  <th>h_net</th>
				  <th>h_ip</th>
				</tr>
</thead>
<tbody>

<?php

$query = "select h_id, h_name, h_port, h_vm_name, h_net, h_ip from haproxy order by h_name ";
$result = mysql_query($query);

for ($i=0; $i < mysql_num_rows($result); $i++){
	
	$row = mysql_fetch_row($result);
	list($h_id,$h_name, $h_port, $h_vm_name, $h_net, $h_ip ) = $row;
	echo "<tr><td><input type='checkbox' name='del_id_list[]' value='$h_id' /></td>";
	echo "<td>".$h_id."</td><td>".$h_name."</td><td>".$h_port."</td><td>".$h_vm_name.
	"</td><td>".$h_net."</td><td>" .$h_ip."</td>";
	echo "</tr>";
	
}
	
?>

</tbody>
<input type="hidden" name="act" value="delete" />
</table>
<button type="submit" class="btn btn-danger" name="ha_del">Delete</button>
</form>
<br>
<br>

</div>



<br>
<hr>



<h3 class="page-header">VM Not HAProxy List</h3>

<form class="navbar-form navbar-left" method="POST" action="haproxycfg.php">

<table class="table table-striped">
<thead>
                <tr>
				  <th></th>
		    	  <th>vm_name</th>
				  <th>vm_cpu</th>
				  <th>vm_mem</th>
                  <th>vm_net</th>
                  <th>vm_ip</th>
                </tr>
</thead>
<tbody>

<?php

	$query = "select vm_name, vm_cpu, vm_mem, vm_net, vm_ip from vm  order by vm_net ";
	$result = mysql_query($query) or die("can't select vm");


	for ($i=0; $i < mysql_num_rows($result); $i++){
	
		$row = mysql_fetch_row($result);
		list($vm_name, $vm_cpu, $vm_mem, $vm_net, $vm_ip ) = $row;
	
		$query2 = "select h_vm_name from haproxy where h_vm_name = '$vm_name' ";
		$result2 = mysql_query($query2) or die("can't select haproxy 2");
		$sql_check_vm_name = mysql_fetch_row($result2);
	
		
		if ( !$sql_check_vm_name){
			
			echo "<tr><td><input type='checkbox' name='add_vm_list[]' value='$vm_name' /></td>";
			echo "<td>".$vm_name."</td><td>".$vm_cpu."</td><td>".
			number_format($vm_mem/1024/1024, 2)." GB</td><td>".
			$vm_net."</td><td>" .$vm_ip."</td>";
			echo "</tr>";
		}
	
	}

?>


</tbody>

<input type="hidden" name="act" value="add" />

</table>



 <!--
<p>
 <input type="text" class="form-control" placeholder="HA Name"
	id="input_h_name" name="h_name" required data-fv-notempty-message />
   &nbsp &nbsp
</p>
<p>
	<input type="text" class="form-control" placeholder="Port"
	id="input_h_port" name="h_port" size="5" required data-fv-notempty-message />
	( between 10000 ~ 20000 )
</p>
-->



 
<button type="submit" class="btn btn-success">
 HAProxy VM Additon</button>
</a>

</form>






</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');
?>
