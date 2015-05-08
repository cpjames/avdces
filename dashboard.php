<?php 
require("bsheader.php");
require("bssidebar.php");
require("config.php");
require("mysql_config.php");

?>


<script>
 
 function autoRefresh1()
{
	   window.location.reload();
}

 function autoRefresh()
{
	window.location = window.location.href;
}
 
 setInterval('autoRefresh1()', 60000); // this will reload page after every 5 secounds; Method II
</script>


  
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		  
	<h2 class="page-header">Dashboard</h2>

		
	<h3 class="page-header">Virtaul Network</h3>
	
	<?php 
		$cpu_load = explode(",",exec("cat tmp/main.tmp"));
			$cpu_usage = $cpu_load[0]." %";
								
			if ( $cpu_usage > 60 ){
				$color = "red";
			}elseif($cpu_usage > 40 ){
				$color = "orange";
			}else{
				$color = "blue";
			}
		$sess =  $cpu_load[1];
		
		
		echo "<pre>Main Host: ".$_SERVER["SERVER_ADDR"]." CPU Usage :<font color=".$color.
		"> ".$cpu_usage." </font>Sessions: ".$sess ;
		
	?>
	
		<div class="row placeholders">
		
		<?php
		
		$sql_net = "SELECT net_name, forwarding, net_network FROM net order by net_name asc";
			
		$result_net = mysql_query($sql_net);
			
		for ($i=0; $i<mysql_num_rows($result_net); $i++){
		
			$row_net = mysql_fetch_row($result_net);
			
			list($net_name, $forwarding, $net_network) = $row_net;
			
			$sql_net_vm = "SELECT vip_use_name FROM net_vip WHERE vip_net_name = '$net_name' AND vip_use_name IS NOT NULL ";
			
			$vm_result = mysql_query($sql_net_vm) or die("can't query net_vip");
			
			$vm_num = mysql_num_rows($vm_result) - 1;		
			
			#coloer holder.js
			# lava
			# sky, vine, lava, gray, industrial, and social.
			
			if ($vm_num == 0 ){
			$size = '80x80';
			$color = "gray";
			}elseif($vm_num == 1){
				$size = '100x100';
				$color = "vine";				
			}elseif($vm_num > 1 && $vm_num <=4 ){
				$size = '110x110';
				$color = "sky";
			}elseif( $vm_num >= 5 && $vm_num <=8 ){
				$size = '130x130';
				$color = "social";	
			}elseif( $vm_num > 8){
				$size = '150x150';
				$color = "social";	
				#$color = "industria";	
			}

			#check cpu load
			$query_vm = "select vm_name from vm where vm_net = '$net_name'";
			$result_vm = mysql_query($query_vm);
			
			
			if ( $result_vm ){
				
				for ($ii=0; $ii < mysql_num_rows($result_vm); $ii++ ){
				
				$row_vm = mysql_fetch_row($result_vm);
				list($vm_name) = $row_vm;
				
				$chk_cpu = explode(",",exec('cat tmp/'.$vm_name.'.tmp'));
				
					if ($chk_cpu[0] > 60 ){
						$color = "lava";
						
					}							
				
				}				
			}
			
			if ( $forwarding == "None" ){
				$forwarding = "isolation";
			}
			
			echo '<div class="col-xs-6 col-sm-3 placeholder">';
			echo '<img data-src="holder.js/'.$size.'/auto/'.$color.'/text: vm : '.$vm_num.' \n vcpu :'
			.'">';
			echo "<h5>".$net_name."</h5>";
			echo ' <span class="text-muted">'.$net_network .'.0/24</span>';
			echo "<h5><font color=blueviolet>".$forwarding."</font></h5>";
			echo '</div>';
			
		}
		
		?>
				
		</div>
	</pre>
	
	<h3 class="page-header">Virtual Machine</h3>
	
	<div class="table-responsive">
        <table class="table table-striped">
			<thead>
                <tr>
                  <th>vm_name</th>
                  <th>vm_net</th>
                  <th>vm_ip</th>
				  <th>state</th>
				  <th>CPU</th>
				  <th>sess</th>
                </tr>
            </thead>
            
			<tbody>	
				
			<?php
			
				$query_vm = " select vm_name, vm_net, vm_ip from vm order by vm_net ASC";
				
				$result_vm = mysql_query($query_vm) or die("can't query vm");
			
				for ($i=0; $i < mysql_num_rows($result_vm); $i++ ){
					
					$row_vm = mysql_fetch_row($result_vm);
					list($vm_name, $vm_net, $vm_ip) = $row_vm;
					
					$res = $lv->get_domain_by_name($vm_name);
					$dom = $lv->domain_get_info($res);
					$state = $lv->domain_state_translate($dom['state']);
					$cpu_load = "-";
					if ($state == "running" ){
					
						$cpu_load = explode(",",exec("cat tmp/$vm_name.tmp"));
						$cpu_usage = $cpu_load[0]." %";
						$sess = $cpu_load[1];
					}else{
						$cpu_usage = "-";
						$sess = "-";
					}
					
					if ( $cpu_usage > 60 ){
						$color = "red";
						#$color = "orange";
					}elseif($cpu_usage > 40 ){
						$color = "orange";
					}else{
						$color = "blue";
					}
					
					echo "<tr>";
					echo "<td>".$vm_name."</td><td>".$vm_net."</td><td>".$vm_ip."</td><td>".$state."</td><td><font color='$color'>".$cpu_usage." </font></td><td>".$sess."</td>";
					#echo "<td>".$vm_name."</td><td>".$vm_net."</td><td>".$vm_ip."</td><td><font color='$color'>".$cpu_usage." </font></td><td>".$sess."</td>";
					echo "</tr>";
					ob_flush();
					flush();
					
										
				}
			
			?>
			  
			</tbody>
		</table>
	
	  
	</div>
	
</div>


<?php 

mysql_close();
require("bsfooter.php");
?>
