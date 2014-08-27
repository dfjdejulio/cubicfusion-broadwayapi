<?php

include("class.BroadwayAPI.php");

$Broadway = new BroadwayAPI();

$listings 	= $Broadway->getChannelListing();
$config 	= parse_ini_file("config.ini");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>BroadwayAPI (PHP)</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="http://bootswatch.com/flatly/bootstrap.min.css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container"> 
  <!-- Static navbar -->
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
      <!--/.nav-collapse --> 
    </div>
    <!--/.container-fluid --> 
  </div>
  <div class="jumbotron">
    <h2>Settings</h2>
    <table class="table">
      <?php
	  foreach($config as $key => $var){
echo "<tr>
		<td>".$key."</td>
		<td>".$var."</td>		
		</tr>";
	  }
		?>
    </table>
    <h2>Channellists</h2>
    <table class="table">
      <?php
echo "<tr>
		<td>ID</td>
		<td>NAME</td>
		<td>CHANNELS</td>
		</tr>";
		
foreach($listings as $list){
		echo "<tr>
		<td>".$list->Id."</td>
		<td>".$list->DisplayName."</td>
		<td>".$list->Count."</td>
		</tr>";
}
?>
    </table>
  </div>
</div>
</body>
