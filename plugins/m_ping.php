<?php 
	/*
	 * m_ping.php
	 * Agrega el comando ping.
	 */
	 $name="ping"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'ping', 'ping');	
		$irc->addcmd($this, 'pong', 'ping');	
		$irc->addcmd($this, 'pig', 'ping');	
		$this->help['ping']='Envia un CTCP PING y cuenta los segundos pasados hasta la respuesta';
		$this->help['pong']='Responde con PING';
		$this->help['pig']='Responde con POG';
                if(!@isset($irc->hdf['NOTICE'])){$irc->hdf['NOTICE']=array();}
                array_push($irc->hdf['NOTICE'],array("ping","pingcom"));      
	}

	public function ping(&$irc,$msg,$channel,$param,$who){ 
		$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :\001PING ".microtime(true)."\001");	
	}

	public function pong(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :PING");}
	public function pig(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :".$irc->mask2nick($who).", Necesitas un cerdo? ve a verte al espejo!");}

	public function pingcom(&$irc,$txt){
		if(preg_match('@^:(.+) NOTICE .+ :PING (.+)@', $txt, $m)){
			@$ppl = $irc->mask2nick($m[1]);
			@$msg = $m[2];
			$LAG = round(microtime(true) - $msg, 5) ;
			# Para no utilizar el 100% de la cpu del sistema, la clase principal posee un
			# sleep(1). Eso retrasa el calculo del lag en aproximadamente 1.0004 segundos.
			$LAG = $LAG -1;
			$irc->SendCommand("NOTICE ".$ppl." :Su LAG es de ".round($LAG,4)." segundos");
		}
	}
}
?>
