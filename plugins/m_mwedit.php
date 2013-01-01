<?php 
	$name="mwedit"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	private $wikis=array();
	public function __construct(&$irc){	
		$irc->addcmd($this, 'ortografia', 'mwedit',array("orto","ortografía","ort"));	
		$irc->addcmd($this, 'palabra', 'mwedit');	
		$this->help['ortografia']='Corrige errores ortográficos en un artículo';$this->help['ortografia_l']=4;
		$this->help['palabra']='Agrega o elimina una palabra de la lista del corrector. Sintaxis: palabra <add|del> <palabra-mal> [palabra-bien]';$this->help['palabra_l']=4;
	}

	public function ortografia(&$irc,$msg,$channel,$param,$who){
		if(!$irc->checkauth($who,4,'mwedit')==1){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: No posees los privilegios suficientes para usar este comando.");return 0;}
		if(!isset($param[1])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Faltan parámetros.");return 0;}
		$i=1;$ts="";
		while(@isset($param[$i])){
			$ts.=$param[$i]. " ";$i++;
		}
		$ts=trim($ts);
		
		$dicc=array();
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query("SELECT * FROM wikichans WHERE `chan`='".strtolower($channel)."'",$myconn);
		if(mysql_num_rows($rsx)==0){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Esta función no esta habilitada aqui!");return 0;}
		
		$rowx=mysql_fetch_array($rsx);$wiki="http://".$rowx['chan']."/";
		$rsx = mysql_query("SELECT * FROM ortoerr",$myconn);
		while($rowx=mysql_fetch_array($rsx)){
			$dicc[$rowx['b']]=$rowx['g'];
		}
		mysql_close($myconn);
		
		$api=new mwApi($irc->conf['m_mwedit']['mwuser'],$irc->conf['m_mwedit']['mwpass'],$wiki,"CoBot, IRC Bot");
			
			$pc=$api->callApi("api.php?action=query&prop=revisions&titles=".urlencode($ts)."&rvprop=content");
			foreach($pc['query']['pages'] as $key => $val){	$cont=$val['revisions'][0]["*"];}
			$res = $this->corrector($cont,$dicc);
			if($res[1]==0){$irc->SendPriv($channel,"No se han encontrado errores de ortografía en el artículo.");return 0;}
			$edittoken=$api->get_token("edit");
			$post = "title=".urlencode($ts)."&action=edit&text=".urlencode($res[0])."&token=$edittoken&summary=".urlencode("Corrección ortográfica")."&bot=true";
			$r=$api->callApi($post,1);
			if($r['edit']['result']=="Success"){$resp="Se han encontrado y corregido $res[1] errores ortográficos";}else{$resp="Se han encontrado $res[1] errores ortográficos, pero no se han podido corregir: $r[edit][result]";}
			$irc->SendPriv($channel,$resp);
		unset($api);
	}
	
	public function palabra(&$irc,$msg,$channel,$param,$who){
		if(!$irc->checkauth($who,4,'mwedit')==1){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: No posees los privilegios suficientes para usar este comando.");return 0;}
		if(!isset($param[2])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Faltan parámetros.");return 0;}
		switch($param[1]){
			case "add":
				if(!isset($param[3])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Faltan parámetros.");return 0;}
				$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
					$rsx = mysql_query("INSERT INTO `ortoerr` (`b`, `g`) VALUES ('$param[2]', '$param[3]')",$myconn);
				mysql_close($myconn);
				break;
			case "del":
					$rsx = mysql_query("DELETE FROM ortoerr WHERE `b` = '$param[2]'");
				break;
			default:
				$irc->SendCommand("PRIVMSG $channel :\00305Error\003: El usuario parece no haber visto la ayuda de este comando");return 0;

		}
	}

	
	private function corrector($text, $diccionario){
		$b=array();$g=array();
		foreach($diccionario as $key => $val){
			array_push($b,$key);
			array_push($g,$val);
		}
		$c=0;
		$correct=str_replace($b,$g,$text,$c);
		return array($correct,$c);
	}


}
	?>
