<?php

require('bsheader.php');
require('bssidebar.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 




<?php


require('mysql_config.php');

$t_name = $_POST['t_name'];

#echo "<h3>delete template  ".$t_name."</h3><p>";


$query_check = "select t_name from template where t_name ='$t_name'";
$result = mysql_query($query_check) or die("can't check template");

if ( !mysql_num_rows($result)){
	
	echo "template Not Exist!<p>";	
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();		
}


$img_path = "/var/lib/libvirt/images";

exec("/usr/bin/ssh -o StrictHostKeyChecking=no root@127.0.0.1 'cd $img_path;
rm -fr $t_name'");

if (file_exists("$img_path/$t_name")){
	echo "can't delete template ".$t_name ."<p>";	
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();
}else{

	$query_delete_template = "DELETE FROM template WHERE t_name ='$t_name' ";
	mysql_query($query_delete_template)or die("mysql can't delete template");
	echo "template <font color=red>".$t_name."</font> delete!";
	
}


?>

</div>
</div>


<?php 

mysql_close();
require('bsfooter.php');
?>
