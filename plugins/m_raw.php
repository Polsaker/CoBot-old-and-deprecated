<?php 
	/*
	 * m_raw.php
	 * Envia comandos directamente al servidor.
	 */
	 $name="raw"; 
	$key="ee111t1t1172";
class ee111t1t1172{	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'raw', 'raw');	
		$this->help['raw']='Envia comandos raw al servidor. Requiere permisos de nivel 9 o superior.';
		$this->help['raw_l']=9;
	}

	public function raw(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,9)==1){$irc->SendCommand($irc->jparam($param,1));}
	}
}
