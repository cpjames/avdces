<?php
require('bsheader.php');
require('bssidebar.php');
require('config.php');
require('mysql_config.php');
?>
<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    

<h3 class="page-header">List Virtual IP Address</h3>

<?php



$net_name = $_POST['net_name'];

echo $net_name;

if ( $net_name ) {
	$query = "select * from net_vip where vip_net_name ='$net_name'";
}else{
	$query = "select * from net_vip where vip_use_name is not null";
}

$result = mysql_query($query);

?>




<div class="table-responsive">

<p>
<form class="navbar-form navbar-left" method="post" action="virtual_ips.php">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Net Name"
			  id="input_net_name" name="net_name" 
			  required data-fv-notempty-message />
            </div>                
<button type="submit" class="btn btn-info">Search</button>
</form>
</p>

<table class="table table-striped">
<thead>
                <tr>
                  <th>vip_mac</th>
                  <th>vip_ip</th>
                  <th>vip_use_vm_name</th>
                  <th>vip_net_name</th>
				 </tr>
</thead>
<tbody>

<?php

for ($i=0; $i<mysql_num_rows($result); $i++){
	$row = mysql_fetch_row($result);
	list($vip_id, $vip_mac, $vip_ip, $vip_use_name, $vip_net_name) = $row;
	echo "<tr>";
	echo "<td>".$vip_mac."</td><td>".$vip_ip."</td><td>".$vip_use_name."</td><td>".$vip_net_name."</td>";
	echo "</tr>";
}





?>


</tbody>
</table>
</div>

<!--

<hr>

<p>
<a href="reflash_ip.php"><button type="button" class="btn btn-primary">Reflash IP</button></a>

<button type="button" class="btn btn-success">Success</button>
<button type="button" class="btn btn-default">Default</button>
<button type="button" class="btn btn-info">Info</button>
<button type="button" class="btn btn-warning">Warning</button>
<button type="button" class="btn btn-link">Link</button>
</p>

-->


</div>
</div>



<?php
mysql_close();
require('bsfooter.php');
?>