<?php

require('bsheader.php');
require('bssidebar.php');
require('config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h4 class="page-header">Create Template</h4>


<?php

$vm_name = $_POST['vm_name'];
$t_name = $_POST['t_name'];
$t_describe = $_POST['t_describe'];

$time_now = date("Y/m/d-H:i:s");

$df = number_format(disk_free_space("/")/1024/1024/1024, 2);

#echo $vm_name. $t_describe;


require('mysql_config.php');

$query_vm = "select vm_name, vm_disk_size, vm_disk_file, vm_ip, vm_os 
from vm where vm_name = '$vm_name'";

$result_vm = mysql_query($query_vm) or die("can't query vm");

$row_vm = mysql_fetch_array($result_vm);

if ( !$row_vm ){
	echo $vm_name. " not exist!<br>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit(); 
}



$query_check = "select * from template where t_name = '$t_name'";

$result = mysql_query($query_check) or die("can't query template");

$row = mysql_fetch_array($result);

if ( $row ){
	echo "template name ".$t_name." exist!<br>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();
}


$query_check = "select * from template where t_name = '$t_name.template'";

$result = mysql_query($query_check) or die("can't query template");

$row = mysql_fetch_array($result);

if ( $row ){
	echo "template name ".$t_name." exist!<br>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();
}




list($vm_name, $vm_disk_size, $vm_disk_file, $vm_ip, $vm_os) = $row_vm;

#check template vm or given key
#echo $vm_ip;


#$tmp = $lv->get_domain_count();

#print_r($tmp);

#$status = $lv->domain_is_running($res, $vm_name);

#$vm_name = "vm0";

$status = $lv->domain_is_running($vm_name);

echo $status;

if (!$status){
	
	echo $vm_name."not runnig!<br>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();
	
}




#echo $vm_name.$vm_disk_size.$vm_disk_file.$vm_os;


#check image size


if ( $vm_disk_size > $df ){
	
	echo "Not enough storage size!<br>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();

}


#path of image

$img_path = "/var/lib/libvirt/images";


# check ssh 

echo $vm_ip."<p>";

$chk_ssh = exec("ssh -o StrictHostKeyChecking=no root@$vm_ip 'echo ok'");

if ( !$chk_ssh){
	
	echo $vm_name." have no ssh key to create template!<br>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();
	
}

#$chk_ssh = "ok";

echo $chk_ssh."<p>";


#check image file type

system("ssh root@127.0.0.1 'chmod 644 $img_path/$vm_disk_file'");

#echo "ssh root@127.0.0.1 'chmod 644 $img_path/$vm_disk_file'<br>";

$chk_img = 
exec("qemu-img info $img_path/$vm_disk_file |grep file|awk -F ' ' '{print $3}'");

#echo "qemu-img info $img_path/$vm_disk_file |grep file|awk -F ' ' '{print $3}'<br>";

#echo $chk_img."<br>";

echo "check: ".$vm_disk_file." type: ".$chk_img."<p>";


# create image

$t_name = $t_name.".template";

echo $t_name."<p>";

if (!$t_describe){
	
	$t_describe = $time_now." ".$vm_name;
	echo $t_describe."<p>";
	
}else{

	echo $t_describe."<p>";
	
}


echo "create image .....";

exec("ssh -o StrictHostKeyChecking=no root@$vm_ip 'cd /etc/udev/rules.d;
rm -fr 70-persistent-net.rules'");

#exec("ssh -o StrictHostKeyChecking=no root@$vm_ip 'yum install -y acpid'");

exec("ssh -o StrictHostKeyChecking=no root@$vm_ip ' chkconfig  acpid --level 35 on'");

exec("ssh root@127.0.0.1 'cd $img_path; 
qemu-img convert -f $chk_img -O qcow2 $vm_disk_file $t_name'");



if (file_exists("$img_path/$t_name")){
	
	echo "ok!<p>";
	
	$qeury_ins_tmp = "insert into 
		template (t_name, t_disk_size, t_os, t_date, t_describe, t_path)
		values ( '$t_name', '$vm_disk_size', '$vm_os', '$time_now', '$t_describe', 
		'$img_path')";
		
		echo $qeury_ins_tmp."<p>";
		
		mysql_query($qeury_ins_tmp) or die("can't create IPs insert to net_vip");
	
	
	
}else{
	
	echo "create template fail!<p>";
	
}


?>


</div>
</div>


<?php 

mysql_close();

require('bsfooter.php');

?>
