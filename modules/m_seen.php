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
		try {
			$k = ORM::for_table('seen')->find_one();
		}catch(PDOException $e){
			$query="CREATE TABLE 'seen' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'ts' INTEGER NOT NULL, 'txt' TEXT NOT NULL);";
			$db = ORM::get_db();
			$db->exec($query);
		}
	}
	
	public function seen(&$irc, $data, $core){
		$r="";
		$n = ORM::for_table('seen')->where('nick',strtolower($data->messageex[1]))->find_one();
		if($n){
			
			$this->diff($n->ts);
			if(($this->diff['ye']==0) && ($this->diff['mo']==0) && ($this->diff['da']==0) && ($this->diff['ho']==0) && ($this->diff['mi']==0) && ($this->diff['se']==0)){
				$r="¡¡Pero si me acabás de hablar!!";
			}else{
				$r.="Escuche hablar a \002{$n->nick}\002 hace ";$r2="";
				if($this->diff['ye']!=0){$r2.="\002{$this->diff['ye']}\002 años, ";}
				if($this->diff['mo']!=0){$r2.="\002{$this->diff['mo']}\002 meses, ";}
				if($this->diff['da']!=0){$r2.="\002{$this->diff['da']}\002 días, ";}
				if($this->diff['ho']!=0){$r2.="\002{$this->diff['ho']}\002 horas, ";}
				if($this->diff['mi']!=0){$r2.="\002{$this->diff['mi']}\002 minutos, ";}
				$r2.="y \002{$this->diff['se']}\002 segundos";
				$r2 = trim($r2, " ,y");
				$r.=$r2.": ".$n->txt;
			}
		}else{
			$r.="No he visto a \002{$data->messageex[1]}\002...";
		}
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}
	
	function diff($start,$end = false) { 
		if(!$end) { $end = time(); } 
		if(!is_numeric($start) || !is_numeric($end)) { return false; } 

		$start  = date('Y-m-d H:i:s',$start); 
		$end    = date('Y-m-d H:i:s',$end); 
		$d_start    = new DateTime($start); 
		$d_end      = new DateTime($end); 
		$diff = $d_start->diff($d_end); 

		$this->diff['ye']    = $diff->format('%y'); 
		$this->diff['mo']    = $diff->format('%m'); 
		$this->diff['da']      = $diff->format('%d'); 
		$this->diff['ho']     = $diff->format('%h'); 
		$this->diff['mi']      = $diff->format('%i'); 
		$this->diff['se']      = $diff->format('%s'); 
		return true; 
	} 
	
	public function seenator(&$irc, $data, $core){
		try{	
			//print_r($data);
			$n = ORM::for_table('seen')->where('nick',strtolower($data->nick))->find_one();
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
		}catch(PDOException $e){echo "ATENCION ATENCION ATENCION: Algo raro ha pasao con la base de datos! {$e}\n";}
	}
	
}
