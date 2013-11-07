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
		
		try {
			$k = ORM::for_table('games_users')->find_one();
			$k = ORM::for_table('games_banco')->find_one();
			$k = ORM::for_table('games_cgames')->find_one();
		}catch(PDOException $e){
			$db = ORM::get_db();
			$db->exec("CREATE TABLE 'games_users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'dinero' INTEGER NOT NULL, 'nivel' INTEGER NOT NULL, 'congelado' INTEGER NOT NULL, 'extrainf' TEXT NOT NULL);");
			$db->exec("CREATE TABLE 'games_banco' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'dinero' INTEGER NOT NULL, 'extrainf' TEXT NOT NULL);");
			$db->exec("CREATE TABLE 'games_cgames' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'extrainf' TEXT NOT NULL);");
		}
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
		}
  }
  
  public function alta($irc,$data){
	  
	  $guser = ORM::for_table('games_users')->create();
	  $guser->nick=$data->nick;
	  $guser->dinero=$this->startrial;
	  $guser->congelado=0;
	  $guser->extrainf=json_encode(array());
	  $guser->nivel=0; $guser->save();
	  $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\002Te has dado de alta!!\002 ahora tienes \002\${$this->startrial}\002 para empezar a jugar!");
  }
  
  public function dados($irc,$data){
	  $d1 = rand(1,6);  $d2 = rand(1,6);  $d3 = rand(1,6);
	  $d = $d1+$d2+$d3;
	  
	  if ($d%2==0){// TODO: si es nivel 1, bla bla bla
		  $w=rand(2, 150);
	  }else{
		  $w=rand(2, 350);
	  }
	  $r = "\002{$data->nick}\002:\017 [\002$d1+$d2+$d3=$d\002] ".(($d%2==0)?"ganaste":"perdiste")." $$w!!!";
	  // TODO: lo de la base de datos...
	  
	  
	  $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
  }
}
