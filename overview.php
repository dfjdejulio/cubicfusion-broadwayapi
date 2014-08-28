<?php

include("class.BroadwayAPI.php");

$Broadway = new BroadwayAPI();

$listings 	= $Broadway->getChannelListing();
$config 	= parse_ini_file("config.ini");

$streamAvailable 	= $Broadway->isStreamAvailable();
$broadwayAvailable 	= $Broadway->checkForBroadway();


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>BroadwayAPI (PHP)</title>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="http://bootswatch.com/flatly/bootstrap.min.css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container"> 

  <div class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <a class="navbar-brand" href="#">BroadwayAPI (PHP)</a> </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li ><a href="<?php echo "http://".$Broadway->stream_ip; ?>" target="_blank">Your Broadway</a></li>
          <li><a href="http://<?php echo "http://".$Broadway->stream_ip; ?>/TVC.1343/ui/broadway/Admin.html">Admin</a></li>
          <li><a href="http://<?php echo "http://".$Broadway->stream_ip; ?>/TVC.1343/ui/Settings.html">Settings</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li ><a href="https://bitbucket.org/portalzine/broadwayapi/overview" target="_blank">Repository</a></li>
        </ul>
      </div>
     
    </div>
 
  </div>
  <div class="jumbotron">
   <h2>Status</h2>
   
   <div class="btn-group">
  <button type="button" <?php if($broadwayAvailable){echo 'class="btn btn-default"><span class="glyphicon glyphicon glyphicon-ok"></span> Broadway live';}else{ echo 'class="btn-danger"><span class="glyphicon glyphicon glyphicon-remove"></span>Broadway not live' ;} ?></button>
  <button type="button" <?php if($streamAvailable){echo 'class="btn btn-default"><span class="glyphicon glyphicon glyphicon-ok"></span> Stream available';}else{echo 'class="btn btn-danger"><span class="glyphicon glyphicon glyphicon-remove"></span> Stream in use' ;}  ?> </button>
  
</div>
  </div>
   <div class="jumbotron">
    <h2>Settings</h2>
    <form role="form">
    <table class="table table-striped">
      <?php
	  foreach($config as $key => $var){
echo "<tr>
		<td>".$key."</td>
		<td><input class='form-control' type='text' value='".$var."'></td>		
		</tr>";
	  }
		?>
    </table>
    </form>
    </div>
     <div class="jumbotron">
    <h2>Channellists</h2>
    <table class="table table-striped">
      <?php
echo "<thead><tr>

		<th>ID</td>
		<th>NAME</td>
		<th>CHANNELS</td>
		
		</tr></thead><tbody>";
		
foreach($listings as $list){
		echo "<tr>
		<td>".$list->Id."</td>
		<td>".$list->DisplayName."</td>
		<td>".$list->Count."</td>
		</tr>";
		
		
		echo "<tr>
		
		<td colspan='3'>";
		foreach($list->Items->Channels as $channel){
			echo '<span class="label label-primary">'.$channel->DisplayName.'</span> ';
		}
		echo "</td>
		
		</tr></tbody>";
}
?>
    </table>
    </div>
     <div class="jumbotron">
     <h2>Channel Logos</h2>
    <table class="table table-striped">
      <?php
echo "<thead><tr>

		<th>ID</td>
		<th>NAME</td>
		<th>CHANNELS</td>
		
		</tr></thead><tbody>";
		
foreach($listings as $list){
		echo "<tr>
		<td>".$list->Id."</td>
		<td>".$list->DisplayName."</td>
		<td>".$list->Count."</td>
		</tr>";
		
		
		
		foreach($list->Items->Channels as $channel){echo "<tr>
		
		<td colspan='2'>";
			echo ''.$channel->DisplayName.'</td> ';
			if(file_exists($config['channel_logos'].str_replace('/',"-",str_replace(" ","-",$channel->DisplayName)) .".png")){
			echo 	"<td class='active'><img width='80' src='/".$config['channel_logos'].str_replace('/',"-",str_replace(" ","-",$channel->DisplayName)) .".png'></td>";
			}else{
				echo 	"<td class='danger'>Missing: ".str_replace('/',"-",str_replace(" ","-",$channel->DisplayName)) .".png</td>";
				}
		echo "</td></tr>";
		}
		
	
		
		echo "</tbody>";
}
?>
    </table>
    
  </div>
  <center>&copy; Copyright 2014 <a href="http://www.portalzine.de" target="_blank">portalZINE NMN / Alexander Graef. All rights reserved.</center><br><br>
</div>
</body>
