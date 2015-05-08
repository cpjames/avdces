<?php

require('bsheader.php');
require('bssidebar.php');
require('libvirt_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 

<?php

$net_name = $_POST['net_name'];
$forwarding = $_POST['forwarding'];
$net_network = $_POST['net_network'];
$dhcp_start = $_POST['dhcp_start'];
$dhcp_stop = $_POST['dhcp_stop'];

$libvirt_net_name = $lv->get_networks(VIR_NETWORKS_ALL);



#check network 

 for ($i = 0; $i < sizeof($libvirt_net_name); $i++) {
	
	#echo $i." check net name exist<p>";
	
	if ( $net_name == $libvirt_net_name[$i] ){
		
		echo "Net Name Exist!<p>";
		echo '<input type="button" value="goback" onclick="history.back()">';
		exit();		
	
	}else{
		
		#echo $i." check network exist<p>";
		
		$libvirt_network = $lv->get_network_information($libvirt_net_name[$i]);
		$check_network = preg_replace("/(\d{1,3})\.(\d{1,3}).(\d{1,3}).(\d{1,3})/", '$1.$2.$3',$libvirt_network[ip]);
		
		if ($net_network == $check_network){
			
			echo "Network Exist!<p>";
			echo '<input type="button" value="goback" onclick="history.back()">';
			exit();			
	
		}else{
		
			#echo "<p>".$i."  check ip section".$net_network."<p>";
			$net_gateway = $net_network.".1";
			#echo "<p>".$net_gateway."filter var<p>";
				
				if ( filter_var( $net_gateway, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) ||
					!filter_var( $net_gateway, FILTER_VALIDATE_IP)){
					echo $net_network." is not VALIDATE IP section!<p>";
					echo '<input type="button" value="goback" onclick="history.back()">';
					exit();	
				}
			
						
		}
	}
 }

	#swich variable
 
	$net_mac = "52:54:00:".implode(':',str_split(substr(md5(mt_rand()),0,6),2));
 
	$ip_start = $dhcp_start;
	$ip_stop = $dhcp_stop;
	$ip_section = $net_network;
	
	$dhcp_start = $net_network.".".$dhcp_start;
	$dhcp_stop = $net_network.".".$dhcp_stop;
	$network = $net_network.".1";
 
	$uuid = exec(uuidgen);
	
	echo "creating network ...<p>";
	echo "net name: ".$net_name."<p>";
	echo "forwarding: ".$forwarding."<p>";
	echo "net_mac: ".$net_mac."<p>";
	echo "network: ".$network."<p>";
	echo "dhcp_start: ".$dhcp_start."<p>";
	echo "dhcp_stop: ".$dhcp_stop."<p>";
	echo "uuid: ".$uuid."<p>";
	
	# mysql
	# Insert to net
	
	require('mysql_config.php');
	
	$query_check = "select * from net where net_name = '$net_name'";
	$result = mysql_query($query_check) or die("can't query net");
	
	$row = mysql_fetch_array($result);
	
	if ( ! $row ){
	
		$query_net_ins = "insert into 
		net (uuid, net_name, forwarding, net_mac, net_network, dhcp_start, dhcp_stop)
		values ( '$uuid', '$net_name', '$forwarding', '$net_mac', '$net_network', 
		'$dhcp_start', '$dhcp_stop')";
		
		echo $query_net_ins."<p>";
		
		mysql_query($query_net_ins) or die("can't insert to net");
	}
	
	
	$query_check = "select * from net_vip where vip_ip = '$network'";
	
	$result = mysql_query($query_check) or die("can't query net_vip");
	$row = mysql_fetch_array($result);
	
	if ( ! $row ){
	
		$query_ip_ins = "INSERT INTO net_vip (vip_id, vip_mac, vip_ip, vip_use_name, vip_net_name) 
		VALUES (NULL, '$net_mac', '$network', '$net_name', '$net_name')";
		
		echo $query_ip_ins."<p>";
		
		mysql_query($query_ip_ins) or die("can't insert to vip_ip");
		
	}
	
	# create ip mac
	
	for ($i=$ip_start; $i<=$ip_stop; $i++){
		
		$vip_ip = $ip_section.".".$i;
		
		$query_vip = "select * from net_vip where vip_ip ='$vip_ip'" ;
		
		$result_vip = mysql_query($query_vip) or die('MySQL query error');
		
		$row_vip = mysql_fetch_array($result_vip);
		
		if ( ! $row_vip ){
		
		$vip_mac = "52:54:00:".implode(':',str_split(substr(md5(mt_rand()),0,6),2));
		
		#echo $vip_ip." ".$mac." ".$net_name."<p>"; 
		
		$query_ip_ins = "INSERT INTO net_vip (vip_id, vip_mac, vip_ip, vip_use_name, vip_net_name) 
		VALUES (NULL, '$vip_mac', '$vip_ip', NULL, '$net_name') ";
	
		#echo $query_ip_ins."<p>";
		
		mysql_query($query_ip_ins) or die("can't create mysql template");
	}
	
		
		
}
	
	
	
	echo "mysql finish!<p>";
	
	
	# create virsh xml
	
	$query_net = "select net_name, uuid, forwarding, net_mac, 
	dhcp_start, dhcp_stop from net where net_name='$net_name'";

	$result = mysql_query($query_net)or die("can't query net");
	
	$row = mysql_fetch_array($result);
	
	list($net_name, $uuid, $forwarding, $net_mac, $dhcp_start, $dhcp_stop) = $row;
	
	echo "<hr>";
	echo "net name: ".$net_name."<p>";
	echo "uuid: ".$uuid."<p>";
	echo "forwarding: ".$forwarding."<p>";
	echo "net_mac: ".$net_mac."<p>";
	echo "network: ".$network."<p>";
	echo "dhcp_start: ".$dhcp_start."<p>";
	echo "dhcp_stop: ".$dhcp_stop."<p>";
	
	$net_xml ="
<network>
	<name>$net_name</name>
	<uuid>$uuid</uuid>";
	if ( $forwarding != "None"){
	$net_xml = $net_xml.
	"<forward mode='$forwarding'/>";
	}
	$net_xml = $net_xml."
	<bridge name='$net_name' stp='on' delay='0' />
	<mac address='$net_mac'/>
	<ip address='$network' netmask='255.255.255.0'>
	<dhcp>
	<range start='$dhcp_start' end='$dhcp_stop' />";
	
	
	$query_net_vip = "select vip_mac, vip_ip from net_vip where vip_net_name='$net_name'";

	$result2 = mysql_query($query_net_vip)or die("can't query net_vip");
	
	while ($row2 = mysql_fetch_array($result2)){
		
		$net_xml = $net_xml.
		"
		<host mac='".$row2[vip_mac]."' ip='".$row2[vip_ip]."' />";
		
	}
			
	$net_xml = $net_xml."
	</dhcp>
	</ip>
</network>";

	echo "net xml: <p>";
	echo "<pre>".htmlentities($net_xml)."</pre>";

	#use tmp if apache didn't give permission to libvirt folder 
	
	$file = fopen("tmp/$net_name.xml","a");
	fwrite($file, $net_xml);
	fclose($file);
	exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 mv /var/www/html/avdces/tmp/$net_name.xml /etc/libvirt/qemu/networks/");
	exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'virsh -c qemu+ssh://root@127.0.0.1/system net-define /etc/libvirt/qemu/networks/$net_name.xml'");
				
	mysql_close();
	
?>



</div>
</div>


<?php require('bsfooter.php');?>
