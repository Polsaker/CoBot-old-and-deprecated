<?php
$name="bot"; 
	$key="ee111t1t1172";
	class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			array_push($irc->connscript, "OPER ".$irc->conf['m_oper']['operuser']." ".$irc->conf['m_oper']['operpass']);
			array_push($irc->connscript, "MODE ".$irc->nick." +B");
		}
		
	}
?>
