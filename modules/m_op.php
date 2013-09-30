<?php
/*
 * @name: Op
 * @desc: Agrega comandos de operador
 * @ver: 1.0
 * @author: MRX
 * @id: op
 * @key: ghasts
 *
 */
class ghasts{
	private $searchfor;
	private $searchforchan;
	public function __construct($core){
		$core->registerCommand("op", "op", "Da OP en un canal. Sintaxis: op [canal] [nick]", 5);
		$core->registerCommand("deop", "op", "Quita OP en un canal. Sintaxis: deop [canal] [nick]", 5);
		$core->registerCommand("voice", "op", "Da voz en un canal. Sintaxis: voice [canal] [nick]", 5);
		$core->registerCommand("devoice", "op", "Quita voz en un canal. Sintaxis: devoice [canal] [nick]", 5);
		$core->registerCommand("kick", "op", "Kickea a una persona en un canal. Sintaxis: kick [canal] [nick]", 5);
		$core->registerCommand("kickban", "op", "Banea a alguien en un canal. Sintaxis: kickban [canal] [nick]", 5);
		$core->registerCommand("unban", "op", "Desanea a alguien en un canal. Sintaxis: unban [canal] [nick]", 5);
		$core->irc->setChannelSyncing(true);
		$core->registerMessageHandler('352', "op", "whorecv");
	}
	
	public function whorecv(&$irc, $data, $core){
		print_r($data);
		if($data->messageex[1] == $this->searchfor){
			if($this->ban==true){
				$irc->ban($this->searchforchan, "*!*@".$data->rawmessageex[5]);
				$irc->kick($this->searchforchan, $this->searchfor);
			}else{
				$irc->unban($this->searchforchan, "*!*@".$data->rawmessageex[5]);
			}
			$this->searchfor = "";
			$this->searchforchan = "";
		}
	}
	
	public function unban(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			if(preg_match("·.+!.+@.*·", $user)){
				$irc->unban($chan,$user);
			}else{
				$this->ban = false;
				$this->searchfor = $user;
				$this->searchforchan = $chan;
				$irc->who($user);
			}
		}
	}
	
	public function kickban(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			if(preg_match("·.+!.+@.*·", $user)){
				$irc->ban($chan,$user);
			}else{
				$this->ban = true;
				$this->searchfor = $user;
				$this->searchforchan = $chan;
				$irc->who($user);
			}
		}
	}
	
	public function kick(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->kick($chan, $user);
		}
	}
	public function op(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->op($chan, $user);
		}
	}
	
	public function deop(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->deop($chan, $user);
		}
	}
	
	public function voice(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->voice($chan, $user);
		}
	}
	
	public function devoice(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->devoice($chan, $user);
		}
	}
}
