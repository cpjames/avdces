<?php

require('bsheader.php');
require('bssidebar.php');
require('config.php');

?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 


<h3 class="page-header">VM VNC</h3>

<p>
<button class="btn btn-primary" onclick="javascript:window.location.href='vnc_auto.php?port=8902'"><i class="icon-plus icon-white"></i>Main Host VNC</button>
</p>
<br>

<!--
<button class="btn btn-primary" onclick="javascript:window.open('vnc_auto.php?port=8902')"><i class="icon-plus icon-white"></i>Main Host VNC</button>
-->

<h4 class="page-header">VM list</h4>


	<div class="table-responsive">
        <table class="table table-striped">
			<thead>
                <tr>
                  <th>vm_name</th>
                  <th>vm_vnc_port</th>
                  <th>Action</th>                  
                </tr>
            </thead>
            
			<tbody>	

			<?php

			$doms = $lv->get_domains();

			for ($i = 0; $i < sizeof($doms); $i++) {
				 
				$vm_name = $doms[$i];
				$res = $lv->get_domain_by_name($vm_name);
				$dom = $lv->domain_get_info($res);
				$vnc = $lv->domain_get_vnc_port($res);
				
				if ($vnc < 0){
                    $vnc = '-';
                    $vm_vnc_port = $vnc;
				}else{
					$vm_vnc_port = $vnc;
					$socket_port = $vnc + 3000;
				}
					
				unset($dom);	
					
				echo "<tr>";
				echo "<td>".$vm_name."</td>";
				echo "<td>".$vm_vnc_port."</td>";
				
				echo "<td>";
				if ($lv->domain_is_running($res, $vm_name)){
					
					#echo "on";
					echo "<a href='vnc_auto.php?port=$socket_port'><button class='btn btn-info'>Start VNC</button></a>";
										
				}else{
					
					#echo "off";
					#echo "<span class='label label-default'>VM Off</span>";
					echo "<h4><label class='label label-default'>VM Off</lable>";
				}
				echo "</td>";
				
				echo "</tr>";
				
				
			}


			?>
			</tbody>
		</table>

	</div>

</div>
</div>


<?php require('bsfooter.php');?>
