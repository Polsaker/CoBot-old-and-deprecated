<?php
/*
 * @name: Raw
 * @desc: Envia mensajes raw al servidor
 * @ver: 1.0
 * @author: MRX
 * @id: raw
 * @key: wooo
 *
 */

class wooo{
	public function __construct(&$core){
		$core->registerCommand("raw", "raw", "Envia comandos Raw al servidor. Sintaxis: raw <mensaje>", 9);
	}
	
	public function raw(&$irc, $data, $core){
		$ts=$core->jparam($data->messageex,1);
		$irc->_send($ts);
	}
}
