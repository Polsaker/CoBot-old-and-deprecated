<?php
/*
 * @name: Regex Akick
 * @ver: 1.0
 * @author: MRX
 * @id: regexakick
 * @key: woop
 *
 */

class woop{
	public function __construct(&$core){
        $core->registerMessageHandler('PRIVMSG', "regexakick", "msgs");
        $core->registerMessageHandler('NOTICE', "regexakick", "msgs");
        $core->registerMessageHandler('JOIN', "regexakick", "joinlookup");
		$core->registerCommand("msgakick", "regexakick", "Maneja los akicks por expresiones regulares (por mensajes). Sintaxis: regexakick <add|del|list> <canal> <Expresión regular> (Nota: las expresiones regulares deben incluir el separador.)");
		$core->registerCommand("joinakick", "regexakick", "Maneja los akicks por expresiones regulares (Al entrar al canal, por máscara). Sintaxis: regexakick <add|del|list> <canal> <Expresión regular> (Nota: las expresiones regulares deben incluir el separador. Nota 2: la regex se analiza teniendo en cuenta la máscara del usuario (nick!user@host). )");
		try {
			$k = ORM::for_table('akicks')->find_one();
		}catch(PDOException $e){
			$query="CREATE TABLE 'akicks' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'regex' TEXT NOT NULL, 'type' INTEGER NOT NULL, 'channel' TEXT NOT NULL);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}
	
	public function msgs(&$irc, $data, $core){
		$k = ORM::for_table('akicks')->where('type', 1)->where('channel', strtolower($data->channel))->find_many();
		print_r($data);
		foreach($k as $akick){
			if(preg_match($akick->regex,$data->message)){
				#if($irc->isOpped($data->channel)){
					$irc->kick($data->channel, $data->nick);
				#}
			}
		}
	}
	
	public function joinlookup(&$irc, $data, $core){
		$k = ORM::for_table('akicks')->where('type', 2)->where('channel', strtolower($data->channel))->find_many();
		foreach($k as $akick){
			if(preg_match($akick->regex,$data->from)){
				#if($irc->isOpped($data->channel)){
					$irc->kick($data->channel, $data->nick);
				#}
			}
		}
	}
	
	public function msgakick(&$irc, $data, $core){
		if(!isset($data->messageex[1])){return 0;} // por que mostrar un mensaje de error ES MUCHO TRABAJO
		switch($data->messageex[1]){
			case "add":
				if(!isset($data->messageex[3])){return 0;}
				$n = ORM::for_table('akicks')->create();
				$n->channel = strtolower($data->messageex[2]);
				$n->type = 1; // 1 == de canal
				$n->regex = $core->jparam($data->messageex,3);
				$n->save();
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Akick por mensajes de canal añadido. ID #{$n->id}");
				break;
			case "del":
				if(!isset($data->messageex[2])){return 0;}
				$n = ORM::for_table('akicks')->where('type', 1)->where('id', $data->messageex[2])->find_one();
				if($n){$n->delete();$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Se ha eliminado el akick."); return 0;}
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "No se ha encontrado el akick o no es del tipo de mensaje de canal.");
				break;
			case "list":
				$n = ORM::for_table('akicks')->where('type', 1)->find_many();
				foreach($n as $akick){
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "#\2{$akick->id}\2 ({$akick->channel}): \2{$akick->regex}\2");
				}
				break;
		}
	}
	
	# Por que duplicar código es genial!
	public function joinakick(&$irc, $data, $core){
		if(!isset($data->messageex[1])){return 0;} // por que mostrar un mensaje de error ES MUCHO TRABAJO
		switch($data->messageex[1]){
			case "add":
				if(!isset($data->messageex[3])){return 0;}
				$n = ORM::for_table('akicks')->create();
				$n->channel = strtolower($data->messageex[2]);
				$n->type = 2; // 1 == de canal
				$n->regex = $core->jparam($data->messageex,3);
				$n->save();
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Akick añadido. ID #{$n->id}");
				break;
			case "del":
				if(!isset($data->messageex[2])){return 0;}
				$n = ORM::for_table('akicks')->where('type', 2)->where('id', $data->messageex[2])->find_one();
				if($n){$n->delete();$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Se ha eliminado el akick."); return 0;}
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "No se ha encontrado el akick o no es del tipo de entrada al canal.");
				break;
			case "list":
				$n = ORM::for_table('akicks')->where('type', 2)->find_many();
				foreach($n as $akick){
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "#\2{$akick->id}\2 ({$akick->channel}): \2{$akick->regex}\2");
				}
				break;
		}
	}
	
}
