<?php
require('bsheader.php');
require('config.php');
require("bssidebar.php");

$tmp = $lv->host_get_node_info();
$ci  = $lv->get_connect_information();
?>


<div class="container-fluid">
     
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    

        <!-- <h2>Host information</h2>  -->
	
	<div class="list">
	

	<?php 
		$info = '';
		if ($ci['uri'])
			$info .= 'connected to <i>'.$ci['uri'].'</i> on <i>'.$ci['hostname'].'</i>, ';
		if ($ci['encrypted'] == 'Yes')
			$info .= 'encrypted, ';
		if ($ci['secure'] == 'Yes')
			$info .= 'secure, ';
		if ($ci['hypervisor_maxvcpus'])
			$info .= 'maximum '.$ci['hypervisor_maxvcpus'].' vcpus per guest, ';

		if (strlen($info) > 2)
			$info[ strlen($info) - 2 ] = ' ';

			
		echo "
			  <table class='table table-bordered'>
				<tr>
					<td>Hypervisor: </td>
					<td>{$ci['hypervisor_string']}</td>
				</tr>
				<tr>
					<td>Connection information: </td>
					<td>$info</td>
				</tr>
				<tr>
					<td>Architecture: </td>
					<td>{$tmp['model']}</td>
				</tr>
				<tr>
					<td>Total memory installed: </td>
					<td>".number_format(($tmp['memory'] / 1048576), 2, '.', ' ')."GB </td>
				</tr>
				<tr>
					<td>Total processor count: </td>
					<td>{$tmp['cpus']}</td>
				</tr>
				<tr>
					<td>Processor speed: </td>
					<td>{$tmp['mhz']} MHz</td>
				</tr>
				<tr>
					<td>Processor nodes: </td>
					<td>{$tmp['nodes']}</td>
				</tr>
				<tr>
					<td>Processor sockets: </td>
					<td>{$tmp['sockets']}</td>
				</tr>
				<tr>
					<td>Processor cores: </td>
					<td>{$tmp['cores']}</td>
				</tr>
				<tr>
					<td>Processor threads: </td>
					<td>{$tmp['threads']}</td>
				</tr>
			  </table>
			";

		unset($tmp);
	?>
	</div>

	
	</div>
	
</div>




<?php require('bsfooter.php');?>