<?php

require('bsheader.php');
require('bssidebar.php');

?>
<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    
	
 <h3>Create Network</h4>
 	
<style type="text/css">
input{ width: 50%;}
</style>
   	
<p>
	
	<form class="navbar-form navbar-left" method="post" action="net_create.php">
    <p>
	<input type="text" class="form-control" placeholder="Net Name"
		id="input_net_name" name="net_name" required data-fv-notempty-message />
    </p>
	<p>
		<label>Forwarding Type</label>
		<select name="forwarding">
                        <option value="nat">nat</option>
                        <option value="route">route</option>
                        <option value="None">none</option>
                        
                    </select>
	</p>
	<p>		
	<input type="text" class="form-control" placeholder="Net Network"
		id="input_net_network" name="net_network" />
	
    </p>     
	<p><label>subnet mask only for /24 
	<p>ex: 192.168.1 or 172.16.2 or 10.0.3</label>
	<hr>
	<!--
	<p>		
	<input type="text" class="form-control" placeholder="Net Start"
		id="input_net_start" name="dhcp_start" required data-fv-notempty-message />
    </p>
    
	<p>		
	<input type="text" class="form-control" placeholder="Net Stop"
		id="input_net_stop" name="dhcp_stop" required data-fv-notempty-message />
    </p>
    -->
	
	<p>
		<label>Net Start IP</label>
		<select name="dhcp_start">
                    <option value="2">2</option>
                        <?php
							for ($i=3;$i<=253;$i++){
								echo '<option value="'.$i.'">'.$i.'</option>';
							}
						?>
					</select>
	</p>
	
	<p>
		<label>Net Stop IP</label>
		<select name="dhcp_stop">
                        <option value="254">254</option>
                        <?php
							for ($i=253;$i>=3;$i--){
								echo '<option value="'.$i.'">'.$i.'</option>';
							}
						?>>
                        
                    </select>
	</p>
	
<hr>	
	<p><button type="submit" class="btn btn-info">Create Net</button>   
	<!--<button type="submit" class="btn btn-info">Quick Create Net</button>-->
	</p>

</form>
</p>

  
   
</div>
</div>
  


<?php
	require('bsfooter.php');
?>