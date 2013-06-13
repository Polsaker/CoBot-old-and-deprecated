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
		$this->help['ping']='Responde con PONG';
		$this->help['pong']='Responde con PING';
	}

	public function ping(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :PONG");}
	public function pong(&$irc,$msg,$channel,$param,$who){ $irc->SendCommand("PRIVMSG ".$channel." :PING");}
}
?>
