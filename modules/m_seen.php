<?php
/*
 * @name: Seen
 * @ver: 1.0
 * @author: MRX
 * @id: seen
 * @key: imagenius
 *
 */

class imagenius{
	public function __construct(&$core){
        $core->registerMessageHandler('PRIVMSG', "seen", "seenator");
		$core->registerCommand("seen", "seen", "Muestra cuando fue la ultima vez que se vio a un usuario. Sintaxis: seen <nick>");
		if(!ORM::for_table('seen')->find_one()){
			$query="CREATE TABLE 'seen' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'ts' INTEGER NOT NULL, 'txt' TEXT NOT NULL);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}
	
	public function seen(&$irc, $data, $core){
		$r="";
		$n = ORM::for_table('seen')->where('nick',strtolower($data->messageex[1]))->find_one();
		if($n){
			echo "TAAAAAAAAAA";
		}else{
			echo "NO TA!";
		}
	}
	public function seenator(&$irc, $data, $core){
		print_r($data);return 0;
		$n = ORM::for_table('seen')->where('nick',$data->nick)->find_one();
		if(!$n){
			$s = ORM::for_table('seen')->create();
			$s->nick = strtolower($data->nick);
			$s->ts = time();
			$s->txt = $data->message;
			$s->save();
		}else{
			$n->ts = time();
			$n->txt = $data->message;
			$n->save();
		}
	}
	
}
