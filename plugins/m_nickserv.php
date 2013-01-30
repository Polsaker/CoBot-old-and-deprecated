<?php 
	 $name="nickserv"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		if(!@isset($irc->hdf['433'])){$irc->hdf['433']=array();}
		array_push($irc->hdf['433'],array("nickserv","nickinuse")); 
		
		if(!@isset($irc->hdf['001'])){$irc->hdf['001']=array();}
		array_push($irc->hdf['001'],array("nickserv","nsid")); 
	}
	
	public function nickinuse(&$irc, $txt){
		$irc->nick.="_";
		$irc->SendCommand("NICK ".$irc->nick);
		if($irc->conf['irc']['nspass'] && $irc->conf['irc']['ghost'){$this->g=1;} // Activando el ghost
	}
	
	public function nsid(&$irc, $txt){
		if($irc->conf['nickserv']['nspass']){
			$irc->SendPriv("NickServ", "IDENTIFY {$irc->conf['nickserv']['nsuser']} {$irc->conf['nickserv']['nspass']}");
			if($this->g==1){ // Auto-ghost
				$irc->SendCommand("PRIVMSG NickServ :GHOST ".$irc->conf['irc']['nick']);
				$irc->nick=$irc->conf['irc']['nick'];
				sleep(1);$irc->SendCommand("NICK " .$irc->conf['irc']['nick']);
			}
		}
	}

	
}
