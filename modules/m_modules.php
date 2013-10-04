<?php
/*
 * @name: Módulos
 * @desc: Carga y descarga módulos
 * @ver: 1.0
 * @author: MRX
 * @id: modules
 * @key: bofh
 *
 */

class bofh{
	public function __construct($core){
		$core->registerCommand("loadmod", "modules", "Carga un módulo. Sinaxis: loadmod <modulo>", 10, "*", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("unloadmod", "modules", "Descarga un módulo. Sinaxis: unloadmod <modulo>", 10, "*", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("reloadmod", "modules", "Descarga y carga un modulo. Sinaxis: reloadmod <modulo>", 10, "*", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
	}

	public function loadmod(&$irc, $data, &$core){
		if(!isset($data->messageex[1])){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Faltan parámetros!!");}// Por que enviar un mensaje de error al usuario es mucho trabajo
		$k=$core->loadModule($data->messageex[1]);
		switch($k){
			case 2: $r = "\00305Error:\003 El módulo tiene un formato incorrecto.";break;
			case -6: $r = "\00305Error:\003 No se ha encontrado el archivo en modules/.";break;
			case -2: $r = "\00305Error:\003 El módulo ya esta cargado.";break;
			case 3: $r = "\00305Error:\003 El módulo tiene errores de sintáxis.";break;
			case -3: $r = "\00305Error:\003 No se encontró la clase principal.";break;
			case 5: $r = "Se ha cargado el módulo";break;
		}
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}
	public function reloadmod(&$irc, $data, &$core){
		$this->unloadmod($irc,$data,$core);
		$this->loadmod($irc,$data,$core);
	}
	public function unloadmod(&$irc, $data, &$core){
		if(!isset($data->messageex[1])){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Faltan parámetros!!");}// Por que enviar un mensaje de error al usuario es mucho trabajo
		$k=$core->unloadModule($data->messageex[1]);
		switch($k){
			case 2: $r = "\00305Error:\003 El módulo tiene un formato incorrecto.";break;
			case -6: $r = "\00305Error:\003 No se ha encontrado el archivo en modules/.";break;
			case -2: $r = "\00305Error:\003 El módulo no esta cargado.";break;
			case 5: $r = "Se ha descargado el módulo";break;
		}
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}

	
}
