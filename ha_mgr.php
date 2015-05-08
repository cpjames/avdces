<?php

require('bsheader.php');
require('bssidebar.php');
require('mysql_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 

<!--

<a href="create_vm_temp.php">
<button type="button" class="btn btn-primary" >
 HAProxy Name Create</button>
</a>
-->

<h3 class="page-header">HAProxy Manage Page</h3>

<h4 class="page-header">HAProxy Group List</h4>


<div class="table-responsive">

<form class="navbar-form navbar-left" method="POST" action="haproxycfg.php">
<table class="table table-striped">
<thead>
                <tr>
				  <!-- <th><input type="checkbox" name="all value="checked" /></th> -->
				  <th></th>
					<th>hg_id</th>
					<th>hg_name</th>
					<th>hg_port</th>
					<th>hg_mode</th>
				<!--<th>hg_net</th>-->
					
				</tr>
</thead>
<tbody>

<?php

$query = "select hg_id, hg_name, hg_port, hg_mode, hg_net from hagroup order by hg_id ";
$result = mysql_query($query);

for ($ig=0; $ig < mysql_num_rows($result); $ig++){
	
	$row = mysql_fetch_row($result);
	list($hg_id,$hg_name, $hg_port, $hg_mode, $hg_net) = $row;
	
	$sql_chk = "select * from haproxy where h_name = '$hg_name'";
	$res_chk = mysql_query($sql_chk);
	$row_chk = mysql_fetch_row($res_chk);
	
	if (!$row_chk){
		echo "<tr><td><input type='checkbox' name='del_gid_list[]' value='$hg_id' /></td>";
	}else{
		echo "<tr><td></td>";
	}
	
	echo "<td>".$hg_id."</td><td>".$hg_name."</td><td>".$hg_port.
	"</td><td>".$hg_mode."</td><td>".$hg_net."</td>";
	echo "</tr>";
	
}
	
?>

</tbody>
<input type="hidden" name="act" value="hagroup_del" />
</table>
<p>
<button type="submit" class="btn btn-danger" >Delete HA Group</button>
</p>
</form>
</div>

<!-- end haproxy group list -->


<div class="table-responsive">
<form class="navbar-form navbar-left" method="POST" action="haproxycfg.php">
<table class="table table-striped">
<tbody>
</tbody>
</table>
<p>
<input type="hidden" name="act" value="hagroup_add" />
<input type="text" class="form-control" placeholder="HA Group Name"
	id="input_hg_name" name="hg_name" required data-fv-notempty-message />
   &nbsp 
<input type="text" class="form-control" placeholder="Port"
	id="input_hg_port" name="hg_port" size="5" required data-fv-notempty-message />
	
&nbsp range in 10000 to 20000
</p>
<p>
<lable>balance mode: </lable>
&nbsp 
<select name="hg_mode">
<option value='roundrobin'>roundrobin</option>
<option value='source'>source</option>
<option value='url'>url</option>
</select>
&nbsp 
<button type="submit" class="btn btn-info">
 HA Group Add</button>
 </p>

</form>
</div>
<!-- end haproxy group add-->



<hr>



<h4 class="page-header">VM In HAProxy List</h4>

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

$query = "select h_id, h_name, h_vm_name, h_net, h_ip from haproxy order by h_name ";
$result = mysql_query($query);

for ($i=0; $i < mysql_num_rows($result); $i++){
	
	$row = mysql_fetch_row($result);
	list($h_id,$h_name, $h_vm_name, $h_net, $h_ip ) = $row;
	echo "<tr><td><input type='checkbox' name='del_id_list[]' value='$h_id' /></td>";
	echo "<td>".$h_id."</td><td>".$h_name."</td><td>".$h_vm_name.
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



<h4 class="page-header">VM Not HAProxy List</h4>

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

<label>HA Proxy Group Name:  </label>

<select name="hg_name">
<?php
$query = "select hg_name from hagroup";
$result = mysql_query($query) or die("can't select hagroup");


for ($k=0; $k < mysql_num_rows($result); $k++){
	$row = mysql_fetch_row($result);
	if (!$row){
		echo "<option value='$hg_name'>you should create HA group first</option>";
	}else{
	list($hg_name) = $row;
		echo "<option value='$hg_name'>$hg_name</option>";
	}
}
?>
</select>
<br>
<p>
<button type="submit" class="btn btn-success">
 Add to HA</button>
</p>
</form>




</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');
?>
