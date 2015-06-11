
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    

<?php

#require('libvirt.php');

require('mysql_config.php');

#$query = "select * from net_vip where vip_net_name ='default'" ;

$query = "select * from net";

$query2 = "select * from net_vip where vip_net_name ='default'" ;

$result = mysql_query($query) or die('MySQL query error');

$result2 = mysql_query($query2) or die('MySQL query error');

#$row = mysql_fetch_array($result);

#print_r($row);

echo "<p>";

 while($row = mysql_fetch_array($result)){
        #echo $row['uuid']."<p>";
		#print_r($row)."<p>";
		echo $row['dhcp_start']." ".($row['dhcp_stop'])."<p>";
		
		echo ip2long($row['dhcp_start'])." " .ip2long($row['dhcp_stop'])."<p>";
		$ip_num = (ip2long($row['dhcp_stop']) - ip2long($row['dhcp_start']) );
		echo $ip_num."<p>";
		
    }

$net_name = "default";
	
$query3 = "select * from net where net_name='$net_name'";

$result3 = mysql_query($query3) or die('MySQL query error');

$row3 = mysql_fetch_array($result3);


#print_r($row2);

	echo "<p>";

#if ( mysql_fetch_row($result)){

#$ip = $row3['dhcp_start'];

$ip_start = explode(".",$row3['dhcp_start']);

$ip_stop = explode(".",$row3['dhcp_stop']);

$ip_sec = substr($row3['dhcp_stop'], 0, strrpos($row3['dhcp_stop'], "."));

$ip_sec2 = implode(".", array_slice(explode(".", $row3['dhcp_stop']), 0, 3));

$ip_sec3 = preg_replace("/(\d{1,3})\.(\d{1,3}).(\d{1,3}).(\d{1,3})/", '$1.$2.$3',$row3['dhcp_stop']);

#echo $ip_sec."<p>";

#echo $ip_sec2."<p>";

#echo $ip_sec3."<p>";

#print_r($ip_start);

#echo $ip_start[3]."<p>";

for ($i=$ip_start[3]; $i <= $ip_stop[3]; $i++){
	
	$vip_ip = $ip_sec3.".".$i;
	
	$query_vip = "select * from net_vip where vip_ip ='$vip_ip'" ;
	
	$result_vip = mysql_query($query_vip) or die('MySQL query error');
	
	
	$row_vip = mysql_fetch_array($result_vip);
	
	if ( ! $row_vip ){
		$mac = "52:54:00:".implode(':',str_split(substr(md5(mt_rand()),0,6),2));
		
		echo $vip_ip." ".$mac." ".$net_name."<p>"; 
		
		$query_ip_ins = "INSERT INTO net_vip (vip_id, vip_mac, vip_ip, vip_use_uuid, vip_net_name) 
		VALUES (NULL, '$mac', '$vip_ip', NULL, '$net_name') ";
	
		echo $query_ip_ins."<p>";
		mysql_query($query_ip_ins) or die("can't insert to $net_name");
	}
}
	/*
	else
	{
		echo "<p>";
		print_r($row_vip);
		
	}
	*/
	#print_r($row_vip);
	
	#$mac = implode(':',str_split(str_pad(base_convert(mt_rand(0,0xffffff),10,16).base_convert(mt_rand(0,0xffffff),10,16),12),2));
	#$mac = "52:54:".implode(':',str_split(substr(md5(mt_rand()),0,8),2));
	
	#13:39:d4:7c:4b:f5
	#52:54:37:3c:1f:2f 
	
	#$mac1 = exec('MACADDR="52:54:$(dd if=/dev/urandom count=1 2>/dev/null | md5sum | sed \'s/^\(..\)\(..\)\(..\)\(..\).*$/\1:\2:\3:\4/\')"; echo $MACADDR');
    #$mac2 = exec('MACADDR="52:54:$(dd if=/dev/urandom count=1 2>/dev/null | md5sum | sed \'s/^\(..\)\(..\)\(..\)\(..\).*$/\1:\2:\3:\4/\')"; echo $MACADDR');
	#echo $mac1." ".$mac2." ".$ip_sec3.".".$i."<p>";
	#echo $mac." ".$ip_sec3.".".$i."<p>";
	#echo $vip_ip."<p>";
	
}
	

while($row2 = mysql_fetch_array($result2))
{
        if ( ! $row2['vip_ip']){
		#echo $row2['vip_ip']."<p>";
		echo "test"."<p>";
		}
		#print_r($row2)."<p>";
}


	
	

/*
for ($i=0; $i<mysql_num_rows($result); $i++){
	$row = mysql_fetch_row($result);
	list($vip_id, $vip_mac, $vip_ip, $vip_use_uuid, $vip_net_name) = $row;
	echo "<tr>";
	echo "<td>".$vip_mac."</td><td>".$vip_ip."</td><td>".$vip_use_uuid."</td><td>".$vip_net_name."</td>";
	echo "</tr><p>";
}
*/

#print_r($row);

#echo mysql_num_rows($result)."<p>";

/*

for ($i=0; $i < mysql_num_rows($result); $i++){


	
	$row = mysql_fetch_row($result);
	
	list($uuid, $net_name, $forwording, $net_mac, $net_network, $dhcp_start, $dhcp_stop) = $row;
	#echo "<tr>";
	echo "<td>".$uuid."</td><td>".$net_name."</td><td>".$forwording."</td><td>".$net_mac."</td><td>".$net_network."</td><td>".$dhcp_start."</td><td>".$dhcp_stop."</td>";
	echo "</tr><p>";
	
	echo $i."<p>";
	
	#print_r($row);
	
	}




}else{

		echo "no";

}

*/

mysql_close();



?>

</div>