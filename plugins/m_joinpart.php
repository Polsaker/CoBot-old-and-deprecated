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
		if($irc->checkauth($who,4)!=1){$irc->SendPriv($channel,"05Error: No tienes los privilegios suficientes como para realizar esta operación"); return 0;}
		$partmsg="Salida ordenada por un administrador";$i=2;
		if(!isset($param[1]) || substr($param[1],0,1)!="#"){$chanout=$channel;$i=1;}else{$chanout=$param[1];}
		if((isset($param[1]) && substr($param[1],0,1)!="#")||isset($param[2])){ $i =1;
			$ts=$irc->jparam($param,$i);
			$partmsg=$ts;
			
		}
		$irc->SendCommand("PART $chanout :$partmsg");
	}
}
	?>
