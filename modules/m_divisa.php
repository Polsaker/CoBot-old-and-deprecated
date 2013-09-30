<?php
/*
 * @name: Divisas
 * @desc: Muestra la cotización de una divisa
 * @ver: 1.0
 * @author: MRX
 * @id: divisa
 * @key: amodkey
 *
 */

class amodkey{
	public function __construct(&$core){
        $core->registerCommand("conv", "divisa", "Muestra el precio de una divisa en otra divisa. Sintaxis: conv <Divisa origen> <Divisa destino> <cantidad> (Las divisas deben estar en formato ISO 4217)");
	}
	
	public function conv(&$irc, &$data, &$core){
		if(!isset($data->messageex[3])){$irc->message(SMARTIRC_TYPE_CHANNEL ,"03Error: Faltan parámetros");return 0;}
		$f=file_get_contents("http://rate-exchange.appspot.com/currency?from={$data->messageex[1]}&to={$data->messageex[2]}&q={$data->messageex[3]}");
		$js=json_decode($f);
		$div=$js->v;
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel,"Convirtiendo {$data->messageex[1]} a {$data->messageex[2]}: $div");

	}

}
