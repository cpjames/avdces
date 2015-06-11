<?php

require('bsheader.php');
require('bssidebar.php');
require("config.php");
require("mysql_config.php");


?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h3 class="page-header">VM CPU Monitor</h3>


<div class="table-responsive">
        <table class="table table-striped">
			<thead>
                <tr>
                  <th>vm_name</th>
                  <th>vm_net</th>
                  <th>vm_ip</th>
				  <th>status</th>
				  <th>CPU Load</th>
				  <th>VM Sessions</th>
                </tr>
            </thead>
            
			<tbody>	



<?php


$cpu_stat = exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'top -b -n2|grep Cpu' ");
						$cpu_load = explode(",",$cpu_stat);		
						$cpu_use = explode(" ",$cpu_load[0]);					
										
						$cpu_usage = substr($cpu_use[1],0,-3);
					
						if ($cpu_usage == Null){
							$cpu_usage = "0.0";
							}
					
						if ( $cpu_usage > 60 ){
							$color = "red";
							#$color = "orange";
						}elseif($cpu_usage > 40 ){
							$color = "orange";
						}else{
							$color = "blue";
						}
# http log
						
						$vm_log = "/var/log/httpd/access_log";
						
						# format 27/Apr/2015:14:23:03 +0800
						# shell
						$log_format = exec("date +%d/%b/%Y:%H:%M -d '1 min ago'");
						$log_con ="OPTIONS";
		
						$vm_sess = 	
						exec("ssh -o StrictHostKeyChecking=no root@127.0.0.1 'grep $log_format $vm_log | grep -v $log_con| wc -l'");
						
	
echo "Main Host Cpu Usage: <font color=".$color.">".
$cpu_usage."%</font>  session: ".$vm_sess."<p>";
	
exec("echo '$cpu_usage,$vm_sess' > tmp/main.tmp");	


$query_vm = " select vm_name, vm_net, vm_ip from vm order by vm_net ASC";
				
				$result_vm = mysql_query($query_vm) or die("can't query vm");
			
				for ($i=0; $i < mysql_num_rows($result_vm); $i++ ){
					
					$row_vm = mysql_fetch_row($result_vm);
					list($vm_name, $vm_net, $vm_ip) = $row_vm;
					#$test = exec("ssh -o StrictHostKeyChecking=no root@$vm_ip 'echo hello' ");
					#echo $test;
					$res = $lv->get_domain_by_name($vm_name);
					$dom = $lv->domain_get_info($res);
					$state = $lv->domain_state_translate($dom['state']);
					$cpu_load = "-";
					
					if ($state == "running" ){
					
						$cpu_stat = exec("ssh -o StrictHostKeyChecking=no root@$vm_ip 'top -b -n2|grep Cpu' ");
						$cpu_load = explode(",",$cpu_stat);		
						$cpu_use = explode(" ",$cpu_load[0]);					
										
						$cpu_usage = substr($cpu_use[1],0,-3);
					
						if ($cpu_usage == Null){
							$cpu_usage = "0.0";
							}
					
						if ( $cpu_usage > 60 ){
							$color = "red";
							#$color = "orange";
						}elseif($cpu_usage > 40 ){
							$color = "orange";
						}else{
							$color = "blue";
						}
				
						# http log
						
						$vm_log = "/var/log/httpd/access_log";
						
						# format 27/Apr/2015:14:23:03 +0800
						# shell
						$log_format = exec("date +%d/%b/%Y:%H:%M -d '1 min ago'");
						$log_con ="OPTIONS";
		
						$vm_sess = 	
						exec("ssh -o StrictHostKeyChecking=no root@$vm_ip 'grep $log_format $vm_log | grep -v $log_con| wc -l'");
												
						exec("echo '$cpu_usage,$vm_sess' > tmp/$vm_name.tmp");
						
						$cpu_usage = $cpu_usage. " %";
						
						echo "<tr>";
					#echo "<td>".$vm_name."</td><td>".$vm_net."</td><td>".$vm_ip."</td><td>".$state."</td><td>".print_r($cpu_use)."</td>";
					#echo "<td>".$vm_name."</td><td>".$vm_net."</td><td>".$vm_ip."</td><td>".$state."</td><td>".print_r($cpu_load)."</td>";
					echo "<td>".$vm_name."</td><td>".$vm_net."</td><td>".$vm_ip."</td><td>".$state.
					"</td><td><font color='$color'>".$cpu_usage."</font></td><td>".$vm_sess."</td>";
					#echo "<td>".$vm_name."</td><td>".$vm_net."</td><td>".$vm_ip."</td><td>".$state."</td><td>".$cpu_stat."</td>";
					echo "</tr>";
					ob_flush();
					flush();
					
															
					}else{
						
						exec("rm -fr tmp/$vm_name.tmp");
						
					}
	
					
										
				}
			
?>
		</tbody>
		</table>
	
	  
	</div>









</div>
</div>


<?php 

mysql_close();
require('bsfooter.php');

?>
