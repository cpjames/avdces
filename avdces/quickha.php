<?php

require('bsheader.php');
require('bssidebar.php');
require('mysql_config.php');
?>

<div class="container-fluid">
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"> 



<h4 class="page-header">Create HA VM Group</h4>


<script type="text/javascript">
$(document).ready(function() {
	$('#wait_1').hide();
	$('#drop_1').change(function(){
	  $('#wait_1').show();
	  $('#result_1').hide();
      $.get("func.php", {
		func: "drop_1",
		drop_var: $('#drop_1').val()
      }, function(response){
        $('#result_1').fadeOut();
        setTimeout("finishAjax('result_1', '"+escape(response)+"')", 400);
      });
    	return false;
	});
});

function finishAjax(id, response) {
  $('#wait_1').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
</script>



<form class="navbar-form navbar-left" method="post" action="vm_create.php">
<p>
<input type="hidden" name="act" value="quick" />
<!--
<input type="text" class="form-control" placeholder="VM Name"
		id="input_vm_name" name="vm_name" required data-fv-notempty-message />
-->
</p>

<p>
<label>VM Number &nbsp &nbsp </label>
<select name="vm_num">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select>
</p>


<p>
<label>CPU Number &nbsp &nbsp </label>
<select name="vm_cpu">
<option value="1">1</option>
<option value="2">2</option>
</select>
</p>

<p>
<label>Memory &nbsp &nbsp </label>
<select name="vm_mem">
<option value="524288">512 MB</option>
<option value="1048576">1024 MB</option>
<option value="2097152">2048 MB</option>
</select>
</p>

<p>
<label>Template &nbsp &nbsp </label>
<select name="t_name">
<?php
$query_template = "select t_name from template";
$result_template = mysql_query($query_template)or die("can't select template");

for ($i=0; $i<mysql_num_rows($result_template);$i++){
$row_template = mysql_fetch_row($result_template);
list($t_name) = $row_template;
echo "<option value=".$t_name.">".$t_name."</option>";
}
?>

</select>
</p>


<p>
<label>VM Network &nbsp &nbsp </label>
<select name="vm_net" id="vm_net">
<?php
$query_net = "select net_name, net_network from net order by net_name";
$result_net = mysql_query($query_net)or die("can't select net");
for ($i=0; $i<mysql_num_rows($result_net);$i++){
$row_net = mysql_fetch_row($result_net);
list($vm_net, $net_network) = $row_net;
echo "<option value=".$vm_net.">".$vm_net." ".$net_network."</option>";
}
?>
</select>
</p>

<p>
<label>HA Group &nbsp &nbsp </label>
<select name="ha_name" id="ha_name">
<?php
$query = "select hg_name from hagroup";
$result = mysql_query($query) or die("can't select hagroup");


for ($k=0; $k < mysql_num_rows($result); $k++){
	$row = mysql_fetch_row($result);
	if (!$row){
		echo "<option value='$hg_name'>you should create HA group first</option>";
	}else{
	list($hg_name) = $row;
		echo "<option value='$hg_name'>$hg_name</option>";
	}
}
?>


</select>
</p>

<!--
<p>
<input type="text" class="form-control" placeholder="IP ( 2 ~ 254 )"
		id="input_vm_ip" name="vm_ip" required data-fv-notempty-message />
</p>
-->


<p><button  type="submit" class="btn btn-info">Create VM From Template</button> 


</form>


</div>
</div>


<?php 
mysql_close();
require('bsfooter.php');
?>