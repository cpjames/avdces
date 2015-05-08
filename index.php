<?php 

$host  = $_SERVER['HTTP_HOST'];
#$self = $_SERVER['PHP_SELF'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'dashboard.php';
header("Location: http://$host$uri/$extra");
exit;

#echo $host."<P>";
#echo $self."<P>"
#echo $uri."<p>";
#echo $extra."<p>";

?>

 