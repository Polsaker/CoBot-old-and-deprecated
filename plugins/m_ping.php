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
		$irc->addcmd($this, 'pong', 'pong');	
		$irc->addcmd($this, 'pig', 'pig');	
		$this->help['ping']='Responde con PONG';
		$this->help['pong']='Responde con PING';
		$this->help['pig']='Responde con POG';
	}

	public function ping(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :PONG");}
	public function pong(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :PING");}
	public function pig(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :POG");}
}
?>
