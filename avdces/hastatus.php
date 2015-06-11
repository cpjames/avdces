<?php

require('bsheader.php');
require('bssidebar.php');
require("config.php");
require("mysql_config.php");

?>

<frameset>

<frame src="http://192.168.2.36:8080/">

</frameset>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<script>
 
 function autoRefresh1()
{
	   window.location.reload();
}

 function autoRefresh()
{
	window.location = window.location.href;
}
 
 setInterval('autoRefresh1()', 10000); // this will reload page after every 5 secounds; Method II
</script>

<h3 class="page-header">HAProxy Group</h3>
	
		<div class="row placeholders">
		
		<?php
		$hcolor ="industrial";
		$hsize ="130x130";
		
				
		$sql_ha = "SELECT hg_name, hg_port FROM hagroup order by hg_name asc";
		$result_ha = mysql_query($sql_ha);
		for ($ha=0; $ha<mysql_num_rows($result_ha); $ha++){
			
			$row_ha = mysql_fetch_row($result_ha);
			list($hg_name, $hg_port) = $row_ha;
			#echo '<div class="col-xs-6 col-sm-3 placeholder">';
			#echo '</div>';
			
			$hg_vm ="loading";
			
			echo '<div class="col-xs-6 col-sm-3 placeholder">';
			echo '<img data-src="holder.js/'.$hsize.'/auto/'.$hcolor.
			'/text: HA: \n '.
			$hg_vm.'">';
			echo "<h5>".$hg_name."</h5>";
			
			echo ' <span class="text-muted">'.$_SERVER['SERVER_ADDR'].':'.$hg_port .'</span>';
			
			echo '</div>';
			
		}
		
		?>
		
		</div>




<?php

system ("date");

#$ha_status = exec("curl http://192.168.2.36:8080/ ");

#echo $ha_status;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.2.36:8080/");
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

$temp = curl_exec($ch);

curl_close($ch);

echo $temp;


?>
<!--
<h4 >
<a href = "http://192.168.2.36:8080/">http://192.168.2.36:8080/</a>
</h4>
-->
<h4 class="page-header" onclick="javascript:window.open('http://192.168.2.36:8080/')">
<a>http://192.168.2.36:8080/</a>
</h4>
</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');?>
