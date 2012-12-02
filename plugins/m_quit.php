<?php 
	/*
	 * m_quit.php
	 * Agrega el comando quit.
	 */
	 $name="quit"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'quit', 'quit');	
		$irc->addcmd($this, 'restart', 'quit');
		$irc->addcmd($this, 'reconnect', 'quit');
		$this->help['quit']='Cierra el bot (requiere permisos nivel 10).';
		$this->help['quit_l']=10;
		$this->help['restart']='Reinicia el bot (requiere permisos nivel 10).';
		$this->help['restart_l']=10;
		$this->help['reconnect']='Reconecta el bot (requiere permisos nivel 10).';
		$this->help['reconnect_l']=10;
		
	}

	public function quit(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,10)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		$irc->disconn=$irc->conf['conn']['reconnect']+2;
		$irc->SendCommand("QUIT :[QUIT] Salida ordenada por un administrador");
	}
	public function reconnect(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,10)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		$irc->disconn=0;
		$irc->SendCommand("QUIT :[RECONNECT] Salida ordenada por un administrador");
	}
	
	public function restart(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,10)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		$irc->disconn=$irc->conf['conn']['reconnect']+2;
		$irc->SendCommand("QUIT :[RESTART] Salida ordenada por un administrador");
		exec("php restart.php");
	}
}
	?>
