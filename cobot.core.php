<?php

class CoBot{
	public $irc;
	public $conf;
	public function __construct($config){
		$this->conf = $config;
		$this->irc = &new Net_SmartIRC();
		$this->irc->setDebug(SMARTIRC_DEBUG_ALL);
		$this->irc->setUseSockets(TRUE);

	}
	public function connect(){
		$this->irc->connect($this->conf['irc']['host'], $this->conf['irc']['port']);
		$this->irc->login($this->conf['irc']['nick'], 'CoBot/'.VER.'', 0, $this->conf['irc']['nick']);
		$this->irc->join($this->conf['irc']['channels']);
		$this->irc->listen();
		$this->irc->disconnect();


	}
}
