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
	private $fnoticmh;
	public function __construct(&$core){
        $this->fnoticmh = $core->registerMessageHandler('NOTICE', "sasl", "fnotic");
	}
	public function fnotic(&$irc, $data, $core){
		$irc->_send("CAP REQ SASL", SMARTIRC_CRITICAL);
		$irc->_send("AUTHENTICATE PLAIN", SMARTIRC_CRITICAL);
		$irc->_send("AUTHENTICATE ".base64_encode("{$core->conf['nickserv']['nsuser']}\0{$core->conf['nickserv']['nsuser']}\0{$core->conf['nickserv']['nspass']}"), SMARTIRC_CRITICAL);
		$irc->_send("CAP END", SMARTIRC_CRITICAL);
		$core->unregisterMessageHandler($this->fnoticmh);
	}
	
}
