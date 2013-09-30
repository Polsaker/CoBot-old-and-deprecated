<?php
/*
 * @name: Acortador
 * @desc: Acorta links
 * @ver: 1.0
 * @author: MRX
 * @id: short
 * @key: bofh
 *
 */

class bofh{
	public function __construct($core){
		$core->registerCommand("acortar", "short", "Acorta una URL. Sinaxis: acortar <googl|bitly> <URL> [j.mp|bit.ly|bitly.com]");
		$core->registerCommand("desacortar", "short", "Descorta una URL. Sinaxis: desacortar <googl|bitly> <URL> [j.mp|bit.ly|bitly.com]");
	}
	
	public function acortar(&$irc, $data, &$core){
			if(($data->messageex[1]=="googl")||($data->messageex[1]=="g")||($data->messageex[1]=="1")){
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,"https://www.googleapis.com/urlshortener/v1/url?key=".$core->conf["m_google"]["api_key"]);
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$data->messageex[2])));
			curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			$result = curl_exec($ch);
			curl_close($ch);
			$j=json_decode($result,true);
			$short=($j['id']) ? $j['id']: false;
		}elseif(($data->messageex[1]=="bitly")||($data->messageex[1]=="b")||($data->messageex[1]=="2")){
			$res=file_get_contents("http://api.bit.ly/v3/shorten?apiKey=".$core->conf['m_short']['bitly-api']."&login=".$core->conf['m_short']['bitly-user']."&longUrl=".$data->messageex[2].(($data->messageex[3]) ? "&domain=". $data->messageex[3]: ""));
			$j=json_decode($res,true);
			$short=($j['data']['url']) ? $j['data']['url']: false;
		}
		
		if(@$short){
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Acortado: 10$short");
		}else{$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel,"No se pudo acortar..");}

	}
	public function desacortar(&$irc, $data, &$core){
		if(($data->messageex[1]=="googl")||($data->messageex[1]=="g")||($data->messageex[1]=="1")){
			$res=file_get_contents("https://www.googleapis.com/urlshortener/v1/url?shortUrl=".$data->messageex[2]."&key=".$core->conf["m_google"]["api_key"]);
			$j=json_decode($res,true);
			$short=($j['longUrl']) ? $j['longUrl']: false;
		}elseif(($data->messageex[1]=="bitly")||($data->messageex[1]=="b")||($data->messageex[1]=="2")){
			$res=file_get_contents("http://api.bit.ly/v3/expand?apiKey=".$core->conf['m_short']['bitly-api']."&login=".$core->conf['m_short']['bitly-user']."&shortUrl=".$data->messageex[2]);
			$j=json_decode($res,true);
			$short=($j['data']['expand'][0]['long_url']) ? $j['data']['expand'][0]['long_url']: false;
		}
		
		if(@$short){
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel,"Desacortado: 10$short");
		}else{$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel,"No se pudo desacortar..");}


	}

}
