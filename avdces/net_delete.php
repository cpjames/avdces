<?php
require('bsheader.php');
require('bssidebar.php');

?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<?php

#check net exist


require('mysql_config.php');

$net_name = $_POST['net_name'];

	echo "<h3>delete network  ".$net_name."</h3><p>";
	
$query_check = "select vip_use_name from net_vip where vip_use_name is not null and vip_net_name='$net_name'";
$result = mysql_query($query_check);

if ( !mysql_num_rows($result)){
	
	echo "Net Name Not Exist!<p>";	
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();		
}

if ( mysql_num_rows($result) > 1){
	
	echo "Net Name VM Exist!<p>";
	echo mysql_num_rows($result)."<p>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();		
}


echo mysql_num_rows($result)."<p>";



$query_delete_net = "DELETE FROM net WHERE net_name ='$net_name' ";

mysql_query($query_delete_net);

$query_delete_net_vip = "DELETE FROM net_vip WHERE vip_net_name ='$net_name' ";

mysql_query($query_delete_net_vip);

exec("ssh root@127.0.0.1 'virsh -c qemu+ssh://root@127.0.0.1/system net-destroy $net_name'");

exec("ssh root@127.0.0.1 'virsh -c qemu+ssh://root@127.0.0.1/system net-undefine $net_name'");



mysql_close();


?>



</div>


<?php
require('bsfooter.php');
?>