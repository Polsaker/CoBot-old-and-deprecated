<?php
/*
* @name: Quotes
* @ver: 1.0
* @author: Richzendy
* @id: quote
* @key: quotes
*
*/

class quotes{
	private $registered;

	public function __construct(&$core){
		$core->registerCommand("quote", "quote", "Permite gestionar Quotes en el bot. Sintaxis: " . $core->conf['irc']['prefix'] . "quote [add|last|find|del|random] [mensaje de quote|número de quote]");
		$core->registerMessageHandler("307", "quote", "isregistered");
		try {
			$k = ORM::for_table('quotes')->find_one();
		}catch(PDOException $e){
			$query="CREATE TABLE 'quotes' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL,  'quote' TEXT NOT NULL, 'date' datetime default current_timestamp);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}

	public function quote(&$irc, $data, $core){
		$opt = $data->messageex;
		switch ($opt[1]) {
			case 'add': 
				if(!$core->authchk($data->from, 0, "*")) {
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "$data->nick, Debe estar registrado con el bot para poder agregar quote: " . $core->conf['irc']['prefix'] . "help auth");
				} else {
					$quote = str_replace("add ", '', $core->jparam( $data->messageex,1));
					try {
						$s = ORM::for_table('quotes')->create();
						$s->nick = strtolower($data->nick);
						$s->quote = $quote;
						$s->save();
						$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote agregada por $data->nick.");
					} catch(PDOException $e){
						$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Error agregando Quote: $e");
					}
				}
				break; 
			case 'last': 
				$n = ORM::for_table('quotes')->order_by_desc('id')->find_one();
				if(!$n){
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Todavía no se ha añadido ningún quote..");
				}else{
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$n->id}\002, agregada por \002{$n->nick}\002 de fecha \002{$n->date}\002: $n->quote");
				}
				break; 
			case 'find': 
				$n = ORM::for_table('quotes')->where_like('quote', "%$opt[2]%")->find_one();
				if($n) {
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$n->id}\002, agregada por \002{$n->nick}\002 de fecha \002{$n->date}\002: $n->quote");
				} else {
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "No existen Quotes que coincidan con el parámetro de búsqueda: \002{$opt[2]}\002.");
				}
				break;
                        case 'random':
                                $n = ORM::for_table('quotes')->raw_query('SELECT * FROM quotes ORDER BY RANDOM()')->find_one();
                               	if(!$n){
                                       	$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Todavía no se ha añadido ningún quote..");
                                }else{
                                        $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$n->id}\002, agregada por \002{$n->nick}\002 de fecha \002{$n->date}\002: $n->quote");                                }
                                break;
			case 'del':
				if(!$core->authchk($data->from, 0, "*")) {
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "$data->nick, Debe estar registrado con el bot para poder agregar quote: " . $core->conf['irc']['prefix'] . "help auth");
				} else {
					if(is_numeric($opt[2])) {
						$n = ORM::for_table('quotes')->where('id', $opt[2])->find_one();
						if($n->id){
							if($n->nick == strtolower($data->nick)) {
								$n->delete();
								$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$n->id}\002, eliminada por \002{$data->nick}\002");
							} else {
								$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Las quotes solo pueden ser eliminadas por quien las ha creado, la quote \002$n->id\002 fue creada por \002$n->nick\002");
							}
						} else {
							$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$opt[2]}\002, no existe");
						}
					} else {
						$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Error de sintaxis, debe usar: " . $core->conf['irc']['prefix'] . "quote del número");
					}
				}
				break; 
			default: 
				if(is_numeric($data->messageex[1])) {
				$n = ORM::for_table('quotes')->where('id', $data->messageex[1])->find_one();
					if($n){	
						$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$n->id}\002, agregada por \002{$n->nick}\002 de fecha \002{$n->date}\002: $n->quote");
					} else {
						$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Quote número \002{$data->messageex[1]}\002, no existe");
					}
				} else {
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Opción no disponible, intente " . $core->conf['irc']['prefix'] . "help quote");
				}
				break; 
		} 
	}
}
