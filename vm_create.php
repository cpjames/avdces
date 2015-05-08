<?php

require('bsheader.php');
require('bssidebar.php');
require('config.php');
require('mysql_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 

<h4 class="page-header">vm create php</h4>

<?php

$act = $_POST['act'];
echo "create type: ".$act."<br>";



#check vm_name 
$vm_name = $_POST['vm_name'];

$sql_check_vm_name = 
mysql_fetch_row(mysql_query("select * from vm where vm_name='$vm_name'"));

if ($sql_check_vm_name){
	
	echo "VM Name ".$vm_name." Exist!<p>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();	
}


$vm_cpu = $_POST['vm_cpu'];
$vm_mem = $_POST['vm_mem'];

$vm_net = $_POST['vm_net'];
$vm_ip = $_POST['vm_ip'];


#needs check network

#echo $vm_net ;




# check ip 
#echo $vm_ip."<br>";

$result_net_ip = 
mysql_query("select net_network from net where net_name='$vm_net'")
or die("can't select net for ip");

$row_net_ip = mysql_fetch_row($result_net_ip);
$vm_ip = $row_net_ip[0].".".$vm_ip;

#check ip used 

$sql_check_ip = 
mysql_fetch_row(mysql_query("select vip_use_name from net_vip where vip_ip='$vm_ip'"));

if ($sql_check_ip[0]){
	
	echo "IP ".$vm_ip." used!<p>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();	
	
}


#vm mac 

$sql_vm_mac = mysql_fetch_row(mysql_query("select vip_mac from net_vip where vip_ip='$vm_ip'"));

$vm_mac = $sql_vm_mac[0];

if ($act == "template"){
	
	$t_name = $_POST['t_name'];
	
	# check template info 
	
	$sql_template = mysql_fetch_row(mysql_query(
	"select t_disk_size, t_os, t_path from template where t_name ='$t_name'"));
	
	list($vm_disk_size, $vm_os, $img_path) = $sql_template;
	

	#copy temple as new vm
	
	echo "creating img file ....<br>";
	
	#system("ssh root@127.0.0.1 'rm -fr $img_path/$vm_name'");
	system("ssh root@127.0.0.1 'cd $img_path;cp -p $t_name $vm_name.qcow2'");	
	
	if ( is_file($img_path."/".$vm_name.".qcow2" )){
		$vm_disk_file = $vm_name;
		echo "done!<br>";
	}else{
		echo "create img file failed !<br>" ;
		exit();
	}
	
	echo $img_path."/".$vm_name.".qcow2<br>";
	
	#create xml by function
	#create_vm_xml($vm_name, $vm_cpu, $vm_mem, $vm_os, $vm_disk_file, 
	#$vm_net, $vm_mac, $vm_vnc_port);
	
	echo "create vm xml file<br>";
	
	$vm_xml = create_vm_xml($vm_name, $vm_cpu, $vm_mem, $vm_os, $vm_disk_file, 
	$vm_net, $vm_mac);
	
	echo "<pre>".htmlspecialchars($vm_xml)."</pre>";
	
	#define vm xml 
	
	$res = $lv->domain_define($vm_xml);
		
	system("ssh root@127.0.0.1 'chmod 644 /etc/libvirt/qemu/$vm_name.xml' ");
	
	$lv->domain_start($vm_name);
	
	#wait the vm booting
	
	echo "starting vm";
	
	ob_flush();
	flush();
	
	sleep(45);
	
	
	# change vm host name
	
	system("ssh -o StrictHostKeyChecking=no root@$vm_ip 'echo NETWORKING=yes > /etc/sysconfig/network'");
	system("ssh -o StrictHostKeyChecking=no root@$vm_ip 'echo HOSTNAME=$vm_name >> /etc/sysconfig/network'");
	system ("ssh -o StrictHostKeyChecking=no root@$vm_ip 'reboot'");
	
	#print_r($res);
	
	$uuid = libvirt_domain_get_uuid_string($res);
	
	# insert  new vm  data to DB
	
	#table vm 
	
	
	#$vm_name $vm_cpu $vm_mem  $vm_os  $t_name $vm_net $vm_ip $vm_mac $vm_disk_size;
	
	#'$vm_name', '$vm_cpu', '$vm_mem','$vm_os','$t_name','$vm_net','$vm_ip','$vm_mac','$vm_disk_size';
	
	
	$query_vm_ins = "insert into 
		vm (uuid, vm_name, vm_cpu, vm_mem, vm_disk_size, 
		vm_disk_file, vm_mac, vm_ip, vm_net, vm_os)
		values ( '$uuid','$vm_name','$vm_cpu','$vm_mem','$vm_disk_size',
		'$vm_disk_file.qcow2','$vm_mac','$vm_ip','$vm_net','$vm_os')";
		
		echo $query_vm_ins."<p>";
		
		mysql_query($query_vm_ins) or die("can't insert to vm");
		
		
	
	$query_ip_vip_ins = "update net_vip set vip_use_name = '$vm_name' where vip_mac='$vm_mac' ";
		
	echo $query_ip_vip_ins."<p>";
	
	mysql_query($query_ip_vip_ins) or die("can't insert vm to vip");
	
	
}

?>

</div>
</div>





<?php

function create_vm_xml
($vm_name, $vm_cpu, $vm_mem, $vm_os, $vm_disk_file, 
$vm_net, $vm_mac, $vm_vnc_port="")
{	
	$uuid =  exec("uuidgen");
	/*
	echo $vm_name;
	echo $uuid;
	echo $vm_cpu;
	echo $vm_mem;
	echo $vm_os;
	echo $vm_disk_file;
	echo $vm_mac;
	echo $vm_net;
	echo $vm_vnc_port;
	*/
	
$vm_xml = "
<domain type='kvm'>
<name>$vm_name</name>
<uuid>$uuid</uuid>
<memory unit='KiB'>$vm_mem</memory>
<currentMemory unit='KiB'>$vm_mem</currentMemory>
<vcpu placement='static'>$vm_cpu</vcpu>
<os>
<type arch='x86_64' machine='$vm_os'>hvm</type>
<boot dev='hd'/>
</os>
<features>
<acpi/>
<apic/>
<pae/>
</features>
<clock offset='utc'/>
<on_poweroff>destroy</on_poweroff>
<on_reboot>restart</on_reboot>
<on_crash>restart</on_crash>
<devices>
<emulator>/usr/libexec/qemu-kvm</emulator>
<disk type='file' device='disk'>
<driver name='qemu' type='qcow2' cache='none'/>
<source file='/var/lib/libvirt/images/$vm_disk_file.qcow2'/>
<target dev='vda' bus='virtio'/>
<address type='pci' domain='0x0000' bus='0x00' slot='0x04' function='0x0'/>
</disk>
<controller type='usb' index='0'>
<address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x2'/>
</controller>
<interface type='network'>
<mac address='$vm_mac'/>
<source network='$vm_net'/>
<address type='pci' domain='0x0000' bus='0x00' slot='0x03' function='0x0'/>
</interface>
<serial type='pty'>
<target port='0'/>
</serial>
<console type='pty'>
<target type='serial' port='0'/>
</console>
<input type='tablet' bus='usb'/>
<input type='mouse' bus='ps2'/>";
$vm_xml = $vm_xml."
<graphics type='vnc' autoport='yes' listen='127.0.0.1'>
<listen type='address' address='127.0.0.1'/>
</graphics>
<video>
<model type='cirrus' vram='9216' heads='1'/>
<address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
</video>
<memballoon model='virtio'>
<address type='pci' domain='0x0000' bus='0x00' slot='0x05' function='0x0'/>
</memballoon>
</devices>
</domain>
";
	return $vm_xml;
		
}


?>



<?php 

mysql_close();
require('bsfooter.php');

?>
