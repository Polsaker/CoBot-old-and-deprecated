<?php
/*
 * @name: Módulo de prueba
 * @desc: Modulo basico para hacer pruebas
 * @ver: 1.0
 * @author: MRX
 * @id: test
 * @key: asdfg
 *
 */

class asdfg{
	public function __construct(&$core){
		$core->registerCommand("test", "test");
	}
	
	public function test(&$irc, &$data){
		echo "NUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUU";
        $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': No me pruebes D: ');
	}
	
}
