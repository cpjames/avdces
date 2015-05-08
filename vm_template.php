<?php

require('bsheader.php');
require('bssidebar.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h3 class="page-header">VM Template</h3>



<div>

<?php

require('mysql_config.php');

date_default_timezone_set('Asia/Taipei');

#$t = time();
#echo(date("D F d Y",$t));
#echo "<p>";
#echo(date("Y-m-d:T",$t));

echo "<pre>";

$time_now = date("Y/m/d-H:i:s");
echo "System Time : ". $time_now."<br>";

$totdisk = number_format(disk_total_space("/")/1024/1024/1024, 2);
echo "Total Disk : ".$totdisk."GB<br>";
$df = number_format(disk_free_space("/")/1024/1024/1024, 2);
echo "Disk Free : ".$df."GB<br>";

echo "</pre>";
?>

</div>
<hr>
<br>

<h4 class="page-header">List Template</h4>

<div class="table-responsive">



<table class="table table-striped">
<thead>
                <tr>
                  <th>t_name</th>
				  <th>t_disk_size</th>
                  <th>t_date</th>
                  <th>t_describe</th>
				  <th>t_os</th>				  
				</tr>
</thead>
<tbody>


<?php

$query = "select t_name, t_disk_size, t_date, t_describe, t_os from template";
$result = mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++){
	$row = mysql_fetch_row($result);
	list($t_name, $t_disk_size, $t_date, $t_describe, $t_os) = $row;
	echo "<tr>";
	echo "<td>".$t_name."</td><td>".$t_disk_size." GB</td><td>".
	$t_date."</td><td>".$t_describe."</td><td>".$t_os."</td>";
	echo "</tr>";
}

?>

</tbody>
</table>








<form class="navbar-form navbar-left" method="post" action="del_template.php">

       
	   <input type="text" class="form-control" placeholder="Template Name"
			  id="input_t_name" name="t_name" 
			  required data-fv-notempty-message />
                            
<button type="submit" class="btn btn-danger">Del Template</button>
</form>


</div>

<hr>
<br>

<h4 class="page-header">Create Template</h4>

<div class="table-responsive">
<table class="table table-striped">
<thead>
                <tr>
                  <th>vm_name</th>
				  <th>vm_disk_size</th>
				  <th>vm_disk_file</th>                  
                  <th>vm_os</th>				 
				</tr>
</thead>
<tbody>


<?php

$query = "select vm_name, vm_disk_size, vm_disk_file, vm_os from vm";
$result = mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++){
	$row = mysql_fetch_row($result);
	list($vm_name, $vm_disk_size, $vm_disk_file, $vm_os) = $row;
	echo "<tr>";
	echo "<td>".$vm_name."</td><td>".$vm_disk_size." GB</td><td>".
	$vm_disk_file."</td><td>".$vm_os."</td>";
	echo "</tr>";
}

?>



</tbody>
</table>


<form class="navbar-form navbar-left" method="post" action="create_template.php">

       
	   <p><input type="text" class="form-control" placeholder="From VM Name"
			  id="input_vm_name" name="vm_name" 
			  required data-fv-notempty-message />
		</p>
		<p><input type="text" class="form-control" placeholder="To Template Name"
			  id="input_t_name_name" name="t_name" 
			  required data-fv-notempty-message />
		</p>
        <p><input type="text" class="form-control" placeholder="Template Decribe"
			  id="input_t_describe" name="t_describe" /></p>

			  
<button type="submit" class="btn btn-info">Create Template</button>
</form>






</div>


<!-- <h3 class="page-header">ISO</h3> -->


</div>
</div>


<?php 

mysql_close();

require('bsfooter.php');?>
