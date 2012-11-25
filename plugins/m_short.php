<?php 

	 $name="short"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'acortar', 'short');	
			$irc->addcmd($this, 'desacortar', 'short');	
			$this->help['acortar']='Acorta una URL. Sinaxis: acortar <googl|bitly> <URL> [j.mp|bit.ly|bitly.com]';
			$this->help['desacortar']='Des-acorta una URL';
		}

		public function acortar(&$irc,$msg,$channel,$param,$who)
		{
			if(($param[1]=="googl")||($param[1]=="g")||($param[1]=="1")){
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL,"https://www.googleapis.com/urlshortener/v1/url?key=".$irc->conf["m_google"]["api_key"]);
				curl_setopt($ch,CURLOPT_POST,1);
				curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$param[2])));
				curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				$result = curl_exec($ch);
				curl_close($ch);
				$j=json_decode($result,true);
				$short=($j['id']) ? $j['id']: false;
			}elseif(($param[1]=="bitly")||($param[1]=="b")||($param[1]=="2")){
				$res=file_get_contents("http://api.bit.ly/v3/shorten?apiKey=".$irc->conf['m_short']['bitly-api']."&login=".$irc->conf['m_short']['bitly-use']."&longUrl=".$param[2].(($param[3]) ? "&domain=". $param[3]: ""));
				$j=json_decode($res,true);
				$short=($j['data']['url']) ? $j['data']['url']: false;
			}
			
			if(@$short){
				$irc->SendCommand("PRIVMSG ".$channel." :Acortado: 10$short");
			}else{$irc->SendCommand("PRIVMSG ".$channel." :No se pudo acortar..");}

		}
		public function desacortar(&$irc,$msg,$channel,$param,$who)
		{
			if(($param[1]=="googl")||($param[1]=="g")||($param[1]=="1")){
				$res=file_get_contents("https://www.googleapis.com/urlshortener/v1/url?shortUrl=".$param[2]."&key=".$irc->conf["m_google"]["api_key"]);
				$j=json_decode($res,true);
				$short=($j['longUrl']) ? $j['longUrl']: false;
			}elseif(($param[1]=="bitly")||($param[1]=="b")||($param[1]=="2")){
				$res=file_get_contents("http://api.bit.ly/v3/expand?apiKey=".$irc->conf['m_short']['bitly-api']."&login=".$irc->conf['m_short']['bitly-use']."&shortUrl=".$param[2]);
				$j=json_decode($res,true);
				$short=($j['data']['expand'][0]['long_url']) ? $j['data']['expand'][0]['long_url']: false;
			}
			
			if(@$short){
				$irc->SendCommand("PRIVMSG ".$channel." :Desacortado: 10$short");
			}else{$irc->SendCommand("PRIVMSG ".$channel." :No se pudo desacortar..");}

		}
	}
?>

