<?php 
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
#$components = explode('/', $path);
$components = explode('/',ltrim($_SERVER['REQUEST_URI'],'/'));
$first_part = $components[1];
?>
 
<div class="container-fluid">
 
 <div class="row">
	
	<div class="col-sm-3 col-md-2 sidebar">
        
		<ul class="nav nav-sidebar">
           	
				
			<li class=<?php if ($first_part=="vm_mgr.php") 
								{echo "active"; } else  
								{echo "noactive";}?>>
			<a href="vm_mgr.php">VM Manage</a></li>
			<li class=<?php if ($first_part=="vm_virsh.php") 
								{echo "active"; } else  
								{echo "noactive";}?>>
				<a href="vm_virsh.php">VM Virsh</a>
			</li>
			<li class=<?php if ($first_part=="vm_template.php") 
								{echo "active"; } else  
								{echo "noactive";}?>>
			<a href="vm_template.php">VM Template</a></li>
			</li>
		</ul>
     
	<!--
	 
        <ul class="nav nav-sidebar">
		
			<li class=<?php /* if ($first_part=="vm_vnc.php")
							{echo "active"; } else  
							{echo "noactive";}
							*/
						?>>
			<a href="vm_vnc.php">VM VNC</a>
			</li>
		
		</ul>
		
	-->	
  	
		<ul class="nav nav-sidebar">
			<li class=<?php if ($first_part=="virtual_networks.php")
							{echo "active"; } else  
							{echo "noactive";}?>>
			<a href="virtual_networks.php">Virtual Network</a></li>
            
			
			
			<li class=<?php if ($first_part=="networks_virsh.php")
							{echo "active"; } else  
							{echo "noactive";}?>>
			<a href="networks_virsh.php">Network Virsh</a></li>
			
			<li class=<?php if ($first_part=="virtual_ips.php")
							{echo "active"; } else  
							{echo "noactive";}?>>
			<a href="virtual_ips.php">Virtual IPs</a></li>
			
			<li class=<?php if ($first_part=="mapping_vip.php")
							{echo "active"; } else  
							{echo "noactive";}?>>
			<a href="virtual_ips.php">Port Mapping</a></li>
			
        </ul>
		
		
		<ul class="nav nav-sidebar">
		
			<li class=<?php if ($first_part=="ha_mgr.php") 
								{echo "active"; } else  
								{echo "noactive";}?>>
				<a href="ha_mgr.php">HA Manage</a>
			</li>
			
			
			<li class=<?php if ($first_part=="hastatus.php") 
								{echo "active"; } else  
								{echo "noactive";}?>>
				<a href="hastatus.php">HA Status</a>
			</li>
		
		</ul>
		
		<ul class="nav nav-sidebar">
			<li class=<?php if ($first_part=="test.php") 
								{echo "active"; } else  
								{echo "noactive";}?>>
				<a href="#">*Quick HA Group</a>
			</li>
		</ul>
		
			
	</div>
  </div>
