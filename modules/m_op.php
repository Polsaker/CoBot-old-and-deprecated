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
	private $kbreas;
	public function __construct($core){
		$core->registerCommand("op", "op", "Da OP en un canal. Sintaxis: op [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("deop", "op", "Quita OP en un canal. Sintaxis: deop [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("voice", "op", "Da voz en un canal. Sintaxis: voice [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommandAlias("v", "voice");
		$core->registerCommand("devoice", "op", "Quita voz en un canal. Sintaxis: devoice [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("kick", "op", "Kickea a una persona en un canal. Sintaxis: kick [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommandAlias("k", "kick");
		$core->registerCommand("kickban", "op", "Banea a alguien en un canal. Sintaxis: kickban [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommandAlias(array("kb", "ban"), "voice");
		$core->registerCommand("unban", "op", "Desanea a alguien en un canal. Sintaxis: unban [canal] [nick]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("topic", "op", "Cambia el topic en un canal. Sintaxis: topic [canal] [topic]", 5, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		
		/* Aliases: */
		$core->irc->setChannelSyncing(true);
		$core->registerMessageHandler('352', "op", "whorecv");
	}
	
	public function whorecv(&$irc, $data, $core){
		//print_r($data);
		if($data->rawmessageex[7] == $this->searchfor){
			echo "wooo--";
			if($this->ban==true){
				echo "wooo";
				$irc->ban($this->searchforchan, "*!*@".$data->rawmessageex[5]);
				$irc->kick($this->searchforchan, $this->searchfor,$this->kbreas);
			}else{
				$irc->unban($this->searchforchan, "*!*@".$data->rawmessageex[5]);
			}
			$this->searchfor = "";
			$this->searchforchan = "";
		}
	}
	
	public function unban_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
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
	
	public function kickban_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function kickban(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];$reason=$core->jparam($data->messageex,3);}else{$chan=$data->channel;$reason=$core->jparam($data->messageex,2);}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			if(preg_match("#@.+!.+@.*#", $user)){
				$irc->ban($chan,$user);
			}else{
				$this->ban = true;
				$this->searchfor = $user;
				$this->searchforchan = $chan;
				$this->kbreas = $reason;
				$irc->who($user);
			}
		}
	}
	
	public function kick_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function kick(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];$reason=$core->jparam($data->messageex,3);}else{$chan=$data->channel;$reason=$core->jparam($data->messageex,2);}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->kick($chan, $user, $reason);
		}
	}
	public function op_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function op(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->op($chan, $user);
		}
	}
	
	public function deop_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function deop(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->deop($chan, $user);
		}
	}
	
	public function voice_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function voice(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->voice($chan, $user);
		}
	}
	
	public function topic_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function topic(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];$t = $core->jparam($data->messageex,2);}else{$chan=$data->channel;$t = $core->jparam($data->messageex,1);}
		if($irc->isOpped($chan)){
			$irc->setTopic($chan, $t);
		}
	}
	
	public function devoice_priv(&$irc, $data, &$core){ return $this->privchk($irc,$data,$core);}
	public function devoice(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if(!isset($data->messageex[1]) || (substr($data->messageex[1],0,1)=="#" && !isset($data->messageex[2]))){$user=$data->nick;}elseif(substr($data->messageex[1],0,1)=="#"){$user=$data->messageex[2];}else{$user=$data->messageex[1];}
		if($irc->isOpped($chan)){
			$irc->devoice($chan, $user);
		}
	}
	
	
	/* Funcion interna para verificar privilegios! */
	
	private function privchk($irc, $data,$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if($core->authchk($data->from,5,$chan) == true){return true; echo "LOOOOOOOOOOOOOOOOO - " .$chan;}else{return false;}
	}
}
