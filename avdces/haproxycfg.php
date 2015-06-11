<?php

require('bsheader.php');
require('bssidebar.php');
require('mysql_config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 

<h3 class="page-header">HAProxy Cfg</h3>

<?php

$act = $_POST['act'];

#echo "<h4>action is ".$act ."</h4><p>";
echo "<h4> ".$act ." to haproxy</h4><p>";

# hagroup del 

if ( $act == "hagroup_del" ) {
	if(!empty($_POST['del_gid_list'])) {
		foreach($_POST['del_gid_list'] as $check) {
				echo $check."<p>";
				
				$sql_del_hagroup = "DELETE FROM hagroup WHERE hg_id ='$check' ";
				echo $sql_del_hagroup."<p>";
				mysql_query($sql_del_hagroup) or die("$check. can't delete from hagroup");
		}
	}else{
		#header( 'Location: http://www.yoursite.com/new_page.html' ) ; 
		#echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		
	}
	
	sh_haproxy();
	
}

# hagroup add

if ( $act == "hagroup_add" ) {
		
	$hg_port = $_POST['hg_port'];
	
	if ( !is_numeric($hg_port ) or $hg_port < 10000 or  $hg_port > 20000 ){
		
		echo "Port no in range 10000 ~ 20000 <p>";
		echo '<input type="button" value="goback" onclick="history.back()">';
		exit();	
	}
	
	$query = "select hg_port from hagroup where hg_port='$hg_port'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	
	if ( $row ){
		
		echo "port already use!<p>";
		echo '<input type="button" value="goback" onclick="history.back()">';
		exit();
		
	}	
		
	$hg_name = $_POST['hg_name'];

	$query = "select hg_name from hagroup where hg_name='$hg_name'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	
	if ( $row ){
		
		echo "group name exist!<p>";
		echo '<input type="button" value="goback" onclick="history.back()">';
		exit();
		
	}
	
	
	$hg_mode = $_POST['hg_mode'];
	echo $hg_name."  ".$hg_port." ".$hg_mode."<p>";
	
	$sql_ins_hagroup = 
			"insert into 
			hagroup (hg_name, hg_port, hg_mode )
			value ('$hg_name', '$hg_port', '$hg_mode')
			";
			echo $sql_ins_hagroup;
			
			mysql_query($sql_ins_hagroup) or die("$hg_name  can't insert to hagroup");
			
	sh_haproxy();
}

# ha list del
if ( $act == "delete"){

	if(!empty($_POST['del_id_list'])) {
		foreach($_POST['del_id_list'] as $check) {
				echo $check."<p>";
				$sql_del_haproxy = "DELETE FROM haproxy WHERE h_id ='$check' ";
				echo $sql_del_haproxy."<p>";
				mysql_query($sql_del_haproxy) or die("$check. can't delete from haproxy");
		}
		
		sh_haproxy();
		
	}else{
		#header( 'Location: http://www.yoursite.com/new_page.html' ) ; 
		#echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
	
}


if ( $act == "add" ) {

	if(!empty($_POST['add_vm_list']) ){
		foreach($_POST['add_vm_list'] as $check) {
				
			$query = "select vm_ip, vm_net from vm where vm_name='$check'";
			$result = mysql_query($query) or die ("can't select vm for haproxy");
			$row = mysql_fetch_row($result);
			list($vm_ip, $vm_net) = $row;
			#echo "<p>";
			#echo $h_name." ";
			#echo $h_port." ";
			$h_name = $_POST['hg_name'];
			
			#echo $check." ";
			#echo $vm_net." ".$vm_ip."<p>";
			
			$sql_ins_haproxy = 
			"insert into 
			haproxy (h_name, h_vm_name, h_net, h_ip )
			value ('$h_name', '$check', '$vm_net', '$vm_ip')
			";
			echo $sql_ins_haproxy;
			mysql_query($sql_ins_haproxy) or die("$check  can't insert to haproxy");
			
		}

		sh_haproxy();
		
	}else{
		#header('Location: ' . $_SERVER['HTTP_REFERER']);
		echo "<p>there is no vm select<p>";
		echo 
		"<script type='text/javascript'>
		function goBack() {
		javascript: history.go(-1);
		}
		function timer() {
		setTimeout('goBack()', 3000);
		}
		window.onload=timer;
		</script>";
		
	}
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


</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');
?>
