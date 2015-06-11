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

#$chk_num = preg_match('/^\d+$/',$vm_name);
#echo $chk_num."<p>";

if (preg_match('/^\d+$/',$vm_name)){
	echo "VM Name ".$vm_name." <p>Guest name can not be only numeric characters<p>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();	
}


$sql_check_vm_name = 
mysql_fetch_row(mysql_query("select * from vm where vm_name='$vm_name'"));

if ($sql_check_vm_name){
	
	echo "VM Name ".$vm_name." Exist!<p>";
	echo '<input type="button" value="goback" onclick="history.back()">';
	exit();	
}

# check ip 
$vm_net = $_POST['vm_net'];
$vm_ip = $_POST['vm_ip'];
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

# vm create

$vm_cpu = $_POST['vm_cpu'];
$vm_mem = $_POST['vm_mem'];

#vm mac 

$sql_vm_mac = mysql_fetch_row(mysql_query("select vip_mac from net_vip where vip_ip='$vm_ip'"));
$vm_mac = $sql_vm_mac[0];


# create from ISO
if ($act == "iso"){
	
	$iso_path = "/var/lib/libvirt/images";
	$vm_disk_file = $vm_name.".qcow2";
	#$vm_mem_mb = number_format($vm_mem/1024);
	$vm_mem_mb = $vm_mem/1024;
	$iso = $_POST['iso'];
	$vm_disk_file = $vm_name.".qcow2";
	$vm_disk_size =  $_POST['vm_disk_size'];
	
	#virt-install create vm
	
	echo "creating vm ".$vm_name." ..<p>";
	ob_flush();
	flush();
	
	exec ("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'virt-install --virt-type kvm --name $vm_name --ram $vm_mem_mb --cdrom=$iso_path/$iso --disk path=$iso_path/$vm_disk_file,size=$vm_disk_size,format=qcow2 --network network=$vm_net,mac=$vm_mac --graphics vnc,listen=0.0.0.0 --noautoconsole --os-type=linux'");
	
	sleep(1);
	
	$xml_path = "/etc/libvirt/qemu";
	$ssh_cmd = "ssh -o StrictHostKeyChecking=no root@127.0.0.1";
	$vm_os_temp = exec("$ssh_cmd 'grep machine $xml_path/$vm_name.xml '");
	$vm_os_temp = explode("'",$vm_os_temp);
	$vm_os = $vm_os_temp[3];
	
	$uuid = exec("$ssh_cmd 'grep uuid $xml_path/$vm_name.xml '");
	$uuid = strip_tags($uuid);
	
	
	#insert vm data to DB
		
	$query_vm_ins = "insert into 
		vm (uuid, vm_name, vm_cpu, vm_mem, vm_disk_size, 
		vm_disk_file, vm_mac, vm_ip, vm_net, vm_os)
		values ( '$uuid','$vm_name','$vm_cpu','$vm_mem','$vm_disk_size',
		'$vm_disk_file','$vm_mac','$vm_ip','$vm_net','$vm_os')";
		
	mysql_query($query_vm_ins) or die("can't insert to vm");
	
	$query_ip_vip_ins = "update net_vip set vip_use_name = '$vm_name' where vip_mac='$vm_mac' ";
		
	mysql_query($query_ip_vip_ins) or die("can't insert vm to vip");
	
	echo "prepare vnc ...<p>";
	ob_flush();
	flush();
	
	$res = $lv->get_domain_by_name($vm_name);
	$dom = $lv->domain_get_info($res);
	$vnc = $lv->domain_get_vnc_port($res);
	$socket_port = $vnc + 3000;
	
	echo "<a href='vnc_auto.php?port=$socket_port&name=$vm_name'><button class='btn btn-info'>Start VNC</button></a>";
	
}


#template

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
	
	#echo "<pre>".htmlspecialchars($vm_xml)."</pre>";
	
	#define vm xml 
	$res = $lv->domain_define($vm_xml);
	
	echo "<p>res: ".$res."<p>";
	
	system("ssh root@127.0.0.1 'chmod 644 /etc/libvirt/qemu/$vm_name.xml' ");
	$lv->domain_start($vm_name);
	
	#wait the vm booting
	echo "starting vm<br>";
	
	#ob_flush();
	#flush();
	#set time
	#sleep(10);
	# change vm host name
	#echo "change vm host name<p>";
	#system("ssh -o StrictHostKeyChecking=no root@$vm_ip 'echo NETWORKING=yes > /etc/sysconfig/network'");
	#system("ssh -o StrictHostKeyChecking=no root@$vm_ip 'echo #HOSTNAME=$vm_name >> /etc/sysconfig/network'");
	#system ("ssh -o StrictHostKeyChecking=no root@$vm_ip 'hostname $vm_name'");
	
	#insert vm data to DB
	
	$uuid = libvirt_domain_get_uuid_string($res);
		
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


if ($act == "quick"){
	
	$vm_num = $_POST['vm_num'];
	$vm_cpu = $_POST['vm_cpu'];
	$vm_mem = $_POST['vm_mem'];
	$t_name = $_POST['t_name'];

	$sql_template = mysql_fetch_row(mysql_query(
	"select t_disk_size, t_os, t_path from template where t_name ='$t_name'"));
	list($vm_disk_size, $vm_os, $img_path) = $sql_template;

	$vm_net = $_POST['vm_net'];
	$hg_name = $_POST['ha_name'];
	
	$vm_date = date("md");
	
	
	for ( $i=1; $i <= $vm_num; $i++ ){
		
		echo "<p>create ".$i." vm<p>";
		
		# define vm_name
		$vm_name = $hg_name."_".$vm_date."_".$i;
		
		#check vm_name
		
		$sql_check_vm_name = 
		mysql_fetch_row(mysql_query("select * from vm where vm_name='$vm_name'"));

		if ($sql_check_vm_name){
			
			echo "VM Name ".$vm_name." Exist!<p>";
			echo '<input type="button" value="goback" onclick="history.back()">';
			exit();	
		}

		echo "<p>vm_name =". $vm_name."<p>";
				
		# create img file
		$vm_disk_file = $vm_name.".qcow2";
		echo "creating img file ....<br>";
		system("ssh root@127.0.0.1 'cd $img_path;cp -p $t_name $vm_name.qcow2'");	
	
		if ( is_file($img_path."/".$vm_name.".qcow2" )){
			$vm_disk_file = $vm_name;
			echo "done!<br>";
		}else{
			echo "create img file failed !<br>" ;
			exit();
		}
	
		echo $img_path."/".$vm_name.".qcow2<br>";
		
		#find a IP
		
		$sql_vip = "select vip_mac, vip_ip from net_vip where vip_net_name='$vm_net' and vip_use_name is Null";
		$result_vip = mysql_query($sql_vip)or die(" $sql is wrong");
		$vip_row = mysql_fetch_row($result_vip);
		list ($vm_mac, $vm_ip) = $vip_row;
		
		# set IP is use
		$query_ip_vip_ins = "update net_vip set vip_use_name = '$vm_name' where vip_mac='$vm_mac' ";
		echo $query_ip_vip_ins."<p>";
		mysql_query($query_ip_vip_ins) or die("can't insert vm to vip");
		
		#create vm xml file 
		$vm_xml = create_vm_xml($vm_name, $vm_cpu, $vm_mem, $vm_os, $vm_disk_file, 
		$vm_net, $vm_mac);
		 
		#define vm xml 
		$res = $lv->domain_define($vm_xml);
		echo "<p>res: ".$res."<p>";
		system("ssh root@127.0.0.1 'chmod 644 /etc/libvirt/qemu/$vm_name.xml' ");
		$lv->domain_start($vm_name);
	
		#wait the vm booting
		echo "starting ".$vm_name."<br>";
	
		# insert to vm table
		$uuid = libvirt_domain_get_uuid_string($res);
		$query_vm_ins = "insert into 
		vm (uuid, vm_name, vm_cpu, vm_mem, vm_disk_size, 
		vm_disk_file, vm_mac, vm_ip, vm_net, vm_os)
		values ( '$uuid','$vm_name','$vm_cpu','$vm_mem','$vm_disk_size',
		'$vm_disk_file.qcow2','$vm_mac','$vm_ip','$vm_net','$vm_os')";
		
		#echo $query_vm_ins."<p>";
		
		mysql_query($query_vm_ins) or die("can't insert to vm");
		
		
		echo "inert into ha group <p>";
		
		$sql_ins_haproxy = 
			"insert into 
			haproxy (h_name, h_vm_name, h_net, h_ip )
			value ('$hg_name', '$vm_name', '$vm_net', '$vm_ip')
			";
		
		mysql_query($sql_ins_haproxy) or die("$check  can't insert to haproxy");
		
		
		
		
		
		ob_flush();
		flush();
	}
	
		echo "refresh haproxy <p>";
		sh_haproxy();
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



function sh_haproxy()
{
	# create new haproxy.cfg
	# global
	exec("echo 'global' > tmp/haproxy.cfg");
	exec("echo 'log /dev/log    local0' >> tmp/haproxy.cfg");
	exec("echo 'log /dev/log    local1 notice' >> tmp/haproxy.cfg");
	exec("echo 'user    avdces' >> tmp/haproxy.cfg");
	exec("echo 'group   avdces' >> tmp/haproxy.cfg");
	exec("echo 'nbproc 1' >> tmp/haproxy.cfg");
	exec("echo 'maxconn 8192' >> tmp/haproxy.cfg");
	exec("echo 'pidfile /var/run/haproxy.pid' >> tmp/haproxy.cfg");
	# defaults
	exec("echo 'defaults' >> tmp/haproxy.cfg");
	exec("echo 'log     global' >> tmp/haproxy.cfg");
	exec("echo 'mode http' >> tmp/haproxy.cfg");
	exec("echo 'option  httplog' >> tmp/haproxy.cfg");
	exec("echo 'option  dontlognull' >> tmp/haproxy.cfg");
	exec("echo 'timeout connect 5000ms' >> tmp/haproxy.cfg");
	exec("echo 'timeout client 50000ms' >> tmp/haproxy.cfg");
	exec("echo 'timeout server 50000ms' >> tmp/haproxy.cfg");
	exec("echo 'retries 3' >> tmp/haproxy.cfg");
	#monitor
	exec("echo 'listen stats 0.0.0.0:8080' >> tmp/haproxy.cfg");
	exec("echo 'mode http' >> tmp/haproxy.cfg");
	exec("echo 'stats enable' >> tmp/haproxy.cfg");
	exec("echo 'stats hide-version' >> tmp/haproxy.cfg");
	exec("echo 'stats realm Haproxy\ Statistics' >> tmp/haproxy.cfg");
	exec("echo 'stats uri /' >> tmp/haproxy.cfg");
	exec("echo 'stats refresh 10s' >> tmp/haproxy.cfg");
	
	#HA
	
	$query = "select hg_name, hg_port, hg_mode from hagroup";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	
	if ($row){
		
		$res = mysql_query($query);
			
		for ($i=0; $i < mysql_num_rows($res); $i++){
			
			$row_ha = mysql_fetch_row($res);
			
			list($hg_name, $hg_port, $hg_mode ) = $row_ha;
									
			exec("echo 'listen $hg_name 0.0.0.0:$hg_port' >> tmp/haproxy.cfg");
			exec("echo 'option  httpchk *' >> tmp/haproxy.cfg");
			exec("echo 'balance $hg_mode' >> tmp/haproxy.cfg");
			exec("echo 'cookie  SERVERID insert indirect nocache' >> tmp/haproxy.cfg");
			
			#echo "<p> group: ".$i." ".$hg_name." ".$hg_port." ".$hg_mode."<p>" ;
			
			$sql_haproxy = "select h_vm_name, h_ip from haproxy where h_name = '$hg_name'";
			$res_haproxy = mysql_query($sql_haproxy);
			
			for ($j=0; $j < mysql_num_rows($res_haproxy); $j++){
				
				$row_haproxy = mysql_fetch_row($res_haproxy);
				
				list($h_vm_name, $h_ip) = $row_haproxy;
				
				exec("echo 'server $h_vm_name $h_ip:80 check cookie $h_vm_name' >> tmp/haproxy.cfg ");
				
				#echo $j." ".$h_vm_name." ".$h_ip."<p>";
				
			}
			
			
		}
		
		exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'cp -p /var/www/html/avdces/tmp/haproxy.cfg /etc/haproxy/haproxy.cfg'");
		
		exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 '/etc/init.d/haproxy reload'");
		
		
	}
}


?>



<?php 

mysql_close();
require('bsfooter.php');

?>
