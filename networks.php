<?php
require('bsheader.php');
require('bssidebar.php');
require('config.php');
?>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">    
	
	<div class="list">
      
        <?php
            $ret = false;
            if ($subaction) {
                $name = $_GET['name'];
                
				if ($subaction == 'start'){
					
					exec("virsh -c qemu+ssh://root@127.0.0.1/system net-start $name");
					$tmp_url = $_SERVER['SERVER_NAME'];
					$tmp_uri = explode("?",$_SERVER['REQUEST_URI']);
					header("Location: http://$tmp_url$tmp_uri[0]");
					#echo "test".$tmp_uri[0]."<p>";
					#echo $tmp_url."<p>";
					
					/*
                    $ret = $lv->set_network_active($name, true)? 
					"Network has been started successfully" : 
					'Error while starting network: '.$lv->get_last_error();
					*/
                
				} elseif ($subaction == 'stop'){
                    exec("virsh -c qemu+ssh://root@127.0.0.1/system net-destroy $name");
					$tmp_url = $_SERVER['SERVER_NAME'];
					$tmp_uri = explode("?",$_SERVER['REQUEST_URI']);
					header("Location: http://$tmp_url$tmp_uri[0]");
					#header("Location: http://192.168.2.36/prototype/networks.php");
					
					/*
					$ret = $lv->set_network_active($name, false)?
					"Network has been stopped successfully" : 
					'Error while stopping network: '.$lv->get_last_error();
					*/
					
                } elseif (($subaction == 'dumpxml') || ($subaction == 'edit')) {
                    $xml = $lv->network_get_xml($name, false);

                   
				   if ($subaction == 'edit') {
                        if (@$_POST['xmldesc']) {
                            $ret = $lv->network_change_xml($name, $_POST['xmldesc']) ? "Network definition has been changed" :
                                                                                        'Error changing network definition: '.$lv->get_last_error();
                        }
                        else
                            $ret = 'Editing network XML description: <br /><br /><form method="POST"><table width="100%"><tr><td width="200px">Network XML description: </td>'.
                                        '<td><textarea name="xmldesc" rows="25" style="width:80%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
                                        '<input type="submit" value=" Edit domain XML description "></tr></form>';
                    }
                    else
                        $ret = 'XML dump of network <i>'.$name.'</i>:<br /><br />'.htmlentities($lv->get_network_xml($name, false));
                }
            }
			?>
            
			<?php
			
			echo "<h3>Networks Virsh Controls</h3>";
			
			echo "<hr>";
			
            $tmp = $lv->get_networks(VIR_NETWORKS_ALL);

            echo "<table class='table table-bordered'>
                <tr>
                 <th>Network name $spaces</th>
                 <th>$spaces Network state $spaces</th>
                 <th>$spaces Gateway IP Address $spaces</th>
                 <th>$spaces IP Address Range $spaces</th>
                 <th>$spaces Forwarding $spaces</th>
                 <th>$spaces DHCP Range $spaces</th>
                 <th>$spaces Actions $spaces</th>
                </tr>";

            for ($i = 0; $i < sizeof($tmp); $i++) {
                $tmp2 = $lv->get_network_information($tmp[$i]);
                if ($tmp2['forwarding'] != 'None')
                    $forward = $tmp2['forwarding'].' to '.$tmp2['forward_dev'];
                else
                    $forward = 'None';
                if (array_key_exists('dhcp_start', $tmp2) && array_key_exists('dhcp_end', $tmp2))
                    $dhcp = $tmp2['dhcp_start'].' - '.$tmp2['dhcp_end'];
                else
                    $dhcp = 'Disabled';
                $activity = $tmp2['active'] ? 'Active' : 'Inactive';

                $act = !$tmp2['active'] ? "<a href=\"?subaction=start&amp;name={$tmp2['name']}\">Start network</a>" :
                                          "<a href=\"?subaction=stop&amp;name={$tmp2['name']}\">Stop network</a>";
                $act .= " | <a href=\"?subaction=dumpxml&amp;name={$tmp2['name']}\">Dump network XML</a>";
                if (!$tmp2['active']) {
                    $act .= ' | <a href="?subaction=edit&amp;name='.$tmp2['name'].'">Edit network</a>';
                }

                echo "<tr>
                        <td>$spaces{$tmp2['name']}$spaces</td>
                        <td align=\"center\">$spaces$activity$spaces</td>
                        <td align=\"center\">$spaces{$tmp2['ip']}$spaces</td>
                        <td align=\"center\">$spaces{$tmp2['ip_range']}$spaces</td>
                        <td align=\"center\">$spaces$forward$spaces</td>
                        <td align=\"center\">$spaces$dhcp$spaces</td>
                        <td align=\"center\">$spaces$act$spaces</td>
                      </tr>";
            }
            echo "</table>";

            
			if ($ret)
                echo "<pre>$ret</pre>";

        ?>
    
</div>
