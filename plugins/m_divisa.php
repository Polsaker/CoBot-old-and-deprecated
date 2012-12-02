<?php 
	/*
	 * m_ping.php
	 * Agrega el comando ping.
	 */
	 $name="divisa"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'conv', 'divisa',array("convertir","divisa"));	
		$this->help['conv']='Convierte divisas. Sintaxis: conv <Divisa origen> <Divisa destino> <cantidad> (Las divisas deben estar en formato ISO 4217)';
	}

	public function conv(&$irc,$msg,$channel,$param,$who){
		if(!isset($param[3])){$irc->SendPriv($channel,"03Error: Faltan parámetros");return 0;}
		$f=file_get_contents("http://rate-exchange.appspot.com/currency?from=$param[1]&to=$param[2]&q=$param[3]");
		$js=json_decode($f);
		$div=$js->v;
		$irc->SendPriv($channel,"Convirtiendo $param[1] a $param[2]: $div");
	}
}
?>
