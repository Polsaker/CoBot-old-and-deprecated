<?php 
	 $name="wikichn"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	private $chans=array();
	public function __construct(&$irc){	
		$irc->addcmd($this, 'info', 'wikichn');	
		$irc->addcmd($this, 'nickassoc', 'wikichn', array("wikinick","na"));	
		$irc->addcmd($this, 'wikisearch', 'wikichn');	
		$irc->addcmd($this, 'preview', 'wikichn',array("lpreview"));	
		$irc->addcmd($this, 'contribs', 'wikichn',array("contribuciones", "aportes","contrib"));
		$irc->addcmd($this, 'chwadd', 'wikichn');
		$irc->addcmd($this, 'chwrem', 'wikichn');
		$this->help['info']='Proporciona información de un usuario de una wiki. Sintaxis: info <usuario>';
		$this->help['nickassoc']='Asocia un nick de IRC con un usuario de una wiki. Sintaxis nickassoc <usuario>';
		$this->help['wikisearch']='Busca un articulo en una wiki.';
		$this->help['preview']='Muestra una vista previa de hasta 250 caracteres de un artículo.';
		$this->help['lpreview']='Muestra una vista previa de hasta 440 caracteres de un artículo.';
		$this->help['contribs']='Muestra el número de contribuciones de un usuario en una determinada cantidad de dias. Sintaxis: contribs <dias> <usuario>.';
		$this->help['chwadd']='Asocia una wiki con un canal. Requiere permisos de nivel 5 o superior. Sintaxis: chwadd <canal> <wiki>.';
		$this->help['chwadd_l']=5;
		$this->help['chwrem_l']=5;
		
			$myconn=$irc->myConn();
			$rsx = mysql_query("SELECT * FROM wikichans",$myconn);
			while($rowx=mysql_fetch_array($rsx)){$this->chans[$rowx['chan']]=$rowx['wiki'];}
			mysql_close($myconn);
	}

	public function info(&$irc,$msg,$channel,$param,$who)
	{
		if(!@isset($this->chans[strtolower($channel)])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Esta función no esta habilitada aqui!");return 0;}
		if((!@isset($param[1]))||(@$param[1]=="")){
			$myconn=$irc->myConn();
			$sqlx="SELECT * FROM nickassoc WHERE ircnick='".$irc->mask2nick($who)."' AND chn='$channel'";
			$rsx = mysql_query($sqlx,$myconn) or die(exit("  - ERROR: verifique que las tablas mysql esten creadas."));
			$i=0;
			while($rowx=mysql_fetch_array($rsx)){ $param[1]=$rowx["wikinick"];$i=1; }
			if($i==0){$param[1]=$irc->mask2nick($who);}
			mysql_close($myconn);
		}else{
			$myconn=$irc->myConn();
			$sqlx="SELECT * FROM nickassoc WHERE ircnick='".$param[1]."' AND chn='$channel'";
			$rsx = mysql_query($sqlx,$myconn) or die(exit("  - ERROR: verifique que las tablas mysql esten creadas."));
			while($rowx=mysql_fetch_array($rsx)){
				$param[1]=$rowx["wikinick"];
			}
			mysql_close($myconn);
		}
		$i=1;
		$ts="";
		while(@isset($param[$i])){
			$ts.=$param[$i]. " ";
			$i++;
		}
		$ts=trim($ts);
		

		$jsn = file_get_contents("http://".$this->chans[$channel]."/api.php?action=query&list=users&ususers=".urlencode(str_replace(" ", "_",$ts))."&format=json&usprop=blockinfo|editcount|registration|gender|groups");		
		if($jsn==""){$irc->SendCommand("PRIVMSG ".$channel." :Error interno \002001\002. Notifique al administrador.");return 0;}
		$result=json_decode($jsn);
				
		if(@!isset($result->query->users[0]->missing)){
			if($result->query->users[0]->registration){
				$registration=date("d/m/Y H:i",strtotime(substr($result->query->users[0]->registration,0,strlen($result->query->users[0]->registration)-1)));
			}else{$registration="04N/D";}

			$w2 = file_get_contents("http://".$this->chans[$channel]."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=7&format=json"); $r2=json_decode($w2);
			$w3 = file_get_contents("http://".$this->chans[$channel]."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=30&format=json"); $r3=json_decode($w3);
			$w4 = file_get_contents("http://".$this->chans[$channel]."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=180&format=json"); $r4=json_decode($w4);
			
			$resp="Usuario ".$result->query->users[0]->name." - Registrado: ". $registration. " - Ediciones: ".$result->query->users[0]->editcount ." (Semana: ".$r2->userdailycontribs->timeFrameEdits.", Mes: ".$r3->userdailycontribs->timeFrameEdits.", ~6 meses:".$r4->userdailycontribs->timeFrameEdits.") - ";
			$i=0;
			$resp.="Grupos: ";
			while(@isset($result->query->users[0]->groups[$i+1])){
				$i++;
				if($result->query->users[0]->groups[$i]!="*"){
					$resp.=$result->query->users[0]->groups[$i]. ", ";
				}
			}
			$resp=substr($resp,0,strlen($resp)-2);
			switch(@$result->query->users[0]->gender){
				case 'male':
					$resp.=" - Sexo: 12Masculino ♂";
					break;
				case 'female':
					$resp.=" - Sexo: 13Femenino ♀";
					break;
				case 'unknown':
					$resp.=" - Sexo: 04N/D";
					break;
			}
			if(@isset($result->query->users[0]->blockid)){
				$resp.=" - 05Usuario bloqueado por ".$result->query->users[0]->blockedby.": ".$result->query->users[0]->blockreason;
			}
		}else{$resp="Usuario inexistente.";}
		$irc->SendCommand("PRIVMSG ".$channel." :".$resp);
	}
	public function nickassoc(&$irc,$msg,$channel,$param,$who)
	{
		if(!@isset($this->chans[strtolower($channel)])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Esta función no esta habilitada aqui!");return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$sqlx="INSERT INTO `nickassoc` (`ircnick` ,`wikinick`, `chn`) VALUES ('".$irc->mask2nick($who). "',  '".$param[1]."', '$channel')";
		if(!$rsx = mysql_query($sqlx,$myconn)){ $irc->SendCommand("PRIVMSG ".$channel." :Error interno \002002\002. Notifique al administrador."); return 0;}
		$irc->sendCommand("PRIVMSG ".$channel." :El nick ".$irc->mask2nick($who)." ha sido asociado con la cuenta wiki ".$param[1]);
		mysql_close($myconn);
	}
	public function wikisearch(&$irc,$msg,$channel,$param,$who)
	{
		if(!@isset($this->chans[strtolower($channel)])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Esta función no esta habilitada aqui!");return 0;}
		$i=1;
		$ts="";
		while(@isset($param[$i])){
			$ts.=$param[$i]. " ";
			$i++;
		}
		$ts=trim($ts);
		$xml = file_get_contents("http://".$this->chans[$channel]."/api.php?action=query&list=search&srsearch=".urlencode(str_replace(" ", "_",$ts))."&format=json&text=title&srlimit=5&srprop=snippet");		
		$result = json_decode($xml);
		$i=0;$r="";
		while(@isset($result->query->search[$i]->title)){
			$r.="\002\037".$result->query->search[$i]->title. "\037\002 \00310http://".$this->chans[$channel]."/?title=".urlencode(str_replace(" ", "_",$result->query->search[$i]->title))."\003 - ";$i++;
		}
		$r=substr($r,0,strlen($r)-3);
		$irc->SendCommand("PRIVMSG ".$channel." :Resultados: ".$r);
	}

	public function preview(&$irc,$msg,$channel,$param,$who){
		if(!@isset($this->chans[strtolower($channel)])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Esta función no esta habilitada aqui!");return 0;}
		$i=1;
		$ts="";
		while(@isset($param[$i])){
			$ts.=$param[$i]. " ";
			$i++;
		}
		$ts=trim($ts);
		$xml =  file_get_contents("http://".$this->chans[$channel]."/api.php?action=query&prop=extracts&exchars=440&titles=".urlencode(str_replace(" ", "_",$ts))."&format=json");		
		$result = json_decode($xml);
		foreach($result->query->pages as $key => $val){$pageid=$key;}
		if(@isset($result->query->pages->$pageid->missing)){$irc->SendCommand("PRIVMSG ".$channel.' :04Error: El artículo no existe.');return 0;}
		$rs=$result->query->pages->$pageid->extract;
		if(preg_match('#^.*REDIRECCIÓN (.+)$#',strip_tags($rs), $matches)){
			$this->preview($irc,$msg,$channel,array("",$matches[1]),$who);
			return 0;
		}
		if(preg_match('#^.*REDIRECT (.+)$#',strip_tags($rs), $matches)){
			$this->preview($irc,$msg,$channel,array("",$matches[1]),$who);
			return 0;
		}
		$rs=$this->wformat($rs);
		
		$irc->SendCommand("PRIVMSG ".$channel.' :"'.mb_convert_encoding($rs,$irc->conf['conn']['charset']).'"');
		$irc->SendCommand("PRIVMSG ".$channel.' :10http://'.$this->chans[$channel].'/index.php?title='.urlencode(str_replace(" ", "_",$ts)));
	}
	private function wformat($text){
		$text=str_replace("<b>","\002",$text);
		$text=str_replace("</b>","\002",$text);
		$text=str_replace("<i>","\026",$text);
		$text=str_replace("</i>","\026",$text);
		$text=str_replace("<u>","\037",$text);
		$text=str_replace("</u>","\037",$text);
		$text=str_replace("\n","||",$text);
		$text=mb_convert_encoding(strip_tags($text),$irc->conf['conn']['charset']);
		$text=mb_convert_encoding(html_entity_decode($text),$irc->conf['conn']['charset']);
		return $text;
	}
	public function contribs(&$irc,$msg,$channel,$param,$who){
		if(!@isset($this->chans[strtolower($channel)])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Esta función no esta habilitada aqui!");return 0;}
		
		$i=2;
		$ts="";
		while(@isset($param[$i])){
			$ts.=$param[$i]. " ";
			$i++;
		}
		$ts=trim($ts);
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		
		if($ts==""){
			$sqlx="SELECT * FROM nickassoc WHERE ircnick='".$irc->mask2nick($who)."' AND chn='$channel'";
			$rsx = mysql_query($sqlx,$myconn);
			$i=0;
			while($rowx=mysql_fetch_array($rsx)){ $ts=$rowx["wikinick"];$i=1; }
			if($i==0){/*$param[1]=$irc->mask2nick($who);*/$ts=$irc->mask2nick($who);}
		}else{
			$sqlx="SELECT * FROM nickassoc WHERE ircnick='".$ts."' AND chn='$channel'";

			$rsx = mysql_query($sqlx,$myconn);
			while($rowx=mysql_fetch_array($rsx)){
				$ts=$rowx["wikinick"];
			}
		}
		mysql_close($myconn);
		$ts=trim($ts);
		$xml = file_get_contents("http://".$this->chans[$channel]."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=".$param[1]."&format=json");		
		$result = json_decode($xml);
		if(!$xml){$irc->SendCommand("PRIVMSG ".$channel." :\00305Error\003 al procesar el comando");return 0;}
		$irc->SendCommand("PRIVMSG ".$channel." :Contribuciones del usuario \002".$ts."\002 en los últimos ".$param[1]." dia(s): \002".$result->userdailycontribs->timeFrameEdits."\002 de \002".$result->userdailycontribs->totalEdits."\002 contribuciones en total.");
	}
	public function chwadd(&$irc,$msg,$channel,$param,$who){
		if(!$irc->checkauth($who,5)==1){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: No posees los privilegios suficientes para usar este comando.");return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query("INSERT INTO wikichans (chan,wiki) VALUES ('".strtolower($param[1])."', '$param[2]')",$myconn);
		$rsx = mysql_query("SELECT * FROM wikichans",$myconn);
		while($rowx=mysql_fetch_array($rsx)){$this->chans=null; $this->chans[$rowx['chan']]=$rowx['wiki'];}
		mysql_close($myconn);
	}
	public function chwrem(&$irc,$msg,$channel,$param,$who){
		if(!$irc->checkauth($who,5)==1){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: No posees los privilegios suficientes para usar este comando.");return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query("DELETE FROM wikichans WHERE chan='".strtolower($param[1])."'",$myconn);
		sleep(1);
		$rsx = mysql_query("SELECT * FROM wikichans",$myconn);
		while($rowx=mysql_fetch_array($rsx)){$this->chans=null; $this->chans[strtolower($rowx['chan'])]=$rowx['wiki'];}
		mysql_close($myconn);
	}
}
?>
