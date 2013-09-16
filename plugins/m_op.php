<?php 
	$name="op"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	private $chanprivs;
	private $kbanuser;
	private $kbanchan;
	private $kbanrsn;
	private $kban=-1; // -1=no hace nada; 0=kickban; 1=unban
	public function __construct(&$irc){	
		if(!$irc->conf['nickserv']['nspass']){return 0;}
		$irc->addcmd($this, 'op', 'op');	
		$irc->addcmd($this, 'deop', 'op');
		$irc->addcmd($this, 'voice', 'op');
		$irc->addcmd($this, 'devoice', 'op');
		$irc->addcmd($this, 'kick', 'op',array("patea","patear"));
		$irc->addcmd($this, 'kickban', 'op',array("ban","kban","kb"));
		$irc->addcmd($this, 'unban', 'op',array("ub"));
		$this->help['op']='Da op en el canal. Sintaxis: op [#Canal] <Nick>';$this->help['op_l']=5;
		$this->help['deop']='Quita op en el canal. Sintaxis: deop [#Canal] <Nick>';$this->help['deop_l']=5;
		$this->help['voice']='Da voz en el canal. Sintaxis: voice [#Canal] <Nick>';$this->help['voice_l']=5;
		$this->help['devoice']='Quita voz en el canal. Sintaxis: devoice [#Canal] <Nick>';$this->help['devoice_l']=5;
		$this->help['kick']='Echa a alguien del canal. Sintaxis: kick [#Canal] <Nick> [Razón]';$this->help['kick_l']=5;
		$this->help['kickban']='Echa y banea a alguien del canal. Sintaxis: kickban [#Canal] <Nick> [Razón]';$this->help['kickban_l']=5;
		$this->help['unban']='Elimina un baneo. Sintaxis: unban [#Canal] <Mascara/Nick>';$this->help['unban_l']=5;
		if(!@isset($irc->hdf['MODE'])){$irc->hdf['MODE']=array();}
		array_push($irc->hdf['MODE'],array("op","modestat")); 
		
		if(!@isset($irc->hdf['352'])){$irc->hdf['352']=array();}
		array_push($irc->hdf['352'],array("op","who")); 
	}
	
	public function who(&$irc,$txt){
		if($this->unban==-1){return 0;}
		if(preg_match("@:.+ 352 .+ .+ .+ (.+) .+ {$this->kbanuser} .*@",$txt,$m)){
			//print_r($m);
			if($this->unban==0){
				$irc->SendCommand("MODE {$this->kbanchan} +b *!*@{$m[1]}");
				$irc->SendCommand("KICK {$this->kbanchan} {$this->kbanuser} :{$this->kbanrsn}");
				unset($this->kbanrsn);
			}elseif($this->unban==1){
				$irc->SendCommand("MODE {$this->kbanchan} -b *!*@{$m[1]}");
			}
			$this->unban=-1;
			unset($this->kbanchan);
			unset($this->kbanuser);
		}
	}
	
	public function modestat(&$irc,$txt){
		if (preg_match('#:.+ MODE (.+) (.+) '.$irc->nick.'#',$txt, $matches)){
			$chan=strtolower($matches[1]);
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
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo dar op si no tengo op!!");return 0;}
		}
		if(!isset($param[1]) || (substr($param[1],0,1)=="#" && !isset($param[2]))){$user=$irc->mask2nick($who);}elseif(substr($param[1],0,1)=="#"){$user=$param[2];}else{$user=$param[1];}
		$irc->SendCommand("MODE $chan +o $user");
	}
	public function deop(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo quitar op si no tengo op!!");return 0;}
		}
		if(!isset($param[1]) || (substr($param[1],0,1)=="#" && !isset($param[2]))){$user=$irc->mask2nick($who);}elseif(substr($param[1],0,1)=="#"){$user=$param[2];}else{$user=$param[1];}
		$irc->SendCommand("MODE $chan -o $user");
	}
	public function voice(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo dar voz si no tengo op!!");return 0;}
		}
		if(!isset($param[1]) || (substr($param[1],0,1)=="#" && !isset($param[2]))){$user=$irc->mask2nick($who);}elseif(substr($param[1],0,1)=="#"){$user=$param[2];}else{$user=$param[1];}
		$irc->SendCommand("MODE $chan +v $user");
	}
	public function devoice(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo quitar voz si no tengo op!!");return 0;}
		}
		if(!isset($param[1]) || (substr($param[1],0,1)=="#" && !isset($param[2]))){$user=$irc->mask2nick($who);}elseif(substr($param[1],0,1)=="#"){$user=$param[2];}else{$user=$param[1];}
		$irc->SendCommand("MODE $chan -v $user");
	}
	
	public function kick(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo echar gente si no tengo op!!");return 0;}
		}
		if(substr($param[1],0,1)=="#"){$user=$param[2];$reason=$irc->jparam($param,3);}else{$user=$param[1];$reason=$irc->jparam($param,2);}
		if(!$reason){$reason=$irc->mask2nick($who);}
		$irc->SendCommand("KICK $chan $user :$reason");
	}
	
	public function kickban(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo echar gente si no tengo op!!");return 0;}
		}
		if(substr($param[1],0,1)=="#"){$user=$param[2];$reason=$irc->jparam($param,3);}else{$user=$param[1];$reason=$irc->jparam($param,2);}
		if(!$reason){$reason=$irc->mask2nick($who);}
		$this->kbanuser=$user;
		$this->kbanchan=$chan;
		$this->kbanrsn=$reason;
		$this->unban=0;
		$irc->SendCommand("WHO $user"); // Obtenemos los datos del usuario como para poder realizar el baneo (La respuesta es obtenida por la función who(&$irc, $txt))
	}
	
	public function unban(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,1,trim($channel))!=1){$irc->SendPriv($channel,"05Error: No tienes privilegios suficientes como para realizar esta operación");return 0;}
		if(substr($param[1],0,1)=="#"){$chan=$param[1];}else{$chan=$channel;}
		if($this->chanprivs[strtolower($chan)]>3){
			if($irc->is_loaded("atheme")){$irc->get_class("atheme")->cs_op($chan,$irc->nick);usleep(500000);}else{ // Nota: Existe la posibilidad de que el bot no pueda dar op, planear la manera de saber si ChanServ dio op al bot o no.
			$irc->SendPriv($channel,"05Error: No puedo desbanear si no tengo op!!");return 0;}
		}
		if(substr($param[1],0,1)=="#"){$user=$param[2];}else{$user=$param[1];}
		if(preg_match("#.+!.+@.+#",$user)){ // Si es directamente una mascara de baneo, desbaneamos...
			$irc->SendCommand("MODE $chan -b $user");
		}else{ // Si no.... talvez sea un usuario, hacemos un who.
			$this->kbanuser=$user;
			$this->kbanchan=$chan;
			$this->unban=1;
			$irc->SendCommand("WHO $user");
		}
	}

}
