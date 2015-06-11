<?php

	$ip = '127.0.0.1';
    
	
	error_reporting(0);
    require('libvirt.php');
    #$lv = new Libvirt('qemu://'.$ip.'/system');
    $lv = new Libvirt('qemu+ssh://root@'.$ip.'/system');
    $hn = $lv->get_hostname();
    if ($hn == false)
        die('Cannot open connection to hypervisor</body></html>');

    $action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
    $subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : '';

    if (($action == 'get-screenshot') && (array_key_exists('uuid', $_GET))) {
        if (array_key_exists('width', $_GET) && $_GET['width'])
            $tmp = $lv->domain_get_screenshot_thumbnail($_GET['uuid'], $_GET['width']);
        else
            $tmp = $lv->domain_get_screenshot($_GET['uuid']);

        if (!$tmp){
            echo $lv->get_last_error().'<br />';
        }else {
            Header('Content-Type: image/png');
            die($tmp);
        }
    }

    if($action){
        if( $action == 'domain-vnc'){
            $vmname = $_GET['vmname'];
            $res = $lv->get_domain_by_name($vmname);
            $vnc = $lv->domain_get_vnc_port($res);

            $port = (int)$vnc + 16100;
            
            $lsof = exec("lsof -i tcp:$port");
            if(empty($lsof)){
                exec("/data/noVNC/utils/websockify.py -D $port $ip:$vnc");
            }

            header('Location:http://'.$ip.':6080/vnc_auto.html?host='.$ip.'&port='.$port);
        }
    }

?>