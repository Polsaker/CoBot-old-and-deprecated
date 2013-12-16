<?php
/*
 * @name: Módulo MediaWiki
 * @ver: 1.0
 * @author: MRX
 * @id: wikichan
 * @key: asdfg
 *
 */

class asdfg{
	public function __construct(&$core){
		$core->registerCommand("chwadd", "wikichan", "Activa funciones wiki en un canal. Sintaxis: chwadd <#canal> <ruta a la API mediawiki>", 5);
		$core->registerCommand("chwrem", "wikichan", "Desctiva funciones wiki en un canal. Sintaxis: chwrem <#canal>", 5);
		$core->registerCommand("info", "wikichan", "Miestra información de un usuario wiki. Sintaxis: info <usuario>");
		$core->registerCommand("preview", "wikichan", "Muestra los primeros 440 caracteres de un articulo. Sintaxis: preview <Nombre del articulo>");
		$core->registerCommand("wikisearch", "wikichan", "Busca un articulo wiki. Sintaxis: wikisearch <Termino de busqueda/>");
		try {
			$k = ORM::for_table('wikichan')->find_one();
		}catch(PDOException $e){
			$query="CREATE TABLE 'wikichan' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'chan' TEXT NOT NULL, 'api' TEXT NOT NULL);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}
	
	public function chwadd(&$irc, &$data, &$core){
		$s = ORM::for_table('wikichan')->create();
		$s->chan = strtolower($data->messageex[1]);
		$s->api = $data->messageex[2];
		$s->save();
	}
	public function chwrem(&$irc, &$data, &$core){
		$userpriv = ORM::for_table('wikichan')->where('chan', $data->messageex[2])->find_one();
		if(method_exists($userpriv, "delete")){$userpriv->delete();}
	}
	
	public function info(&$irc, &$data, &$core){
		$qad = ORM::for_table('wikichan')->where('chan', strtolower($data->channel))->find_one();
		if(!isset($qad->api)){ $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Esta funcion no esta habilitada en este canal"); return 0;}
		$ts = $core->jparam($data->messageex,1);
        $jsn = file_get_contents("http://".$qad->api."/api.php?action=query&list=users&ususers=".urlencode(str_replace(" ", "_",$ts))."&format=json&usprop=blockinfo|editcount|registration|gender|groups");		
		if($jsn==""){$irc->message(SMARTIRC_TYPE_CHANNEL,$channel,"Error interno \002001\002. Notifique al administrador.");return 0;}
		$result=json_decode($jsn);
				
		if(@!isset($result->query->users[0]->missing)){
			if($result->query->users[0]->registration){
				$registration=date("d/m/Y H:i",strtotime(substr($result->query->users[0]->registration,0,strlen($result->query->users[0]->registration)-1)));
			}else{$registration="04N/D";}
		$resp="Usuario ".$result->query->users[0]->name." - Registrado: ". $registration. " - Ediciones: ".$result->query->users[0]->editcount ."";

		$dcontrib=json_decode(file_get_contents("http://".$this->chans[$channel]."/api.php?action=help&modules=userdailycontribs&format=json"));		
		if(!isset($dcontrib->help[0]->missing)){
			$w1 = file_get_contents("http://".$qad->api."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=1&format=json"); $r1=json_decode($w1);
			$w2 = file_get_contents("http://".$qad->api."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=7&format=json"); $r2=json_decode($w2);
			$w3 = file_get_contents("http://".$qad->api."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=30&format=json"); $r3=json_decode($w3);
			$w4 = file_get_contents("http://".$qad->api."/api.php?action=userdailycontribs&user=".urlencode(str_replace(" ", "_",$ts))."&daysago=180&format=json"); $r4=json_decode($w4);
			$resp.=" (Dia: ".$r1->userdailycontribs->timeFrameEdits.", Semana: ".$r2->userdailycontribs->timeFrameEdits.", Mes: ".$r3->userdailycontribs->timeFrameEdits.", ~6 meses:".$r4->userdailycontribs->timeFrameEdits.")";
		}
			$i=0;
			$resp.=" - Grupos: ";
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
		$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$resp);
	}
	
	public function wikisearch(&$irc, &$data, &$core){
		$qad = ORM::for_table('wikichan')->where('chan', strtolower($data->channel))->find_one();
		if(!isset($qad->api)){ $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Esta funcion no esta habilitada en este canal"); return 0;}
		$ts = $core->jparam($data->messageex,1);
		
		$xml = file_get_contents("http://".$qad->api."/api.php?action=query&list=search&srsearch=".urlencode(str_replace(" ", "_",$ts))."&format=json&text=title&srlimit=5&srprop=snippet");		
		$result = json_decode($xml);
		$i=0;$r="";
		while(@isset($result->query->search[$i]->title)){
			$r.="\002\037".$result->query->search[$i]->title. "\037\002 \00310http://".$qad->api."/?title=".urlencode(str_replace(" ", "_",$result->query->search[$i]->title))."\003 - ";$i++;
		}
		$r=substr($r,0,strlen($r)-3);
		$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,"Resultados: ".$r);
	}
	
	public function preview(&$irc, &$data, &$core){
		$qad = ORM::for_table('wikichan')->where('chan', strtolower($data->channel))->find_one();
		if(!isset($qad->api)){ $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Esta funcion no esta habilitada en este canal"); return 0;}
		$ts = $core->jparam($data->messageex,1);
		if(isset($data->esg)){$ts = $data->esg;}
		 
		$xml =  file_get_contents("http://".$qad->api."/api.php?action=query&prop=extracts&exchars=440&titles=".urlencode(str_replace(" ", "_",$ts))."&format=json");		
		$result = json_decode($xml);
		foreach($result->query->pages as $key => $val){$pageid=$key;}
		if(@isset($result->query->pages->$pageid->missing)){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, '04Error: El artículo no existe.');return 0;}
		$rs=$result->query->pages->$pageid->extract;
		if(preg_match('#^.*REDIRECCIÓN (.+)$#i',strip_tags($rs), $matches)){
			$data->esg = $matches[1];
			$this->preview($irc,$data,$core);
			return 0;
		}
		if(preg_match('#^.*REDIRECT (.+)$#i',strip_tags($rs), $matches)){
			$data->esg = $matches[1];
			$this->preview($irc,$data,$core);
			return 0;
		}
		$rs=$this->wformat($rs);
		
		$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$rs);

		$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,'10http://'.$qad->api.'/index.php?title='.urlencode(str_replace(" ", "_",$ts)));

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
}
