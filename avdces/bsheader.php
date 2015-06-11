<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>AVDCES Dashboard</title>
	<!-- Bootstrap core CSS-->
	<link href="./css/bootstrap.min.css" rel="stylesheet">
	 
	 <link type="text/css" rel="stylesheet" href="css/style.css"/>
	<!-- Custom styles for this template -->
	
	<link href="css/dashboard.css" rel="stylesheet">
	
    <script src="./js/ie-emulation-modes-warning.js"></script>
	<script src="./js/jquery.min.js"></script>
	<script src="./js/bootstrap.js"></script>
	
	</head>
		
	 <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
	<!-- <nav class="navbar navbar-default  navbar-fixed-top"> -->
      <div class="container-fluid">
        <div class="navbar-header">
        	<a class="navbar-brand" href="index.php">
			A Virtual Distributed Computing for Simulation</a></li>						  	
        </div>
          
		<?php 
			$directoryURI = $_SERVER['REQUEST_URI'];
			$path = parse_url($directoryURI, PHP_URL_PATH);
			$components = explode('/',ltrim($_SERVER['REQUEST_URI'],'/'));
			$first_part = $components[1];
			$first_partx = explode('?',ltrim($first_part,'?'));
			$first_part = $first_partx[0];
			
			
		?>
		
		<div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            
			
			<li class=<?php if ($first_part=="dashboard.php")
						{echo "active"; } else  
						{echo "noactive";}
					   ?>>				
			<a href="dashboard.php">Dashboard</a></li>
            
			
			<li class=<?php 
						if ($first_part=="vm_vnc.php")
						{
							echo "active"; 
						}elseif ($first_part=="vnc_auto.php"){
							echo "active"; 
						}else{
							echo "noactive";}
					   ?>>				
			<a href="vm_vnc.php">VM VNC</a></li>
			
			
            <li class=<?php if ($first_part=="hostinfo.php")
						{echo "active"; } else  
						{echo "noactive";}
					   ?>>				
			<a href="hostinfo.php">Host Info</a></li>
			
			<li class=<?php if ($first_part=="vm_cpu_monitor.php")
						{echo "active"; } else  
						{echo "noactive";}
					   ?>>				
			<a href="vm_cpu_monitor.php">VM Monitor</a></li>
					       
			
			
          </ul>
		  
        </div>
        
    </nav>