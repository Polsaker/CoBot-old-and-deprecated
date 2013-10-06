<?php
/*
 * @name: Ping
 * @desc: Agrega comandos para medir el lag.
 * @ver: 1.0
 * @author: MRX
 * @id: ping
 * @key: pinkflyingelephants
 *
 */

class pinkflyingelephants{
	public function __construct(&$core){
		$core->registerCommand("ping", "ping", "Responde con pong");
		$core->registerCommand("pong", "ping");
		$core->registerCommand("pig", "ping");
		$core->registerCommand("lag", "ping", "Mide la cantidad de lag.");
        
        $core->registerMessageHandler('NOTICE', "ping", "notha");
   	}
	
	public function ping(&$irc, $data, &$core){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': pong');}
	public function pong(&$irc, $data, &$core){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': ping');}
	public function pig(&$irc, $data, &$core){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': ¿Quieres un cerdo? ¡Ve a mirarte al espejo!');}
	public function lag(&$irc, $data, &$core){$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, "\001PING ".microtime(true)."\001");}
    
    public function notha(&$irc, $data, &$core){
        print_r($data);
        $rts = trim($data->messageex[1],"\001");
        $LAG = round(microtime(true) - $rts, 5) ;
        $irc->message(SMARTIRC_TYPE_NOTICE, $data->nick, "Su lag es de $LAG segundos.");
        
    }
    
}
