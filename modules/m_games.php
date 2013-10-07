<?php
/*
 * @name: Juegos (core)
 * @desc: Núcleo de los juegos
 * @ver: 1.0
 * @author: MRX
 * @id: games
 * @key: jueg
 *
 */

class jueg{
	public $xcommands=array(); // Pensado para futuros comandos agregados por modulos de terceros... probablemente sea Array('fname', 'module', 'func', 'help')
	
	public $startrial=100;
	public function __construct(&$core){
		$core->registerMessageHandler('PRIVMSG', "games", "gamecommandhandler");
		$core->registerCommand("changemoney", "games", "Cambia el dinero almacenado en la cuenta de un usuario. Sintaxis: changemoney <nick> <dinero>",5, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);

		try {
			$k = ORM::for_table('games_users')->find_one();
			$k = ORM::for_table('games_banco')->find_one();
			$k = ORM::for_table('games_cgames')->find_one();
		}catch(PDOException $e){
			$db = ORM::get_db();
			$db->exec("CREATE TABLE 'games_users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'dinero' INTEGER NOT NULL, 'nivel' INTEGER NOT NULL, 'congelado' INTEGER NOT NULL, 'extrainf' TEXT NOT NULL);");
			$db->exec("CREATE TABLE 'games_banco' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'dinero' INTEGER NOT NULL, 'extrainf' TEXT NOT NULL);");
			$db->exec("CREATE TABLE 'games_cgames' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'extrainf' TEXT NOT NULL);");
			$banco = ORM::for_table('games_banco')->create();
			$banco->dinero=1000000;$banco->extrainf=json_encode(array());$banco->save();
		}
	}
	
	public function changemoney(&$irc, $data, &$core){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if($k){	$k->dinero=$data->messageex[2]; $k->save(); }
	}
	
  public function gamecommandhandler(&$irc, $data, &$core){
		if(!preg_match("#\!.*#", $data->message)){return 0;}
		// TODO 1: Verificar si el nick esta registrado y si puso un comando de juegos en un canal con juegos habilitados..
		
		if(($data->messageex[0]!="!alta")){
			$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
			 if(!isset($k->congelado)){
				 $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00304Error\003: No estas dado de alta en el juego. Date de alta escribiendo \002!alta\002");
				 return 0;
			 }
		}
		switch($data->messageex[0]){
			case "!alta": $this->alta($irc,$data);break;
			case "!dados": $this->dados($irc,$data);break;
			case "!dinero": $this->dinero($irc,$data);break;
			case "!top": $this->top($irc,$data,5);break;
			case "!top10": $this->top($irc,$data,10);break;
			case "!nivel": $this->nivel($irc,$data);break;
		}
  }
  
	public function alta($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if(isset($k->nick)){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00304Error\003: Ya estás dado de alta");return 0;}
		$guser = ORM::for_table('games_users')->create();
		$guser->nick=$data->nick;
		$guser->dinero=$this->startrial;
		$guser->congelado=0;
		$guser->extrainf=json_encode(array());
		$guser->nivel=0; $guser->save();
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\002Te has dado de alta!!\002 ahora tienes \002\${$this->startrial}\002 para empezar a jugar!");
	}
  
	public function dinero($irc,$data){
		if(!isset($data->messageex[1])){$user = $data->nick;}else{$user=$data->messageex[1];}
		$k = ORM::for_table('games_users')->where("nick", $user)->find_one();
		if($k){
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "En la cuenta de \002$user\002 hay \002\${$k->dinero}\002");
		}else{
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00304Error\003: El usuario \002$user\002 no existe.");
		}
	}
	
	public function top($irc,$data, $n){
		$k = ORM::for_table('games_users')->order_by_desc("dinero")->find_many();
		$i=0;
		$this->schan($irc,$data->channel, "\00308    NICK                NIVEL  DINERO");
		foreach($k as $key => $val){
			$i++;
			$bs1=substr("                  ",0,(20-strlen($val->nick)));
			$r="\002".$i.(($i>=10)?". ":".  ")."\002".$val->nick .$bs1.$val->nivel.(($val->nivel>=10)?"     ":"      ").$val->dinero;
			$this->schan($irc,$data->channel, $r);
			if($i==$n){break;}
		}
		
	}
	public function nivel($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		//if(!isset($data->messageex[1])){
			$this->schan($irc,$data->channel, "{$data->nick}: Nivel {$k->nivel}");
		//}else{
			//if($k->nivel>=$data->messageex[1]){$this->schan($irc,$data->channel,"Ya estás en un nivel igual o superior al {$data->messageex[1]}",true);return 0;}
			//if($data->messageex[1]<=0){$this->schan($irc,$data->channel, "Heh, nivel cero... CREES QUE SOY PELOTUDO O QUE?!", true);return 0;}
			$basecost=125;$i=0;
			while($i<($k->nivel+1)){
				$i++;
				$basecost=$basecost*2;
			}
			$k->nivel=$k->nivel+1;
			$k->dinero=$k->dinero-$basecost;$k->save();
			$this->schan($irc,$data->channel, "Ahora eres nivel {$k->nivel}!");
		//}
	}
	
	public function dados($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if($k->dinero<10){$this->schan($irc,$data->channel, "No tienes suficiente dinero como para jugar a este juego. Necesitas $10.", true); return 0;}
		$d1 = rand(1,6);  $d2 = rand(1,6);  $d3 = rand(1,6);
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
		$d = $d1+$d2+$d3;

		if ($d%2==0){// TODO: si es nivel 1, bla bla bla
			$w=rand(2, 30);
			$k->dinero=$k->dinero + $w;$k->save();
			$b->dinero=$b->dinero - $w;$b->save();
		}else{
			$w=rand(2, 15);
			$k->dinero=$k->dinero - $w;$k->save();
			$b->dinero=$b->dinero + $w;$b->save();
		}
		$r = "\002{$data->nick}\002:\017 [\002$d1+$d2+$d3=$d\002] ".(($d%2==0)?"ganaste":"perdiste")." $$w!!!";

		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}
	
	
	
	/* Funciones internas */
	
	private function schan($irc, $chan, $txt, $err=false){
		$irc->message(SMARTIRC_TYPE_CHANNEL, $chan, ($err?"\00304Error\003: ":"").$txt);
	}
}
