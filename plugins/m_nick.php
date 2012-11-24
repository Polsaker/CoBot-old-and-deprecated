<?php 
	/*
	 * m_nick.php
	 * Agrega el comando nick.
	 */
	 $name="nick"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'nick', 'nick');	
			$this->help['nick']='Cambia el nick del bot. Requiere permisos de nivel 7 o superior.';
			$this->help['nick_l']=7;
		}

		public function nick(&$irc,$msg,$channel,$param,$who)
		{
			if($irc->checkauth($who,7)==1){$irc->SendCommand("NICK ".$param[1]);}
		}
	}
?>
