<?php
/*
 * @name: Nick
 * @desc: Comando para cambiar de nick
 * @ver: 1.0
 * @author: MRX
 * @id: nick
 * @key: subliminalmessagesonthecode
 *
 */

class subliminalmessagesonthecode{
	public function __construct(&$core){
		$core->registerCommand("nick", "nick", "Cambia el nick del bot. Sintaxis: nick <nuevonick>", 6);
	}
	
	public function nick(&$irc, $data, &$core){
		$irc->changeNick($data->messageex[1]);
	}
}
