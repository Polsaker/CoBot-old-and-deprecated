<?php 
	/*
	 * m_joinpart.php
	 * Agrega el comando join y part.
	 */
	 $name="joinpart"; 
	$key="ee111t1t1172";
class ee111t1t1172{	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'join', 'joinpart');	
		$irc->addcmd($this, 'part', 'joinpart');
		$this->help['join']='Hace que el bot entre a un canal (requiere permisos nivel 4 o superior).';
		$this->help['join_l']=4;
		$this->help['part']='Hace que el bot salga de un canal (requiere permisos nivel 4 o superior).';
		$this->help['part_l']=4;
	}

	public function join(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,4)==1){$irc->SendCommand("JOIN ".$param[1]);}
	}
	public function part(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,4)==1){$irc->SendCommand("PART ".$param[1]." :Salida ordenada por un administrador");}
	}
}
	?>
