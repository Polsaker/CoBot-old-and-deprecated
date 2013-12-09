<?php
/*
 * @name: SkyNet
 * @desc: Te vigila
 * @ver: 1.0
 * @author: MRX
 * @id: skynet
 * @key: key
 *
 */

class key{
	private $paises;
	private $paises2;
	public function __construct(&$core){
		if(!$core->isLoaded("country")){$core->loadModule("m_countries.php"); } // Usaremos a m_countries!
		$this->paises = array_flip($core->getModule("country")->paises);
		$this->paises2 = $core->getModule("country")->paises;
		$core->registerMessageHandler('JOIN', "skynet", "hostspy");
		//$core->registerTimeHandler(1800000, "skynet", "spyall"); // Cada 30 minutos los espiamos a todos!
		$core->irc->setChannelSyncing(true);
		$core->registerCommand("espiar", "skynet", false, 1);
		$core->registerCommand("skystats", "skynet", false, 1);
		$core->registerCommand("skytop", "skynet", "Muestra un ranking de los usuarios por país. Sintaxis: skytop [cantidad] [pais]",1);
		$core->registerCommand("skyuser", "skynet", "Muestra la información de un usuario o borra la entrada de ese usuario. Sintaxis: skyuser <nick> [del]", 3);
		
		try {
			$k = ORM::for_table('spy')->find_one();
		}catch(PDOException $e){
			$query="CREATE TABLE 'spy' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'ip' TEXT NOT NULL, 'pais' TEXT NOT NULL, 'region' TEXT NOT NULL, 'ciudad' TEXT NOT NULL, 'timezone' TEXT NOT NULL);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}
	public function skystats(&$irc, $data, $core){
		$t = ORM::for_table('spy')->find_many();
		$foo=0;$pais=array();
		foreach($t as $u){$foo++; @$pais[$u->pais]++;}
		$ps = count($pais);
		$r="Tengo en total, registro de {$foo} usuarios, de {$ps} países distintos.";
		
		$core->message($data->channel, $r);
	}
	
	public function skyuser($irc, $data, $core){
		if(!isset($data->messageex[1])){return 0;}
		$u = ORM::for_table('spy')->where('nick', strtolower($data->messageex[1]))->find_one();
		if($u){
			if((isset($data->messageex[2])) && ($data->messageex[2] == "del")){
				$u->delete();
				$core->message($data->channel, "Se ha borrado la entrada de informacion del usuario \2{$data->messageex[1]}\2");
			}else{
				$core->message($data->channel, "P: {$u->pais}, R: {$u->region}, C: {$u->ciudad}, TS: {$u->timezone}, LIP: {$u->ip}");
			}
		}else{
			$core->message($data->channel, "\00304Error\003: Usuario no encontrado.");
		}
	}
	public function skytop($irc, $data, $core){
		$lpais = false;
		if((isset($data->messageex[1])) && (is_numeric($data->messageex[1]))){ $limit = $data->messageex[1];}else{$limit = 10;}
		if((isset($data->messageex[1])) && (!is_numeric($data->messageex[1]))){ $lpais = $data->messageex[1];}
		if((isset($data->messageex[2])) && (!is_numeric($data->messageex[2]))){ $lpais = $data->messageex[2];}
		
		if($lpais == false){
			$t = ORM::for_table('spy')->find_many();
			$pais=array();
			foreach($t as $u){
				$pais[($this->paises[$u->pais]?$this->paises[$u->pais]:"Desconocido")]++;
			}
			arsort($pais);
			$i=0;
			$core->message($data->channel, "\00306    PAÍS                     CANTIDAD");
			foreach($pais as $p => $n){
				$i++;
				$bs1=substr("                       ",0,(25-strlen(utf8_decode($p))));
				$r="\002".$i.(($i>=10)?". ":".  ")."\002".$p .$bs1.$n;
				$core->message($data->channel,$r);
				if($i == $limit){break;}
			}
		}else{
			$t = ORM::for_table('spy')->where('pais', strtoupper($lpais))->find_many();
			$pais=array();
			foreach($t as $u){
				
				$pais[($u->ciudad?$u->ciudad:"Desconocido")]++;
			}
			arsort($pais);
			$core->message($data->channel, "\00306    CIUDAD                   CANTIDAD");
			foreach($pais as $p => $n){
				$i++;
				$bs1=substr("                       ",0,(25-strlen($p)));
				$r="\002".$i.(($i>=10)?". ":".  ")."\002".$p .$bs1.$n;
				$core->message($data->channel,$r);
				if($i == $limit){break;}
			}
		}
	}
	
	public function espiar(&$irc, $data, $core){
		$this->spyall($irc, $data->channel);
	}
	public function hostspy(&$irc, $data, $core){
		
		if($data->nick != $irc->_nick){
			//print_r($data);
			// TODO: geolocalizar $data->host
			$this->analizar($data->host, $data->nick);
		}
	}
	
	public function spyall($irc, $ch){
		$ww=0;$ww2=0;
		$mch = count($irc->channel);
		foreach($irc->channel as $chan){
			$mus= count($chan->users);
			foreach($chan->users as $user){
				$ww2++;
				$u = ORM::for_table('spy')->where('nick', strtolower($user->nick))->find_one();
				if(!$u){
					$ww++;
					if($ww > 25){
						$irc->message(SMARTIRC_TYPE_CHANNEL, $ch, "Se han procesado {$ww2}/{$mus} en el canal actual. $mch canales para procesar.", SMARTIRC_CRITICAL);
						$ww=0;
					}
					$this->analizar($user->host, $user->nick);
				}
			}
			$ww2=0;
		}
	}
	
	private function analizar($host, $nick){
		echo "DEBUG: Analizando {$nick} ({$host})\n";
		
		/* Validación */
		$paso = false;
		if(@inet_pton($host)){ $paso =true;}
		if($this->is_valid_domain_name($host)){ $paso =true;}
		if($paso == false){return 0;}
		/* </validación> */
		$u = ORM::for_table('spy')->where('nick', strtolower($nick))->find_one();
		if($u){return 0;}
		$ip = file_get_contents("http://ip-api.com/json/{$host}");
		$jao = json_decode($ip);
		if($jao->status=="success"){
		try{
			$n = ORM::for_table('spy')->create();
			$n->nick	= strtolower($nick);
			$n->ip		= $jao->query;
			$n->pais	= (!$jao->countryCode?"UNK":$jao->countryCode);
			$n->region	= $jao->regionName;
			$n->ciudad	= $jao->city;
			$n->timezone= $jao->timezone;
			$n->save();
		}catch(PDOException $e){echo $e;}
		}
	}
	
	function is_valid_domain_name($domain_name)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
}
	
	
}
