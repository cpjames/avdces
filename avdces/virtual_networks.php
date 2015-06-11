<?php
require('bsheader.php');
require('bssidebar.php');
require('config.php');
?>
<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    
<h3 class="page-header">List Virtual Network</h3>

<?php

require('mysql_config.php');

$query = "select * from net";
$result = mysql_query($query);

?>

<div class="table-responsive">
<table class="table table-striped">
<thead>
                <tr>
                  <th>net_name</th>
                  <th>forwarding</th>
                  <th>net_network</th>
				  <th>dhcp_start</th>
				  <th>dhcp_stop</th>
				  <th>vm</th>
                </tr>
</thead>
<tbody>

<?php

for ($i=0; $i<mysql_num_rows($result); $i++){
	$row = mysql_fetch_row($result);
	list($uuid, $net_name, $forwording, $net_mac, $net_network, $dhcp_start, $dhcp_stop) = $row;
	echo "<tr>";
	#echo "<td>".$uuid."</td><td>".$net_name."</td><td>".$forwording."</td><td>".$net_mac."</td><td>".$net_network."</td><td>".$dhcp_start."</td><td>".$dhcp_stop."</td>";
	echo "<td>".$net_name."</td><td>".$forwording."</td><td>".$net_network."</td><td>".$dhcp_start."</td><td>".$dhcp_stop."</td>";
	
	echo "</tr>";
}


mysql_close();


?>


</tbody>
</table>

<form class="navbar-form navbar-left" method="post" action="net_delete.php">
<p><a href="addnet.php"><button type="button" class="btn btn-info">Add Network</button></a>
       </p>
	   <input type="text" class="form-control" placeholder="Net Name"
			  id="input_net_name" name="net_name" 
			  required data-fv-notempty-message />
                            
<button type="submit" class="btn btn-danger">Delete Net</button>
</form>
</div>


<hr>
<!--<p><a href="addnet.php"><button type="button" class="btn btn-info">Add Network</button></a></p>-->
<!--
<button type="button" class="btn btn-success">Success</button>
<button type="button" class="btn btn-default">Default</button>
<button type="button" class="btn btn-warning">Warning</button>
<button type="button" class="btn btn-link">Link</button>
  <div class="form-group">
</div>
-->



<!--
<p><a href="virtual_ip_reflash.php"><button type="button" class="btn btn-primary">Reflash IP</button></a></p>
-->


</div>
</div>




<?php
require('bsfooter.php');
?>