<?php
/*
 * @name: Autodeop
 * @ver: 1.0
 * @author: MRX
 * @id: autodeop
 * @key: imagenius
 *
 */

class imagenius{
        public function __construct(&$core){
			$core->registerCommand("autodeop", "autodeop", "Activa o desactiva el auto-deop en un canal. Sintaxis: autodeop <on/off> <canal>", 8, CUSTOMPRIV);
			$core->registerMessageHandler('MODE', "autodeop", "deop");
			try {
				$k = ORM::for_table('deopchans')->find_one();
			}catch(PDOException $e){
				$query="CREATE TABLE 'deopchans' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'channel' TEXT NOT NULL);";
				$db = ORM::get_db();
				$db->exec($query);
			}
        }
        
        public function autodeop_priv(&$irc, $data, $core){
			return $core->authchk($data->from, 8, $data->messageex[2]);
		}
		
        public function autodeop(&$irc, $data, $core){
            if($data->messageex[1]=="on"){
				$c = ORM::for_table('deopchans')->create();
				$c->channel = $data->messageex[2];
				$c->save();
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Se ha activado el auto-deop en \2{$data->messageex[2]}\2");
			}elseif($data->messageex[1]=="off"){
				$c = ORM::for_table('deopchans')->where('channel', $data->messageex[2])->find_one();
				if($c){
					$c->delete();
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Se ha desactivado el auto-deop en \2{$data->messageex[2]}\2");
				}else{
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\2{$data->messageex[2]}\2 no tiene el auto-deop activado.");
				}
			}
        }
        
        public function deop(&$irc, $data, $core){
			//print_r($data);
			if(ORM::for_table('deopchans')->where('channel',$data->rawmessageex[2])->find_one()){
				/* ex mask2nick */
				$m = trim($data->rawmessageex[0], ":");
				$n = explode("!", $m);
				$nick = $n[0];
				/* </mask2nick> */
				
				if(($nick != $irc->_nick) && ($data->rawmessageex[4] != $irc->_nick) && ($data->rawmessageex[3] == "+o")){
					$irc->deop($data->channel, $data->rawmessageex[4]);
				}
			}
		}
}
