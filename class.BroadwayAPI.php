<?php
class BroadwayAPI{
		# Broadway network IP
		public $stream_ip		= "192.168.1.46";
		
		# Broadway stream profiles  / stream quality (default is empty = raw stream)
		# m2ts.
		#		 80k.LR              2300k.LC               2300k.MC               2000k.HD
		# 		150k.LR              4300k.LC               4300k.MC               4000k.HD
		# 		300k.LR              6300k.LC               6300k.MC               6000k.HD
		# 		300k.MR             11300k.LC               8300k.MC               8000k.HD
		# 		500k.MR             15300k.LC              15300k.MC              15000k.HD
		# 		700k.MR
		#		1000k.MR		     
		public $stream_profile 	= ""; // m2ts.4000K.HD
		
		#Broadway channellist to use
		public $channel_list	= 1;
				
		static $channels;
		public $playlist;
		public $epg;
		
		function __construct() {
			
			if(file_exists("config.ini")){
			
				$config = parse_ini_file("config.ini");
				
				$this->stream_ip 		= $config["stream_ip"];
				$this->stream_profile 	= $config["stream_profile"];
				$this->channel_list 	= $config["channel_list"];		
			}
		}
		/*
			Load Broadway channel listing / JSON
		*/
		function getChannelListing(){
			$listing =  $this->getData("http://".$this->stream_ip."/TVC/user/data/tv/channellists/");	
			
			foreach($listing as $list){
			
				$list->Items =  $this->getData("http://".$this->stream_ip."/TVC/user/data/tv/channellists/".$list->Id);	
				$data[] = $list;	
			}
			return $data;
		}
		
		/*
			Load Broadway Channellist / JSON
		*/
		function getChannelList(){
			return $this->getData("http://".$this->stream_ip."/TVC/user/data/tv/channellists/".$this->channel_list);	
		}
		
		/*
			Load Broadway Channel EPG / JSON
		*/

		function getChannelEPG($id){
			
		}
		
		/*
			Cleanup and combine channel list / epg
		*/
		function getChannels($loadEPG=true){
			
			if ($this->channels !== NULL && $loadEPG == true)
        		return $this->channels;
		
			$data = array();
			
			$list = $this->getChannelList();
			
			foreach((array)$list->Channels as $channel){
				
				if( $loadEPG == true){
					$prep 			= $this->getChannelEPG($channel->Id);
	 				$entries		= $prep[0]->Entries;
	 				$channel->EPG 	= $entries;
				}
				
				$data[] 		= $channel;
			}
			
			$this->channels =  $data;
			return 	 $data;
		}

		/*
			Build Playlist
			- Images use the name of the channel with empty spaces or slashes replaced width "-"
			- In XBMC the image folder is defined in the IPTV Simple PVR Addon
		*/
		function buildPlaylist(){
			
			 $data = $this->getChannels(false);
			 
			 $playlist = "#EXTM3U\n";

                foreach($data as $d ){
                   
                        $playlist .="#EXTINF:-1 tvg-logo=\"".str_replace('/',"-",str_replace(" ","-",$d->DisplayName)) .".png\", ".$d->DisplayName ."\n";
                        $playlist .=  "http://".$this->stream_ip."/basicauth/TVC/Preview?channel=".$d->Id . "&profile=".$this->stream_profile."\n";
				}
			$this->playlist = $playlist;
		}

		/*
			Export Playlist
		*/
		function exportPlaylist($filename = "broadway.m3u"){
			
			$this->buildPlaylist();
			
			file_put_contents($filename, $this->playlist);	
		}
		
		/*
			Build EPG
			- Improvising on the category, as the Broadway EPG does not provide any further information			
		*/
		function buildEPG(){
			
			$data = $this->getChannels();
			
			$epg ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
            $epg .="<tv>\n";

               foreach($data as $d ){
                    $epg .="<channel id=\"".$d->Id . "\">\n";
                    $epg .=" <display-name>".$d->DisplayName ."</display-name>\n";				
                    $epg .="</channel>\n";
			   }
			
			   foreach($data as $d ){
					
					 foreach($d->EPG as $prog ){
			            if(preg_match("/,/", $prog->ShortDescription, $matches)){
							$split = explode(",",  $prog->ShortDescription);
							if(count($split) == 3){
									$subtitle = $split[0]." / ".$split[2];
									$cat = $split[1];
							}else{
									$subtitle = $split[1];
									$cat = $split[0];
							}
							$epg .="<programme start=\"".date("YmdHis",($prog->StartTime/1000))." +0200\" stop=\"".date("YmdHis",($prog->EndTime/1000))." +0200\" channel=\"".$d->Id."\">\n";                           
							$epg .=" <title>".$prog->Title." (".$subtitle.")</title>\n";
							$epg .=" <desc>".$prog->LongDescription."</desc>\n";
							$epg .=" <category>".$cat ."</category>\n";
							$epg .="</programme>\n";	
						}else{ 
					   		$epg .="<programme start=\"".date("YmdHis",($prog->StartTime/1000))." +0200\" stop=\"".date("YmdHis",($prog->EndTime/1000))." +0200\" channel=\"".$d->Id."\">\n";                           
					   		$epg .=" <title>".$prog->Title."</title>\n";
					   		$epg .=" <desc>".$prog->LongDescription."</desc>\n";
					   		$epg .=" <category>".$prog->ShortDescription."</category>\n";
					   		$epg .="</programme>\n";
					 	}
					 }
				}
 			$epg .="</tv>\n";
			
 			$this->epg =  $epg;
		}
		
		/*
			Export EPG
		*/
		function exportEPG($filename = "broadway_epg.xml"){
			
			$this->buildEPG();
			
			file_put_contents($filename, $this->epg);	
		}
		
		function checkForBroadway(){		
			
			$url =  "http://".$this->stream_ip."/TVC/user/data/tv/channellists"; 
						
			switch($this->getResponseCode($url)){
				case 200:
					return true;
				break;
				
			}	
			return false;
			
		}
		/* Check, if stream is occupied */
		
		function isStreamAvailable(){
			
			$data = $this->getChannels(false);
			
			$url =  "http://".$this->stream_ip."/basicauth/TVC/Preview?channel=".$data[0]->Id . "&profile=".$this->stream_profile.""; 
						
			switch($this->getResponseCode($url)){
				case 200:
					return true;
				break;
				
			}	
			return false;
		}
		
		function getResponseCode($url) {
    		$headers = get_headers($url);
    		return substr($headers[0], 9, 3);
		}
		
		function getRedirectsToUri($uri)
		{
			$redirects = array();
			$http = stream_context_create();
			stream_context_set_params(
				$http,
				array(
					"notification" => function() use (&$redirects)
					{
						if (func_get_arg(0) === STREAM_NOTIFY_REDIRECTED) {
							$redirects[] = func_get_arg(2);
						}
					}
				)
			);
			file_get_contents($uri, false, $http);
			return $redirects;
		}

		/*
			Fetch data
		*/
		function getData($url){
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($ch, CURLOPT_URL, $url);
			
			$result=curl_exec($ch);
		
			curl_close($ch);		
			
			return json_decode($result);
		}
}