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
		$core->registerCommand("impuesto", "games", "Cobra impuestos.",5, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("enablegame", "games", "Activa los juegos en un cana. Sintaxis: enablegame <canal>",4, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("disablegame", "games", "Desactiva los juegos en un canal. Sintaxis: disablegame <canal>",4, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("delgameuser", "games", "Elimina a un usurio de los juegos. Sintaxis: delgameuser <usuario>",5, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		
		$core->registerTimeHandler(86400000, "games", "autoimp");
		try {
			$k = ORM::for_table('games_users')->find_one();
			$k = ORM::for_table('games_banco')->find_one();
			$k = ORM::for_table('games_cgames')->find_one();
			$k = ORM::for_table('games_channels')->find_one();
		}catch(PDOException $e){
			try{
				$db = ORM::get_db();
				$db->exec("CREATE TABLE 'games_channels' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'channel' TEXT NOT NULL, 'extrainf' TEXT NOT NULL);");
				$db->exec("CREATE TABLE 'games_users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT NOT NULL, 'dinero' INTEGER NOT NULL, 'nivel' INTEGER NOT NULL, 'congelado' INTEGER NOT NULL, 'extrainf' TEXT NOT NULL);");
				$db->exec("CREATE TABLE 'games_banco' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'dinero' INTEGER NOT NULL, 'extrainf' TEXT NOT NULL);");
				$db->exec("CREATE TABLE 'games_cgames' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'extrainf' TEXT NOT NULL);");
				$k = ORM::for_table('games_banco')->where('id',1)->find_one();
				if(!$k){
					$banco = ORM::for_table('games_banco')->create();
					$banco->dinero=1000000;$banco->extrainf=json_encode(array('pozo'=>'0'));$banco->save();
				}
			}catch(PDOException $e){/* meh */}
		}
	}
	
	public function autoimp(&$irc){
		$r = $this->cimpuesto(5);
	}
		
	public function delgameuser(&$irc, $data, &$core){
		$c = ORM::for_table('games_users')->where("user", $data->messageex[1])->find_one();
		if($c){
			$c->delete();
			$this->schan($irc,$data->channel, "Se ha eliminado el usuario.");
		}else{
			$this->schan($irc,$data->channel, "Ese usuario no existe!.",true);
		}
	}
	
	public function enablegame(&$irc, $data, &$core){
		$c = ORM::for_table('games_channels')->where("channel", strtolower($data->messageex[1]))->find_one();
		if(!$c){
			$c = ORM::for_table('games_channels')->create();
			$c->channel=strtolower($data->messageex[1]);
			$c->extrainf="[]";
			$c->save();
			$this->schan($irc,$data->channel, "Se han habilitado los juegos en \2{$data->messageex[1]}\2");
		}else{
			$this->schan($irc,$data->channel, "Los juegos ya están habilitados en ese canal.",true);
		}
	}
	
	public function disablegame(&$irc, $data, &$core){
		$c = ORM::for_table('games_channels')->where("channel", strtolower($data->messageex[1]))->find_one();
		if($c){
			$c->delete();
			$this->schan($irc,$data->channel, "Se han deshabilitado los juegos en \2{$data->messageex[1]}\2");
		}else{
			$this->schan($irc,$data->channel, "Los juegos no estaban habilitados en ese canal.",true);
		}
	}
	
	public function impuesto(&$irc, $data, &$core){
		$r = $this->cimpuesto();
		$this->schan($irc,$data->channel, "Se han cobrado \${$r['dinero']} de impuestos a {$r['users']} usuarios");
	}
	public function changemoney(&$irc, $data, &$core){
		if($data->messageex[1]=="banco"){
			$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
			$b->dinero=$data->messageex[2];$b->save();return 0;
		}
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if($k){	$k->dinero=$data->messageex[2]; $k->save(); }
	}
	
  public function gamecommandhandler(&$irc, $data, &$core){
		if($data->messageex[0][0]!="!"){return 0;}
		$chan=ORM::for_table("games_channels")->where("channel", $data->channel)->find_one();
		if(!$chan){return 0;}
		// TODO 1: Verificar si el nick esta registrado y si puso un comando de juegos en un canal con juegos habilitados..
		$bu = ORM::for_table('users')->where("username", strtolower($data->nick))->find_one();
		if($bu){
			if(!$core->authchk($data->from, 0, "*")){
				$this->schan($irc,$data->channel, "Estas usando un nick registrado. Por favor identifiquese o utilice otro nick.", true);
				return 0;
			}
		}
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
			case "!lvlup":
			case "!nivel": $this->nivel($irc,$data);break;
			case "!tragamonedas": 
			case "!tragaperras": $this->tragaperras($irc,$data);break;
			case "!rueda": $this->rueda($irc,$data);break;
			case "!transferir": $this->transferir($irc,$data);break;
		}
  }
  
	public function alta($irc,$data){
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
		if($b->dinero<1000){$this->schan($irc,$data->channel, "No puedes jugar. El banco está en quiebra.", true);return 0;}
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if(isset($k->nick)){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00304Error\003: Ya estás dado de alta");return 0;}
		$guser = ORM::for_table('games_users')->create();
		$guser->nick=$data->nick;
		$guser->dinero=$this->startrial;
		$guser->congelado=0;
		$guser->extrainf=json_encode(array());
		$guser->nivel=0; $guser->save();
		$b->dinero=$b->dinero-$this->startrial; $b->save();
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\002Te has dado de alta!!\002 ahora tienes \002\${$this->startrial}\002 para empezar a jugar!");
	}
  
	public function dinero($irc,$data){
		if(!isset($data->messageex[1])){$user = $data->nick;}else{$user=$data->messageex[1];}
		$k = ORM::for_table('games_users')->where("nick", $user)->find_one();
		if($k){
			$bu = ORM::for_table('users')->where("username", strtolower($data->nick))->find_one();
			$r="\017En la cuenta de \002$user\002 hay $\002{$k->dinero}\002. Flags: [\002Lvl\002 {$k->nivel}] ";
			if($k->dinero>1000000){$r.="[\002\00303M\003\002] ";}
			if($k->dinero>3000000){$r.="[\002\00304M\003\002] ";}
			if($k->dinero>1000000000){$r.="[\002\00304MM\003\002] ";}
			if($bu){$r.="[\2\00307R\003\2] ";}
		}else{
			$r="\00304Error\003: El usuario \002$user\002 no existe.";
		}
		if($user=="banco"){
			$k = ORM::for_table('games_banco')->where("id", 1)->find_one();
			$r="En el banco hay $\002{$k->dinero}\002. Flags: [\002\00302B\003\002] ";
			if($k->dinero<1000){$r.="[\2\00305Q\003\2] ";}
			if($p = json_decode($k->extrainf)->pozo){$r.="[\2Pozo\2 $p]";}
		}
		$this->schan($irc,$data->channel,$r);
	}
	
	public function top($irc,$data, $n){
		$k = ORM::for_table('games_users')->order_by_desc("dinero")->find_many();
		$i=0;
		$this->schan($irc,$data->channel, "\00306    NICK                NIVEL  DINERO");
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
		//$this->schan($irc,$data->channel, "{$data->nick}: Nivel {$k->nivel}");
		$basecost=125;$i=0;
		while($i<($k->nivel+1)){
			$i++;
			$basecost=$basecost*2;
		}
		$basecost=$basecost;
		if($k->dinero<($basecost+50)){$this->schan($irc,$data->channel,"Necesitas ".($basecost+50)." para pasar al nivel ".($k->nivel+1),true);return 0;}
		$k->nivel=$k->nivel+1;
		$k->dinero=$k->dinero-$basecost;$k->save();
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
		$b->dinero=$b->dinero+$basecost;$b->save();
		$this->schan($irc,$data->channel, "Ahora eres nivel {$k->nivel}!");
	}
	
	public function transferir($irc,$data){
		if(!isset($data->messageex[2])){return 0;} // Por que enviar un mensaje de error es mucho trabajo.
		$data->messageex[2]=abs($data->messageex[2]);
		$f = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		$t = ORM::for_table('games_users')->where("nick", $data->messageex[1])->find_one();
		if(!$t){$this->schan($irc,$data->channel, "El usuario {$data->messageex[1]} no existe.", true); return 0; }
		if($f->nivel<6){$this->schan($irc,$data->channel, "Debes ser por lo menos nivel 6 para enviar dinero a otros usuarios.", true);return 0;}
		if($f->dinero<200){$this->schan($irc,$data->channel, "Debes tener por lo menos $200 para enviar dinero a alguien.", true); return 0;}
		if(($f->dinero-$data->messageex[2])<100){$this->schan($irc,$data->channel, "Siempre debes conservar por lo menos $100",true); return 0;}
		$t->dinero = $t->dinero + $data->messageex[2]; $t->save();
		$f->dinero = $f->dinero - ($data->messageex[2]+50);$f->save();
		$ba = ORM::for_table('games_banco')->where("id", 1)->find_one();
		$ba->dinero = $ba->dinero +50; $ba->save();
		$this->schan($irc, $data->channel, "Se han transferido \${$data->messageex[2]} a {$t->nick}.");
	}
	public function rueda($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		$ba = ORM::for_table('games_banco')->where("id", 1)->find_one();
		if($k->dinero<500){$this->schan($irc,$data->channel, "No tienes suficiente dinero como para jugar a este juego. Necesitas $500.", true); return 0;}
		
		if($k->nivel<4){$this->schan($irc,$data->channel, "Debes ser por lo menos nivel 4 para poder jugar a este juego.", true); return 0;}
		
		$po=json_decode($ba->extrainf);
		$resu = rand(0,5);
		switch($resu){
			case 0:
				$final = $k->dinero + round(($po->pozo * 50/100),0);
				$finalp = round(($po->pozo * 50/100),0);$finalb=$ba->dinero;
				$r="\2{$data->nick}\2:\17 \00304GANASTE\00311 EL 50% DEL DINERO DEL POZO!!!\003 Ahora tienes\00303\2 $$final";
				break;
			case 1:
				$final = $k->dinero + round(($po->pozo * 25/100),0);
				$finalp = round(($po->pozo * 75/100),0);$finalb=$ba->dinero;
				$r="\2{$data->nick}\2:\17 \00304GANASTE\00311 EL 25% DEL DINERO DEL POZO!!!\003 Ahora tienes\00303\2 $$final";
				break;
			case 2:
				$final = $k->dinero; $finalp=$po->pozo;$finalb=$ba->dinero;
				$r="\2{$data->nick}\2:\17 No pierdes ni ganas nada de dinero.";
				break;
			case 3:
				$final = round(($k->dinero * 50/100),0);$finalp=$po->pozo + round(($k->dinero * 25/100),0);
				$finalb= $ba->dinero + round(($k->dinero * 25/100),0);
				$r="\2{$data->nick}\2:\17 \00304PERDISTE\00311 EL 50% DE TU DINERO!!!\003 Ahora tienes\00303\2 $$final";
				break;
			case 4:
				$final = round(($k->dinero * 25/100),0);$finalp=$po->pozo+ round(($k->dinero * 25/100),0);
				$finalb= $ba->dinero + round(($k->dinero * 50/100),0);
				$r="\2{$data->nick}\2:\17 \00304PERDISTE\00311 EL 75% DE TU DINERO!!!\003 Ahora tienes\00303\2 $$final";
				break;
			case 5:
				$final = 200;$finalp=$po->pozo+ round(($k->dinero -200) * 50/100,0);
				$finalb= $ba->dinero + round(($k->dinero -200) * 50/100,0);
				$r="\2{$data->nick}\2:\17 \00304PERDISTE\00311 TODO TU DINERO!!!\003 Tienes $200 para amortizar la perdida.";
				break;
		}
		$ba->dinero = $finalb;
		$po->pozo=$finalp;
		$ba->extrainf=json_encode($po);$ba->save();
		$k->dinero=$final;$k->save();
		$this->schan($irc, $data->channel, $r);
	}
	public function tragaperras($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		$ba = ORM::for_table('games_banco')->where("id", 1)->find_one();
		if($ba->dinero<1000){$this->schan($irc,$data->channel, "No puedes jugar. El banco está en quiebra.", true); return 0;}
		if($k->dinero<10){$this->schan($irc,$data->channel, "No tienes suficiente dinero como para jugar a este juego. Necesitas $10.", true); return 0;}
		if($k->nivel==0){$this->schan($irc,$data->channel, "Debes ser por lo menos nivel 1 para poder jugar a este juego.", true); return 0;}
		//$k->dinero=$k->dinero-10; $k->save();
		$po=json_decode($ba->extrainf);
		
		switch($k->nivel){
			case 1:	$s=rand(5,10);	$p=rand(2,14);	$n=rand(-6,10);	$m=rand(9,29);	$e=rand(-10,1);	$b=rand(-19,-2); $x=rand(-10,10); $a=rand(-5,10);break;
			default:$s=rand(6,12);	$p=rand(5,16);	$n=rand(-9,15);	$m=rand(12,30);	$e=rand(-17,3);	$b=rand(-26,-8); $x=rand(-17,17); $a=rand(-8,15);break;
		}
		$n1=rand(1,8);	$n2=rand(1,8);	$n3=rand(1,8);
		// OPTIMIZAR!!!
		$comb="";
		switch($n1){case 1:$r1=$s;$comb.="[\002\00303$\003\002]";break; case 2:$r1=$p;$comb.="[\002\00302%\003\002]";break;	case 3:$r1=$n;$comb.="[\002\00307#\003\002]";break; case 4:$r1=$m;$comb.="[\002\00309+\003\002]";break;	case 5:$r1=$e;$comb.="[\002\00315-\003\002]";break; case 6:$r1=$b;$comb.="[\002\00311/\003\002]";break;	case 7:$r1=$x;$comb.="[\002\00313X\003\002]";break; case 8:$r1=$a;$comb.="[\002\00312&\003\002]";break;}
		switch($n2){case 1:$r2=$s;$comb.="[\002\00303$\003\002]";break; case 2:$r2=$p;$comb.="[\002\00302%\003\002]";break;	case 3:$r2=$n;$comb.="[\002\00307#\003\002]";break; case 4:$r2=$m;$comb.="[\002\00309+\003\002]";break; case 5:$r2=$e;$comb.="[\002\00315-\003\002]";break; case 6:$r2=$b;$comb.="[\002\00311/\003\002]";break; case 7:$r2=$x;$comb.="[\002\00313X\003\002]";break; case 8:$r2=$a;$comb.="[\002\00312&\003\002]";break;}
		switch($n3){case 1:$r3=$s;$comb.="[\002\00303$\003\002]";break; case 2:$r3=$p;$comb.="[\002\00302%\003\002]";break; case 3:$r3=$n;$comb.="[\002\00307#\003\002]";break; case 4:$r3=$m;$comb.="[\002\00309+\003\002]";break;	case 5:$r3=$e;$comb.="[\002\00315-\003\002]";break; case 6:$r3=$b;$comb.="[\002\00311/\003\002]";break; case 7:$r3=$x;$comb.="[\002\00313X\003\002]";break; case 8:$r3=$a;$comb.="[\002\00312&\003\002]";break;}
		$tot=$r1+$r2+$r3;
		if($n1==$n2 && $n3==$n2){$tot=200;}
		$k->dinero=$k->dinero + $tot;$k->save();
		if($tot<0){
			$po->pozo=$po->pozo+abs-($tot);
			$ba->extrainf=json_encode($po);
		}else{
			$ba->dinero=$ba->dinero - $tot;
		}$ba->save();
		$resp="\002{$data->nick}\002\017: $comb ".(($tot<0)?"\002PERDISTE\002 $".abs($tot):"\002GANASTE\002 $$tot");

		$this->schan($irc,$data->channel,$resp);
	}
	
	public function dados($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if($k->dinero<5){$this->schan($irc,$data->channel, "No tienes suficiente dinero como para jugar a este juego. Necesitas $5.", true); return 0;}
		$d1 = rand(1,6);  $d2 = rand(1,6);  $d3 = rand(1,6);
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
//		$k->dinero=$k->dinero-10;
		$po=json_decode($b->extrainf);
		
		if($b->dinero<1000){$this->schan($irc,$data->channel, "No puedes jugar. El banco está en quiebra.", true);return 0;}

		$d = $d1+$d2+$d3;

		if ($d%2==0){
			$w=rand(2, 30);
			$k->dinero=$k->dinero + $w;$k->save();
			$b->dinero=$b->dinero - $w;$b->save();
		}else{
			$w=rand(2, 15);
			$k->dinero=$k->dinero - $w;$k->save();
			//$b->dinero=$b->dinero + $w;
			$po->pozo=$po->pozo+$w;
			$b->extrainf=json_encode($po);$b->save();
		}
		$r = "\002{$data->nick}\002:\017 [\002$d1+$d2+$d3=$d\002] ".(($d%2==0)?"ganaste":"perdiste")." $$w!!!";

		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}
	
	
	
	/* Funciones internas */
	
	private function schan($irc, $chan, $txt, $err=false){
		$irc->message(SMARTIRC_TYPE_CHANNEL, $chan, ($err?"\00304Error\003: ":"").$txt);
	}
	
	
	// Cobra impuestos:
	public function cimpuesto($perc = 10){
		$s = ORM::for_table('games_users')->where_gt("dinero", 1000)->find_many();
		$totdi=0; $totu = 0;
		foreach($s as $user){
			$imp=round(($user->dinero * $perc/100),0);
			$user->dinero=$user->dinero - $imp;
			$user->save();
			$totu++; $toti = $toti+$imp;
		}
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
		$b->dinero = $b->dinero+$toti;$b->save();
		$chans = ORM::for_table('games_channels')->find_many();
		foreach($chans as $chan){
			$irc->message(SMARTIRC_TYPE_NOTICE, $chan->channel, "Se han cobrado \$\2$toti\2 de impuestos a $totu usuarios");
		}
		return array('users'=>$totu, 'dinero'=>$toti);
	}
}
