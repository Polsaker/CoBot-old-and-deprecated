<?php
/*
 * @name: NickServ
 * @desc: Provee autenticación con NickServ
 * @ver: 1.0
 * @author: MRX
 * @id: nickserv
 * @key: imagenius
 *
 */

class imagenius{
	public function __construct(&$core){
		if(!$core->conf['nickserv']['nspass']){return 0;}
        $core->registerMessageHandler('001', "nickserv", "identify");
	}
	
	public function identify(&$irc, $data, $core){
		$irc->message(SMARTIRC_TYPE_QUERY, "NickServ", "IDENTIFY ".$core->conf['nickserv']['nsuser']." ".$core->conf['nickserv']['nspass']);
	}
	
}
