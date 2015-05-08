<?php

require('bsheader.php');
require('bssidebar.php');
require('config.php');
require('mysql_config.php');
?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h3 class="page-header">VM Delete</h3>


<?php

$vm_name = $_POST['vm_name'];


$sql_check_vm_name = 
mysql_fetch_row(mysql_query("select * from vm where vm_name='$vm_name'"));

if (!$sql_check_vm_name){
	
	echo "VM Name ".$vm_name." Not Exist!<p>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();	
}



#delete from db ip 

$sql_del_vip_ip = "update net_vip set vip_use_name = NULL where vip_use_name='$vm_name' ";

#echo $sql_del_vip_ip."<br>";

mysql_query($sql_del_vip_ip) or die("can't update net_vip");


#force shut down vm

$ret = $lv->domain_destroy($vm_name) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
#exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'virsh destroy '$vm_name' ");
echo $test;
# delete image

$img_path = "/var/lib/libvirt/images";

exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'rm -fr $img_path/$vm_name*.qcow2'");
#echo $test2;

#remove from libvirt

exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'virsh undefine $vm_name'");

# delete from db

$sql_del_vm = "delete from vm where vm_name = '$vm_name'";

mysql_query($sql_del_vm)or die("sql can't delete vm $vm_name");


echo $vm_name." deleted!";









?>


</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');
?>
