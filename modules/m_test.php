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
		$core->registerCommand("prueba", "test", "HACE PRUEBAS LOLOLO");
		$core->registerCommand("prueba2", "test", "HACE PRUEBAS", 2);
		$core->registerCommand("prueba3", "test");
	}
	
	public function prueba(&$irc, &$data){
        $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': No me pruebes D: ');
	}
	public function prueba2(&$irc, &$data){
        $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': Esta es una prueba VIP');
	}
	public function prueba3(&$irc, &$data){
        $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': Esta es una prueba oculta');
	}
	
}
