<?php 
	/*
	 * m_google.php
	 * Agrega el comando google.
	 */
	 $name="google"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'google', 'google');	
			$this->help['google']='Realiza una búsqueda en google';
		}

		public function google(&$irc,$msg,$channel,$param,$who)
		{
			$i=1;
			$ts="";
			while(@isset($param[$i])){
				$ts.=$param[$i]. " ";
				$i++;
			}
			$ts=substr($ts,0,strlen($ts)-1);
			$gap=file_get_contents("https://www.googleapis.com/customsearch/v1?num=3&key=".$irc->conf['m_google']['api_key']."&cx=001206920739550302428:fozo2qblwzc&q=".urlencode($ts)."&alt=json");
			$jao=json_decode($gap);
			$resp="Resultados de la búsqueda en Google de \"".$ts."\": ".$jao->items[0]->title." 10".$jao->items[0]->link." ".$jao->items[1]->title." 10".$jao->items[1]->link." ".$jao->items[2]->title." 10".$jao->items[2]->link."";
			
			$irc->SendCommand("PRIVMSG ".$channel." :".$resp);
		}
	}
?>
