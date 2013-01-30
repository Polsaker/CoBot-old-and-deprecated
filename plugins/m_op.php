<?php 
	$name="op"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	private $chanprivs;
	public function __construct(&$irc){	
		if(!$irc->conf['irc']['nspass']){return 0;}
		$irc->addcmd($this, 'op', 'op');	
		$irc->addcmd($this, 'deop', 'op');
		$irc->addcmd($this, 'voice', 'op');
		$irc->addcmd($this, 'devoice', 'op');
		$this->help['op']='Da op en el canal.';$this->help['op_l']=5;
		$this->help['deop']='Quita op en el canal.';$this->help['deop_l']=5;
		$this->help['voice']='Da voz en el canal.';$this->help['voice_l']=5;
		$this->help['devoice']='Quita voz en el canal.';$this->help['devoice_l']=5;
		if(!@isset($irc->hdf['MODE'])){$irc->hdf['MODE']=array();}
		array_push($irc->hdf['MODE'],array("op","modestat")); 
	}
	
	public function modestat(&$irc,$txt){
		if (preg_match('#:.+ MODE (.+) (.+) '.$irc->nick.'#',$txt, $matches)){
			$chan=$matches[1];
			$val=0;
			switch(substr($matches[2],1,1)){
				case "v":$val=1;break;
				case "h":$val=2;break;
				case "o":$val=3;break;
				case "a":$val=4;break;
				case "q":$val=5;break;
			}
			
			if(substr($matches[2],0,1)=="-"){
				if($this->chanprivs[$chan]==$val){$val=0;}
			}
			$this->chanprivs[$chan]=$val;
		}
	}
	
	public function op(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if($this->chanprivs[$channel]<3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($channel,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo dar op si no tengo op!!");return 0;}
		}
		if(!isset($param[1])){$user=$irc->mask2nick($who);}else{$user=$param[1];}
		$irc->SendCommand("MODE $channel +o $user");
		//if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_deop($channel,$irc->nick);} // Nos removemos el op

	}
	public function deop(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if($this->chanprivs[$channel]<3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($channel,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo dar op si no tengo op!!");return 0;}
		}		if(!isset($param[1])){$user=$irc->mask2nick($who);}else{$user=$param[1];}
		$irc->SendCommand("MODE $channel -o $user");
		//if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_deop($channel,$irc->nick);} // Nos removemos el op
	}
	
	public function voice(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if($this->chanprivs[$channel]<3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($channel,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo dar op si no tengo op!!");return 0;}
		}
		if(!isset($param[1])){$user=$irc->mask2nick($who);}else{$user=$param[1];}
		$irc->SendCommand("MODE $channel +v $user");
		//if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_deop($channel,$irc->nick);} // Nos removemos el op
	}
	public function devoice(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if($this->chanprivs[$channel]<3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($channel,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo dar op si no tengo op!!");return 0;}
		}
		if(!isset($param[1])){$user=$irc->mask2nick($who);}else{$user=$param[1];}
		$irc->SendCommand("MODE $channel -v $user");
		//if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_deop($channel,$irc->nick);} // Nos removemos el op

	}

}
