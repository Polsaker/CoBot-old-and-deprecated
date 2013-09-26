<?php
/*
 * @name: Quit
 * @desc: Agrega el comando quit
 * @ver: 1.0
 * @author: MRX
 * @id: quit
 * @key: pinkflyingelephants
 *
 */

class pinkflyingelephants{
	public function __construct(&$core){
		$core->registerCommand("quit", "quit", "Desconecta del IRC y cierra el bot.", 10);
		$core->registerCommand("reconnect", "quit", "Desconecta del IRC y conecta de nuevo.", 10);
        
        // TODO: Restart.
	}
	
	public function quit(&$irc, $data, &$core){
        $irc->quit("[QUIT] Salida ordenada por un administrador.");
        die("Salida ordenada por un administrador");
	}
    
    public function reconnect(&$irc, $data, &$core){
        $irc->reconnect();
	}
    
	
}
