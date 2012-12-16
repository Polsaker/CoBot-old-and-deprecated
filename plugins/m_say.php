<?php 
	/*
	 * m_say.php
	 * Agrega el comando say.
	 */
	 $name="say"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'say', 'say');	
		$this->help['say']='Hace que el bot hable a un canal. Requiere permisos nivel 1 o superior. Sintaxis: say [Canal] [Texto]';
		$this->help['say_l']=1;
	}

	public function say(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,1,trim($param[1]))==1){ 
			$i=2;
			$ts="";
			while(@isset($param[$i])){
				$ts.=$param[$i]." ";
				$i++;
			}
			$irc->SendCommand("PRIVMSG ".$param[1]." :".$ts);
		}
	}
}
	?>
