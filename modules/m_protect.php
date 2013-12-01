<?php
/*
 * @name: Auto-Protect
 * @desc: Agrega la funcionalidad de auto-protección (el bot se puede auto-invitar, auto-desbanear y auto-reopear usando chanserv)
 * @ver: 1.0
 * @author: MRX
 * @id: protect
 * @key: key
 *
 */

class key{
	private $tries=0;
	public function __construct(&$core){
        $core->registerMessageHandler('MODE', "protect", "modeprotect");
        $core->registerMessageHandler('474', "protect", "banprotect");
        $core->registerMessageHandler('KICK', "protect", "kickrejoin");
        $core->registerMessageHandler('JOIN', "protect", "joinchan");
        $core->registerMessageHandler('473', "protect", "invprotect");
	}
	
	public function modeprotect(&$irc, $data, $core){
		print_r($data);
		if(preg_match("&:.* MODE (.+) .*\-.*o.* .*".preg_quote($irc->_nick).".*&", $data->rawmessage, $m)){
			$irc->message(SMARTIRC_TYPE_QUERY, "ChanServ", "OP {$data->rawmessageex[2]}");
		}
	/*	if(($data->rawmessageex[3]=="-o") && ($irc->isMe($data->rawmessageex[4]))){
			$irc->message(SMARTIRC_TYPE_QUERY, "ChanServ", "OP {$data->rawmessageex[2]}");
		}*/
	}
	
	public function banprotect(&$irc, $data, $core){
		if(($irc->isMe($data->rawmessageex[2])) && ($this->tries<20)){
			$irc->message(SMARTIRC_TYPE_QUERY, "ChanServ", "UNBAN {$data->rawmessageex[3]}");
			$irc->join($data->rawmessageex[3]);
			$this->tries++;
		}
	}
	public function joinchan(&$irc, $data, $core){
		if($irc->isMe($data->nick)){
			$this->tries=0;
		}
	}
	
	public function invprotect(&$irc, $data, $core){
		if(($irc->isMe($data->rawmessageex[2])) && ($this->tries<20)){
			$irc->message(SMARTIRC_TYPE_QUERY, "ChanServ", "INVITE {$data->rawmessageex[3]}");
			$irc->join($data->rawmessageex[3]);
			$this->tries++;
		}
	}
	
	public function kickrejoin(&$irc, $data, $core){
		if($irc->isMe($data->rawmessageex[3])){
			$irc->join($data->rawmessageex[2]);
		}
	}
	
}
