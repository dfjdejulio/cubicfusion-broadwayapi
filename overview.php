<?php

include("class.BroadwayAPI.php");

$Broadway = new BroadwayAPI();

$listings 	= $Broadway->getChannelListing();
$config 	= $Broadway->config;
$rename 	= $Broadway->rename;


$streamAvailable 	= $Broadway->isStreamAvailable();
$broadwayAvailable 	= $Broadway->checkForBroadway();

if(!empty($_POST['action'])){
	
	switch($_POST['action']){
	case "build":
		header("content-type:application/json");
		
		// Save playlist to file
		$Broadway->exportPlaylist("broadway.m3u");
	
		// Save EPG data to file
		$Broadway->exportEPG("broadway_epg.xml");
		
		$return['done'] = 1;
		$return["json"] = json_encode($return);
		echo json_encode($return);
		exit;
	break;
	case "check_stream":
		header("content-type:application/json");		
		$return['streamAvailable'] = $Broadway->isStreamAvailable();
		$return["json"] = json_encode($return);
		echo json_encode($return);
		exit;
	
	break;
	
	}
}


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
<script language="javascript">
$( document ).ready(function() {
 /*
  setInterval(function(){
    $.ajax({ url: "overview.php", type: 'post',
        data: {'action': 'check_stream'}, success: function(data){
      
	   if(data.streamAvailable == true){
		   	$("#streamStatus").addClass("btn-default").removeClass("btn-danger").text('<span class="glyphicon glyphicon glyphicon-ok"></span> Stream available'+data.streamAvailable);
	   }else{
	   	$("#streamStatus").addClass("btn-danger").removeClass("btn-default").text('<span class="glyphicon glyphicon glyphicon-remove"></span> Stream in use'+data.streamAvailable);
	   }
        
    }, dataType: "json"});
}, 5000);
*/
  $(".updateLocalFiles").click(function(){
	  $(".spinner").show();
	  $.ajax({
  		url: "overview.php",
		type: 'post',
        data: {'action': 'build'}
	  }).done(function(data,status) {
 		if(data.done == 1) $(".spinner").hide();
	  });
  })
 
});

</script>
<style>
.spinner {
	margin: 50px auto;
	width: 50px;
	height: 30px;
	text-align: center;
	font-size: 10px;
	display: none;
}
.spinner > div {
	background-color: #333;
	height: 100%;
	width: 6px;
	display: inline-block;
	-webkit-animation: stretchdelay 1.2s infinite ease-in-out;
	animation: stretchdelay 1.2s infinite ease-in-out;
}
.spinner .rect2 {
	-webkit-animation-delay: -1.1s;
	animation-delay: -1.1s;
}
.spinner .rect3 {
	-webkit-animation-delay: -1.0s;
	animation-delay: -1.0s;
}
.spinner .rect4 {
	-webkit-animation-delay: -0.9s;
	animation-delay: -0.9s;
}
.spinner .rect5 {
	-webkit-animation-delay: -0.8s;
	animation-delay: -0.8s;
}
 @-webkit-keyframes stretchdelay {
 0%, 40%, 100% {
-webkit-transform: scaleY(0.4)
}
 20% {
-webkit-transform: scaleY(1.0)
}
}
 @keyframes stretchdelay {
 0%, 40%, 100% {
 transform: scaleY(0.4);
 -webkit-transform: scaleY(0.4);
}
20% {
 transform: scaleY(1.0);
 -webkit-transform: scaleY(1.0);
}
}
</style>
</head>
<body><br>
<div class="container">
  <div class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <a class="navbar-brand" href="#">BroadwayAPI (PHP)</a> </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li data-toggle="tooltip" data-placement="bottom" title="Broadway LiveTV"><a href="<?php echo "http://".$Broadway->stream_ip; ?>" target="_blank">LiveTV</a></li>
          <li data-toggle="tooltip" data-placement="bottom" title="Broadway Administration"><a href="http://<?php echo "http://".$Broadway->stream_ip; ?>/TVC.1343/ui/broadway/Admin.html">Admin</a></li>
          <li data-toggle="tooltip" data-placement="bottom" title="Broadway Settings"><a href="http://<?php echo "http://".$Broadway->stream_ip; ?>/TVC.1343/ui/Settings.html">Settings</a></li>
          <li data-toggle="tooltip" data-placement="bottom" title="Update Playlist & EPG files"><a href="#" class="updateLocalFiles">Update Files</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li ><a href="https://bitbucket.org/portalzine/broadwayapi/overview" target="_blank">Repository</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="spinner">
    <div class="rect1"></div>
    <div class="rect2"></div>
    <div class="rect3"></div>
    <div class="rect4"></div>
    <div class="rect5"></div>
  </div>
  <ul class="nav nav-tabs" role="tablist">
    <li class="active" ><a href="#home" role="tab" data-toggle="tab">Home</a></li>
    <li><a href="#setting" role="tab" data-toggle="tab">Settings</a></li>
    <li><a href="#list" role="tab" data-toggle="tab">Channel Lists</a></li>
    <li><a href="#logos" role="tab" data-toggle="tab">Channels & Logos</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="home">
      <div class="jumbotron">
        <h2>Status</h2>
        <div class="btn-group"> <button id="networkStatus" type="button" <?php if($broadwayAvailable){echo 'class="btn btn-default"><span class="glyphicon glyphicon glyphicon-ok"></span> Broadway live';}else{ echo 'class="btn-danger"><span class="glyphicon glyphicon glyphicon-remove"></span>Broadway not live' ;} ?>
          </button>
          <button type="button" id="streamStatus" <?php if($streamAvailable){echo 'class="btn btn-default"><span class="glyphicon glyphicon glyphicon-ok"></span> Stream available';}else{echo 'class="btn btn-danger"><span class="glyphicon glyphicon glyphicon-remove"></span> Stream in use' ;}  ?> 
          </button>
        </div>
        <br><br>
         <h2>Exports</h2>
        <table class="table table-striped">
         <tr>
              <td>Playlist (m3u)<br>
                <span class="label label-primary"><?php echo date("d.m.Y H:i:s.", filectime("broadway.m3u")); ?></span></td>
              <td><input class='form-control' type='text' value='http://<?php echo $_SERVER["SERVER_NAME"]; ?>/broadway.m3u'></td>
            </tr>
            <tr>
              <td>XMLTV<br>
                <span class="label label-primary"> <?php echo date("d.m.Y H:i:s.", filectime("broadway_epg.xml")); ?></span></td>
              <td><input class='form-control' type='text' value='http://<?php echo $_SERVER["SERVER_NAME"]; ?>/broadway_epg.xml'></td>
            </tr>
        </table>
      </div>
    </div>
    <div class="tab-pane" id="setting">
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
    </div>
    <div class="tab-pane" id="list">
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
		echo "<tr class='success'>
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
    </div>
    <div class="tab-pane" id="logos">
      <div class="jumbotron">
        <h2>Channel Logos</h2>
        <table class="table table-striped">
            <form id="logoForm">
          
          <?php
echo "<thead><tr >

		<th>ID</td>
		<th>NAME</td>
		<th>CHANNELS</td>
		
		</tr></thead><tbody>";
		
foreach($listings as $list){
		echo "<tr class='success'>
		<td>".$list->Id."</td>
		<td>".$list->DisplayName."</td>
		<td>".$list->Count."</td>
		</tr>";
		
		
		
		foreach($list->Items->Channels as $channel){echo "<tr>
		
		<td colspan='2'>";
			echo '<h4>'.utf8_decode($channel->DisplayName).'</h4>
<small>[ID: '.$channel->Id.']</small><br>
<input class="form-control" type="text" name="rename['.$channel->DisplayName.']" value="'.$rename[$channel->DisplayName].'" placeholder="Rename"></td>';
			if(file_exists($config['channel_logos'].$Broadway->cleanString($channel->DisplayName) .".png")){
			echo 	"<td class='active'><img width='80' src='/".$config['channel_logos'].$Broadway->cleanString($channel->DisplayName) .".png'></td>";
			}else{
				echo 	"<td class='danger'>Missing: ".$Broadway->cleanString($channel->DisplayName) .".png</td>";
				}
		echo "</td></tr>";
		}
		
	
		
		echo "</tbody>";
}
?>
        </table>
        </form>
      </div>
    </div>
  </div>
  <center>
    &copy; Copyright 2014 <a href="http://www.portalzine.de" target="_blank">portalZINE NMN / Alexander Graef. All rights reserved.
  </center>
  <br>
  <br>
</div>
<script>$('.navbar-nav li').tooltip();</script>
</body>
</html>
