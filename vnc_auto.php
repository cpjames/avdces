<?php


require('bsheader.php');
#require('bssidebar.php');

#echo "avdces";

$socket_port = $_GET['port'];

$vnc_port = $socket_port - 3000 ;

system("ssh root@127.0.0.1 'killall python'");

sleep(3);

system("ssh root@127.0.0.1 '/usr/bin/python /var/www/html/avdces/utils/websockify.py  -D $socket_port 127.0.0.1:$vnc_port &'");

?>





<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
	<meta name="apple-mobile-web-app-capable" content="yes" />
    
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    
    <link rel="apple-touch-startup-image" href="images/screen_320x460.png" />
    
    <link rel="apple-touch-icon" href="images/screen_57x57.png">
     
   <link rel="stylesheet" href="include/base.css" title="plain">
  
    <script src="include/util.js"></script>
</head>


<p>
<div class="container-fluid">



<br>
<br>

    <div id="noVNC_screen">
            <div id="noVNC_status_bar" class="noVNC_status_bar" style="margin-top: 0px;">
                <table border=0 width="100%"><tr>
                    <td><div id="noVNC_status" style="position: relative; height: auto;">
                        Loading
                    </div></td>
                    <td width="1%"><div id="noVNC_buttons">
                        <input type=button value="Send CtrlAltDel"
                            id="sendCtrlAltDelButton">
                        <span id="noVNC_xvp_buttons">
                        <input type=button value="Shutdown"
                            id="xvpShutdownButton">
                        <input type=button value="Reboot"
                            id="xvpRebootButton">
                        <input type=button value="Reset"
                            id="xvpResetButton">
                        </span>
                            </div></td>
                </tr></table>
            </div>
			
            <canvas id="noVNC_canvas" width="640px" height="20px">
                Canvas not supported.
            </canvas>
			
        </div>

        <script>
      

        // Load supporting scripts
        Util.load_scripts(["webutil.js", "base64.js", "websock.js", "des.js",
                           "keysymdef.js", "keyboard.js", "input.js", "display.js",
                           "jsunzip.js", "rfb.js", "keysym.js"]);

        var rfb;

        function passwordRequired(rfb) {
            var msg;
            msg = '<form onsubmit="return setPassword();"';
            msg += '  style="margin-bottom: 0px">';
            msg += 'Password Required: ';
            msg += '<input type=password size=10 id="password_input" class="noVNC_status">';
            msg += '<\/form>';
            $D('noVNC_status_bar').setAttribute("class", "noVNC_status_warn");
            $D('noVNC_status').innerHTML = msg;
        }
        function setPassword() {
            rfb.sendPassword($D('password_input').value);
            return false;
        }
        function sendCtrlAltDel() {
            rfb.sendCtrlAltDel();
            return false;
        }
        function xvpShutdown() {
            rfb.xvpShutdown();
            return false;
        }
        function xvpReboot() {
            rfb.xvpReboot();
            return false;
        }
        function xvpReset() {
            rfb.xvpReset();
            return false;
        }
        function updateState(rfb, state, oldstate, msg) {
            var s, sb, cad, level;
            s = $D('noVNC_status');
            sb = $D('noVNC_status_bar');
            cad = $D('sendCtrlAltDelButton');
            switch (state) {
                case 'failed':       level = "error";  break;
                case 'fatal':        level = "error";  break;
                case 'normal':       level = "normal"; break;
                case 'disconnected': level = "normal"; break;
                case 'loaded':       level = "normal"; break;
                default:             level = "warn";   break;
            }

            if (state === "normal") {
                cad.disabled = false;
            } else {
                cad.disabled = true;
                xvpInit(0);
            }

            if (typeof(msg) !== 'undefined') {
                sb.setAttribute("class", "noVNC_status_" + level);
                s.innerHTML = msg;
            }
        }

        function xvpInit(ver) {
            var xvpbuttons;
            xvpbuttons = $D('noVNC_xvp_buttons');
            if (ver >= 1) {
                xvpbuttons.style.display = 'inline';
            } else {
                xvpbuttons.style.display = 'none';
            }
        }

        window.onscriptsload = function () {
            var host, port, password, path, token;

            $D('sendCtrlAltDelButton').style.display = "inline";
            $D('sendCtrlAltDelButton').onclick = sendCtrlAltDel;
            $D('xvpShutdownButton').onclick = xvpShutdown;
            $D('xvpRebootButton').onclick = xvpReboot;
            $D('xvpResetButton').onclick = xvpReset;

            WebUtil.init_logging(WebUtil.getQueryVar('logging', 'warn'));
            document.title = unescape(WebUtil.getQueryVar('title', 'noVNC'));
            // By default, use the host and port of server that served this file
            host = WebUtil.getQueryVar('host', window.location.hostname);
            port = WebUtil.getQueryVar('port', window.location.port);

            // if port == 80 (or 443) then it won't be present and should be
            // set manually
            if (!port) {
                if (window.location.protocol.substring(0,5) == 'https') {
                    port = 443;
                }
                else if (window.location.protocol.substring(0,4) == 'http') {
                    port = 80;
                }
            }

            // If a token variable is passed in, set the parameter in a cookie.
            // This is used by nova-novncproxy.
            token = WebUtil.getQueryVar('token', null);
            if (token) {
                WebUtil.createCookie('token', token, 1)
            }

            password = WebUtil.getQueryVar('password', '');
            path = WebUtil.getQueryVar('path', 'websockify');

            if ((!host) || (!port)) {
                updateState('failed',
                    "Must specify host and port in URL");
                return;
            }

            rfb = new RFB({'target':       $D('noVNC_canvas'),
                           'encrypt':      WebUtil.getQueryVar('encrypt',
                                    (window.location.protocol === "https:")),
                           'repeaterID':   WebUtil.getQueryVar('repeaterID', ''),
                           'true_color':   WebUtil.getQueryVar('true_color', true),
                           'local_cursor': WebUtil.getQueryVar('cursor', true),
                           'shared':       WebUtil.getQueryVar('shared', true),
                           'view_only':    WebUtil.getQueryVar('view_only', false),
                           'onUpdateState':  updateState,
                           'onXvpInit':    xvpInit,
                           'onPasswordRequired':  passwordRequired});
            rfb.connect(host, port, password, path);
        };
        </script>

 </p>

</div>	


<?php require('bsfooter.php');?>