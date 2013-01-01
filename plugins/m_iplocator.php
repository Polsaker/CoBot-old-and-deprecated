<?php 
	/*
	 * m_iplocator.php
	 * Permite geolocalizar IPs.
	 */
	 
	 $name="iplocator"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		private $gi;
		private $gi6;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'ip', 'iplocator');	
			$this->help['ip']='Geolocaliza una IP. Sintaxis: ip <IP/IPv6> [p] (si el segundo aprametro es "p", se buscarán proxys en la ip)';
			$this->gi = geoip_open("plugins/GeoLiteCity.dat",GEOIP_STANDARD);
			$this->gi6 = geoip_open("plugins/GeoLiteCityv6.dat",GEOIP_STANDARD);
		}

		public function ip(&$irc,$msg,$channel,$param,$who)
		{
			$ip=gethostbyname($param[1]);
			$v4=0;
			switch($this->ipdetector($ip)){
				case "IPV4":
					$v4=1;
					$record = geoip_record_by_addr($this->gi,$ip);
					break;
				case "IPV6":
					$record = geoip_record_by_addr_v6($this->gi6,$ip);
					break;
				case false:
					$irc->SendCommand("PRIVMSG ".$channel." :Dirección IP/Dominio no válido");
					return 0;
			}
			if(@$record==null){$irc->SendCommand("PRIVMSG ".$channel." :Error al procesar la IP.");return 0;}
			
			$hosti=gethostbyaddr($ip);
			if(($hosti!="")||($hosti!=$ip)){$host=" - ".$hosti."";}
			$resp= "IP: ".$ip."".$host." - País: ".$record->country_name."";
			if(@isset($irc->kvar[$record->country_code][$record->region])){ $resp.=" - Región: ".$irc->kvar[$record->country_code][$record->region]."";}
			if(@isset($record->city)){ $resp.=" - Ciudad: ".$record->city."";}
			@$resp.=" - Latitud: ".$record->latitude.", Longitud: ".$record->longitude."";
			@$ts=get_time_zone($record->country_code,$record->region);
			if(@isset($ts)){
				$resp.=" - Zona horaria: ".$ts." - Hora: ". date("H:i:s",now($ts))."";
			}
			if($param[2]=="p"){
				$irc->serv['myconn']=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
				mysql_select_db($irc->conf['db']['name']);
				$sql="SELECT * FROM proxys WHERE ip='".$ip."'";
				$rsx = mysql_query($sql);
				$i=0;
				
while($rowx=mysql_fetch_array($rsx)){$i=1;if($rowx['p']==0){$i=2;}else{$i=1;}}
				switch($i){
					case 0:	if($v4==1){ $re=$this->check_dnsbl($ip);} break;
					case 1: $re=1; break;
					case 2: $re=0; break;
				}
				if($re){$resp.=" - 04La IP está listada en el filtro de abusos/proxy";$sql="INSERT INTO proxys (ip,p) VALUES ('".$ip."',1)";}else{$sql="INSERT INTO proxys (ip,p) VALUES ('".$ip."',0)";}
				if($i==0){$rsx = mysql_query($sql);}
			}
			$irc->SendCommand("PRIVMSG ".$channel." :".utf8_encode($resp));
		}
		
		private function ipdetector($ip){ 
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false){
				return "IPV4";
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
				return false; 
			}
			return "IPV6";
		} 
		private function check_dnsbl($ip)
		{
			
$dnsbl_check=array("dnsbl.dronebl.org","torexit.dan.me.uk","http.dnsbl.sorbs.net","socks.dnsbl.sorbs.net","misc.dnsbl.sorbs.net","dnsbl.tornevall.org");
			if($ip){
				$rip=implode('.',array_reverse(explode(".",$ip))); 
				foreach($dnsbl_check as $val){
					if(checkdnsrr($rip.'.'.$val.'.','A'))
						return $rip.'.'.$val;
				}
			}
			return false;
		}
}
?>
