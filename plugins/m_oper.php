<?php
$name="oper"; 
	$key="ee111t1t1172";
	class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			//$irc->addcmd($this, 'kill', 'oper');	
			//$this->help['kill']='Desconecta a un usuario. Sintaxis: kill <nick> <razón>';
			//$this->help['kill_l']=6;
			array_push($irc->connscript, "OPER ".$irc->conf['m_oper']['operuser']." ".$irc->conf['m_oper']['operpass']);
			//array_push($irc->connscript, "MODE ".$irc->nick." +s +cC");
			array_push($irc->joinscript, "SAMODE &c +Uo ".$irc->nick." ".$irc->nick); //el primer &c se reemplaza por el canal.
			//if(!@is_array($irc->hdf['NOTICE'])){$irc->hdf['NOTICE']=array();}
			//array_push($irc->hdf['NOTICE'],array("oper","chkproxy")); 	//formato: $hdf[COMANDO][NUMERO][0]=MODULO
																		//								[1]=FUNCION
		}
		
	}
?>
