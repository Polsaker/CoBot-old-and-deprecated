<?php
/*
 * @name: SASL
 * @desc: Provee autenticación con NickServ via SASL
 * @ver: 1.0
 * @author: MRX
 * @id: sasl
 * @key: imagenius
 *
 */

class imagenius{
	public function __construct(&$core){
		if(!$core->conf['nickserv']['nspass']){return 0;}
        $core->registerMessageHandler('AUTHENTICATE', "sasl", "reg");
  //      $core->registerMessageHandler('AUTHENTICATE', "CAP", "cap");
        $core->onconnect.="CAP REQ :sasl\nAUTHENTICATE PLAIN";
	}
	
	public function cap(&$irc, $data, $core){
//		$irc->send("AUTHENTICATE PLAIN", SMARTIRC_CRYTICAL);
	}
	public function reg(&$irc, $data, $core){
		$irc->send("AUTHENTICATE ".base64_encode("{$core->conf['nickserv']['nsuser']}\0{$core->conf['nickserv']['nsuser']}\0{$core->conf['nickserv']['nspass']}"), SMARTIRC_CRITICAL);
		$irc->send("CAP END", SMARTIRC_CRITICAL);
	}
	
}
