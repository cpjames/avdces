<?php 
require("bsheader.php");
require("bssidebar.php");
require("config.php")

?>

  <div class="container-fluid">
     
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    

	<?php
	
	$uri = $lv->get_uri();
	$tmp = $lv->get_domain_count();

	
    echo "Hypervisor URI: <i>$uri</i>, hostname: <i>$hn</i><p>";
	echo "<p>";
    echo "VM Number: {$tmp['total']} , {$tmp['active']} active, {$tmp['inactive']} inactive<p>";    
    
	
	
	
	
	?>
	<div class="list">
        <?php
            $doms = $lv->get_domains();
            $domkeys = array_keys($doms);
            $active = $tmp['active'];
        ?>
        <table class="table table-bordered">
            <tr>
                <th>VM Name</th>
                <th>CPUs</th>
                <th>Memory</th>
                <th>Disk</th>
                <th>OS Bits</th>
				<th>Mac</th>
                <th>Status</th>
                
            </tr>
            <?php
            $ret = false;
            if ($action) {
                $domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
                if ($action == 'domain-start') {
                    $ret = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
                }
                else if ($action == 'domain-stop') {
                    $ret = $lv->domain_shutdown($domName) ? "Domain has been stopped successfully" : 'Error while stopping domain: '.$lv->get_last_error();
                }
                else if ($action == 'domain-destroy') {
                    $ret = $lv->domain_destroy($domName) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
                }
                else if (($action == 'domain-get-xml') || ($action == 'domain-edit')) {
                    $inactive = (!$lv->domain_is_running($domName)) ? true : false;
                    $xml = $lv->domain_get_xml($domName, $inactive);

                    if ($action == 'domain-edit') {
                        if (@$_POST['xmldesc']) {
                            $ret = $lv->domain_change_xml($domName, $_POST['xmldesc']) ? "Domain definition has been changed" :
                                                        'Error changing domain definition: '.$lv->get_last_error();
                        }
                        else
                            $ret = 'Editing domain XML description: <br /><br /><form method="POST"><table width="80%"><tr><td width="200px">Domain XML description: </td>'.
                                '<td><textarea name="xmldesc" rows="25" style="width:80%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
                                '<input type="submit" value=" Edit domain XML description "></tr></form>';
                    }
                    else
                        $ret = "Domain XML for domain <i>$domName</i>:<br /><br />".htmlentities($xml);
                }
            }
            for ($i = 0; $i < sizeof($doms); $i++) {
                $name = $doms[$i];
                $res = $lv->get_domain_by_name($name);
                $uuid = libvirt_domain_get_uuid_string($res);
                $dom = $lv->domain_get_info($res);
                $mem = number_format($dom['memory'] / 1024, 2, '.', ' ').' MB';
                $cpu = $dom['nrVirtCpu'];
                $state = $lv->domain_state_translate($dom['state']);
                $id = $lv->domain_get_id($res);
                $arch = $lv->domain_get_arch($res);
                $vnc = $lv->domain_get_vnc_port($res);
                $nics = $lv->get_network_cards($res);
				$netinfo = $lv->get_nic_info($res);
				$mac = $netinfo[0][mac];
                if (($diskcnt = $lv->get_disk_count($res)) > 0) {
                    #$disks = $diskcnt.' / '.$lv->get_disk_capacity($res);
					$disks = $lv->get_disk_capacity($res);
                    $diskdesc = 'Current physical size: '.$lv->get_disk_capacity($res, true);
                }
                else {
                    $disks = '-';
                    $diskdesc = '';
                }

                
                    
                unset($tmp);
                if (!$id)
                    $id = '-';
                unset($dom);

                echo "<tr>
                        <td>$name</td>
                        <td>$cpu</td>
                        <td>$mem</td>
						<td>$disks</td>";
                echo    "<td>$arch</td>
						<td>$mac</td>
                        <td>$state</td>";
				

              

              
                echo "</td></tr>";
            }
			
            ?>
        </table>
        <?php if ($ret) echo "<br /><pre>$ret</pre>";
		print_r($ret);
		echo "<pre>test</pre>";
		?>
    </div>

	</div>
	
	
	

</div>



<?php require("bsfooter.php");?>