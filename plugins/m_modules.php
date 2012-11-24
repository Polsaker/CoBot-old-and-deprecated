<?php 
	/*
	 * m_modules.php
	 * Agrega el manejo de módulos al paso.
	 */
	 
	$name="modules"; 
	$key="ee111t1t1171";
class ee111t1t1171{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'loadmod', 'modules');	
		$irc->addcmd($this, 'unloadmod', 'modules');
		$irc->addcmd($this, 'reloadmod', 'modules');
		$this->help['loadmod']= 'Carga un módulo (Requiere permisos nivel 10). Sintaxis: loadmod <modulo> <nombre del modulo>.';
		$this->help['loadmod_l']=10;
		$this->help['unloadmod']='Des-carga un módulo (requiere permisos nivel 10). ';
		$this->help['unloadmod_l']=10;
		$this->help['reloadmod']='Re-carga un módulo (requiere permisos nivel 10).';
		$this->help['reloadmod_l']=10;
		
	}

	public function loadmod(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,10)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		if(!@isset($param[1])){$irc->SendCommand("PRIVMSG $channel :05Error: Faltan parámetros"); return 0;}
		if(!file_exists("plugins/".$param[1])){$irc->SendCommand("PRIVMSG $channel :05Error: No pude encontrar el módulo en /plugins"); return 0;}
		$k=$irc->load($param[1]);
		if($k==-2){$irc->SendCommand("PRIVMSG $channel :05Error: El módulo ya está cargado!");}elseif($k==-1){$irc->SendCommand("PRIVMSG $channel :05Error: Error de formato en el módulo (¿talvez sea una versión antigua?)");}elseif($k==-3){$irc->SendCommand("PRIVMSG $channel :05Error: Parece que te has equivocado al poner el nombre del módulo (no puedo encontrar la clase!!)");}else{$irc->SendCommand("PRIVMSG $channel :El módulo se ha cargado exitosamente");}
	}
	public function unloadmod(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,10)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		if(!@isset($param[1])){$irc->SendCommand("PRIVMSG $channel :05Error: Faltan parámetros"); return 0;}

		$k=$irc->unload($param[1]);
		if($k==-1){$irc->SendCommand("PRIVMSG $channel :05Error: No se pudo descargar el módulo");}elseif($k==-2){$irc->SendCommand("PRIVMSG $channel :05Error: El módulo no está cargado!");}else{$irc->SendCommand("PRIVMSG $channel :Se ha descargado el módulo");}
	}
	public function reloadmod(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,10)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		if(!@isset($param[1])){$irc->SendCommand("PRIVMSG $channel :05Error: Faltan parámetros"); return 0;}
		$k=$irc->unload($param[1]);
		if($k==-1){$irc->SendCommand("PRIVMSG $channel :05Error: No se pudo descargar el módulo");}elseif($k==-2){$irc->SendCommand("PRIVMSG $channel :05Error: El módulo no está cargado!");}else{$irc->SendCommand("PRIVMSG $channel :Se ha descargado el módulo");}
		if(!file_exists("plugins/".$param[1])){$irc->SendCommand("PRIVMSG $channel :05Error: No pude encontrar el módulo en /plugins"); return 0;}
		$k=$irc->load($param[1]);
		if($k==-2){$irc->SendCommand("PRIVMSG $channel :05Error: El módulo ya está cargado!");}elseif($k==-1){$irc->SendCommand("PRIVMSG $channel :05Error: Error de formato en el módulo (¿talvez sea una versión antigua?)");}elseif($k==-3){$irc->SendCommand("PRIVMSG $channel :05Error: Parece que te has equivocado al poner el nombre del módulo (no puedo encontrar la clase!!)");}else{$irc->SendCommand("PRIVMSG $channel :El módulo se ha cargado exitosamente");}

	}
}
	?>
