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
	public function __construct(&$core){
		$core->registerMessageHandler('JOIN', "skynet", "hostspy");
		$core->registerTimeHandler(1800000, "skynet", "spyall"); // Cada 30 minutos los espiamos a todos!
		$core->irc->setChannelSyncing(true);
		$core->registerCommand("espiar", "skynet");
		$core->registerCommand("spystats", "skynet");
		
		try {
			$k = ORM::for_table('skynet')->find_one();
		}catch(PDOException $e){
			$query="CREATE TABLE 'skynet' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'ip' TEXT NOT NULL, 'pais' TEXT NOT NULL, 'region' TEXT NOT NULL, 'ciudad' TEXT NOT NULL, 'timezone' TEXT NOT NULL);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}
	public function spystats(&$irc, $data, $core){
		$t = ORM::for_table('skynet')->find_many();
		$foo=0;$pais=array();
		foreach($t as $u){
			$foo++;
			$pais[$u->pais]++;
		}
		arsort($pais);
		$r="Tengo en total, registro de {$foo} usuarios, ";
		foreach($pais as $p => $n){
			$r.="{$n} de {$p}, ";
		}
		$core->message($data->channel, trim($r, ", "));
	}
	public function espiar(&$irc, $data, $core){
		$this->spyall($irc, $data->channel);
	}
	public function hostspy(&$irc, $data, $core){
		
		if($data->nick != $irc->_nick){
			print_r($data);
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
				$u = ORM::for_table('skynet')->where('nick', $user->nick)->find_one();
				if(!$u){
					$ww++;
					if($ww > 25){
						$irc->message(SMARTIRC_TYPE_CHANNEL, $ch, "Se han procesado {$ww2}/{$mus} en el canal actual. $mch canales para procesar.", SMARTIRC_CRITICAL);
						$ww=0;
					}
					$this->analizar($user->host, $user->nick);
				}
			}
		}
	}
	
	private function analizar($host, $nick){
		$u = ORM::for_table('skynet')->where('nick', $user->nick)->find_one();
		if($u){return 0;}
		$ip = file_get_contents("http://ip-api.com/json/{$host}");
		$jao = json_decode($ip);
		if($jao->status=="success"){
		try{
			$n = ORM::for_table('skynet')->create();
			$n->nick	= $nick;
			$n->ip		= $jao->query;
			$n->pais	= (!$jao->country?"Pais desconocido":$jao->country);
			$n->region	= $jao->regionName;
			$n->ciudad	= $jao->city;
			$n->timezone= $jao->timezone;
			$n->save();
		}catch(PDOException $e){echo $e;}
		}
	}
	
	
}
