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
		$div=$this->conversor_divisas($param[1],$param[2],$param[3]);
		$irc->SendPriv($channel,"Convirtiendo de $param[1] a $param[2]: $div");
	}
	private function conversor_divisas($divisa_origen, $divisa_destino, $cantidad) {
		$cantidad = urlencode($cantidad);
		$divisa_origen = urlencode($divisa_origen);
		$divisa_destino = urlencode($divisa_destino);
		$url = "http://www.google.com/ig/calculator?hl=en&amp;q=$cantidad$divisa_origen=?$divisa_destino";
		$rawdata = file_get_contents($url);
		$data = explode('"', $rawdata);
		$data = explode(' ', $data['3']);
		$var = $data['0'];
		return round($var,2);
	}
}
?>
