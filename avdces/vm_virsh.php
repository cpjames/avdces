<?php

require('bsheader.php');
require('bssidebar.php');
require('config.php');


$uri = $lv->get_uri();
$tmp = $lv->get_domain_count();

?>
<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    


   
        <!--
		<p>
            <?php echo "Hypervisor URI: <i>$uri</i>, hostname: <i>$hn</i>";?>
        </p>
		-->
        <p>
            <?php 
			#echo "Statistics: {$tmp['total']} domains, {$tmp['active']} active, {$tmp['inactive']} inactive";
			echo "VM Number: {$tmp['total']} , {$tmp['active']} active, {$tmp['inactive']} inactive";
			?>
        </p>
        <!--
		<p>
            <button class="btn btn-primary" onclick="javascript:window.location.href='addvm.php'"><i class="icon-plus icon-white"></i>Add VM</button>
        </p>
		-->
   
    
	
	<div class="list">
        <?php
            $doms = $lv->get_domains();
            $domkeys = array_keys($doms);
            $active = $tmp['active'];
        ?>
        <table class="table table-bordered">
            <tr>
                <th>VM Name</th>
                <!-- 
				<th>CPUs</th>
                <th>Memory</th>
                <th>Disk</th>
                <th>NICs</th> 
                <th>OS Bits</th> -->
                <th>Status</th>
                <!-- <th>ID / VNC port</th>-->
                <th>Option</th>
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
                #$mem = number_format($dom['memory'] / 1024, 2, '.', ' ').' MB';
				$mem = number_format($dom['memory'] / 1024 /1024, 0, '.', ' ').' GB';
                $cpu = $dom['nrVirtCpu'];
                $state = $lv->domain_state_translate($dom['state']);
                $id = $lv->domain_get_id($res);
                $arch = $lv->domain_get_arch($res);
                $vnc = $lv->domain_get_vnc_port($res);
                #$nics = $lv->get_network_cards($res);
                if (($diskcnt = $lv->get_disk_count($res)) > 0) {
                    #$disks = $diskcnt.' / '.$lv->get_disk_capacity($res);
					$disks = $lv->get_disk_capacity($res);
                    #$diskdesc = 'Current physical size: '.$lv->get_disk_capacity($res, true);
					$diskdesc = $lv->get_disk_capacity($res, true);
                }
                else {
                    $disks = '-';
                    $diskdesc = '';
                }
				
                if ($vnc < 0){
                    $vnc = '-';
                    $vncport = $vnc;
                }else{
                    $vncport = $vnc;
                    #$vnc = $_SERVER['HTTP_HOST'].':'.$vnc;
                }
                  
                unset($tmp);
                if (!$id)
                    $id = '-';
                unset($dom);

                echo "<tr>
                        <td>
                        <a href=\"domaininfo.php?uuid=$uuid\">$name</a>
                        </td>";
						
					#echo "<td>$cpu</td><td>$mem</td>";
					#echo "<td>$diskdesc / $disks</td>";
					#echo "<td>$diskdesc</td>";
                        #<td title='$diskdesc'>$disks</td>";
						#<td>$nics</td>
                #echo    "<td>$arch</td>
                echo "       <td>$state</td>";
				#echo "		<td>$id / $vnc</td>";

                echo "<td>";

                if ($lv->domain_is_running($res, $name)){
                    
					/*
					echo "<button class=\"btn btn-info\" onclick=\"javascript:window.open('vm_virsh.php?action=domain-vnc&amp;vmname=$name');\">VNC</button> | ";
					*/                  
					echo "<button class=\"btn btn-warning\" onclick=\"javascript:location.href='vm_virsh.php?action=domain-stop&amp;uuid=$uuid'\">Shutdown</button> | ";
                    echo "<button class=\"btn btn-danger\" onclick=\"javascript:location.href='vm_virsh.php?action=domain-destroy&amp;uuid=$uuid'\">Force Down</button>";
                }else
                    echo "<button class=\"btn btn-success\" onclick=\"javascript:location.href='vm_virsh.php?action=domain-start&amp;uuid=$uuid'\">Start VM</button>";

                #echo " | <button class=\"btn btn-info\" onclick=\"javascript:location.href='vm_virsh.php?action=domain-edit&amp;uuid=$uuid'\">Edit XML</button>";

				#echo " | <button class=\"btn btn-hover\" onclick=\"javascript:location.href='vm_virsh.php?action=domain-edit&amp;uuid=$uuid'\">Edit XML</button>";

				
                if (!$lv->domain_is_running($res, $name))
                  #  echo " | <button class=\"btn btn-danger\" onclick=\"javascript:location.href='delvm.php?vmname=$name'\">Delete VM</button>";
                
				#else
                #    echo " | <a href=\"screenshot.php?uuid=$uuid\">printscreen</a>";
				
                #echo "</td></tr>";
				
				echo "</td></tr>";
            }
            ?>
        </table>
        <?php if ($ret) echo "<br /><pre>$ret</pre>";?>
    </div>



</div>
</div>

<?php require('bsfooter.php');?>