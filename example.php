<?php

include("class.BroadwayAPI.php");

$Broadway = new BroadwayAPI();

$Broadway->stream_ip 		= "192.168.1.46";
$Broadway->stream_profile 	= "";
$Broadway->channel_list 	= 1;

$Broadway->exportPlaylist("broadway.m3u");
$Broadway->exportEPG("broadway_epg.xml");