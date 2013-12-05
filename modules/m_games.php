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
	private $core;
	public $startrial=100;
	private $lastplayer=false;
	public function __construct(&$core){
		$this->core = $core;
		$core->registerMessageHandler('PRIVMSG', "games", "gamecommandhandler");
		$core->registerCommand("changemoney", "games", "Cambia el dinero almacenado en la cuenta de un usuario. Sintaxis: changemoney <nick> <dinero>",6, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("impuesto", "games", "Cobra impuestos.",6, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("enablegame", "games", "Activa los juegos en un cana. Sintaxis: enablegame <canal>",4, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("disablegame", "games", "Desactiva los juegos en un canal. Sintaxis: disablegame <canal>",4, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("delgameuser", "games", "Elimina a un usurio de los juegos. Sintaxis: delgameuser <usuario>",6, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("congelar", "games", "Congela a un usuario de los juegos. Sintaxis congelar <usuario> [hiper|light]",6, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("descongelar", "games", "Descongela a un usuario de los juegos. Sintaxis: descongelar <usuario>",6, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		$core->registerCommand("top100", "games", "Muestra los 100 usuarios con mas dinero",4, "games", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
		
		$core->irc->setSenddelay(500); // Nosotros hacemos mucho flood, pero queremos que el bot siga vivo!
		$core->registerTimeHandler(86400000, "games", "autoimp");
		$core->registerTimeHandler(1800000, "games", "sourprise");
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
	
	
	/***** <API> *****/
	public function registerGameCommand($command, $module){
		array_push($this->xcommands,array('command'=>$command, 'module'=>$module));
	}
	
	/***** </API> *****/
	public function sourprise(&$irc){
		if($this->lastplayer){
			$c = ORM::for_table('games_users')->where("nick", $this->lastplayer)->find_one();
			if(!$c){$this->lastplayer=false; return 0;}
			$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
			$l=rand(1,10); $m ="";
			switch($l){
				case "1":
					$pu=round(($c->dinero * 85/100),0);	$pb=$b->dinero + round(($c->dinero * 15/100),0);
					$m="\2{$this->lastplayer}\2 ha caido ebrio en el suelo. Alguien se aprovecha y le roba algo de dinero. Le quedan \2\${$pu}\2"; break;
				case "2":
					$pu=round(($c->dinero * 107/100),0);	$pb=$b->dinero - round(($pu * 7/100),0);
					$m="\2{$this->lastplayer}\2 ha encontrado una billetera en el suelo. Ahora tiene \2\${$pu}\2"; break;
				case "3":
					$pu=round(($c->dinero * 85/100),0);	$pb=$b->dinero + round(($c->dinero * 15/100),0);
					$m="A \2{$this->lastplayer}\2 le ha caido un rayo aun estando dentro del casino! Este increible suceso hace que parte de su dinero se queme. Le quedan \2\${$pu}\2"; break;
				case "4":
					$pu=round(($c->dinero * 85/100),0);	$pb=$b->dinero + round(($c->dinero * 15/100),0);
					$m="\2{$this->lastplayer}\2 es estafado con el tipico mail del principe nigeriano que necesita dinero para huir. Le quedan \2\${$pu}\2"; break;
				case "5":
					$pu=round(($c->dinero * 95/100),0);	$pb=$b->dinero + round(($c->dinero * 5/100),0);
					$m="A \2{$this->lastplayer}\2 se le cae algo de dinero por el retrete. Le quedan \2\${$pu}\2"; break;
				case "6":
					$pu=round(($c->dinero * 95/100),0);	$pb=$b->dinero + round(($c->dinero * 5/100),0);
					$m="\2{$this->lastplayer}\2 despierta y aparece en sudafrica. Le quedan \2\${$pu}\2"; break;
				case "7":
					$pu=round(($c->dinero * 105/100),0);	$pb=$b->dinero - round(($pu * 5/100),0);
					$m="\2{$this->lastplayer}\2 encuentra dinero adentro de una almohada. Ahora tiene \2\${$pu}\2"; break;
				case "8":
					$pu=round(($c->dinero * 95/100),0);	$pb=$b->dinero + round(($c->dinero * 5/100),0);
					$m="\2{$this->lastplayer}\2 despierta y aparece en sudafrica. Le quedan \2\${$pu}\2"; break;
				case "9":
					$pu=round(($c->dinero * 105/100),0);	$pb=$b->dinero - round(($pu * 5/100),0);
					$m="\2{$this->lastplayer}\2 le pego a alguien con un caño en la cabeza y se queda con su dinero. Ahora tiene \2\${$pu}\2"; break;
				case "10":
					$pu=round(($c->dinero * 95/100),0);	$pb=$b->dinero + round(($c->dinero * 5/100),0);
					$m="A \2{$this->lastplayer}\2 le han pegado con un caño de acero en la cabeza. Se roban parte de su dinero. Le quedan \2\${$pu}\2"; break;
			}
			
			$c->dinero =$pu; $c->save();
			$b->dinero =$pb; $b->save();
			$this->sendGlobalNotice($irc,$m);
			$this->lastplayer=false;
		}
		
		// Aprovechamos para aumentarle la deuda  a los que pidieron prestamos >:D
		
		$c = ORM::for_table('games_users')->where_not_equal("extrainf", "[]")->find_many();
		foreach ($c as $user){
			$i = json_decode($user->extrainf);
			if((isset($inf->prestamo)) && ($inf->prestamo != 0)){
				$inf->prestamo = $inf->prestamo + round(($inf->prestamo * 5/100),0);
				$user->extrainf = json_encode($inf); 
				$user->save();
			}
		}
	}
	
	public function autoimp(&$irc){
		$r = $this->cimpuesto($irc,5);
	}
		
	public function top100(&$irc, $data, &$core){
		$this->top($irc, $data, 100);
	}
	public function congelar(&$irc, $data, &$core){
		$c = ORM::for_table('games_users')->where("nick", $data->messageex[1])->find_one();
		if($c){
			switch(@$data->messageex[2]){
				case "hiper":$c->congelado = 2;break;
				case "light":$c->congelado = 3;break;
				default: $c->congelado = 1; break;
			}
			$c->save();
			$this->schan($irc,$data->channel, "Se ha congelado la cuenta.");
		}else{
			$this->schan($irc,$data->channel, "Ese usuario no existe!.",true);
		}
	}
	public function descongelar(&$irc, $data, &$core){
		$c = ORM::for_table('games_users')->where("nick", $data->messageex[1])->find_one();
		if($c){
			if($c->congelado==0){
				$this->schan($irc,$data->channel, "Ese usuario no está congelado!.",true);
			}else{
				$c->congelado=0;$c->save();
				$this->schan($irc,$data->channel, "Se ha descongelado el usuario.");
			}
		}else{
			$this->schan($irc,$data->channel, "Ese usuario no existe!.",true);
		}
	}
	
	public function delgameuser(&$irc, $data, &$core){
		$c = ORM::for_table('games_users')->where("nick", $data->messageex[1])->find_one();
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
		$r = $this->cimpuesto($irc);
		$this->schan($irc,$data->channel, "Se han cobrado \${$r['dinero']} de impuestos a {$r['users']} usuarios");
	}
	public function changemoney(&$irc, $data, &$core){
		if($data->messageex[1]=="banco"){
			$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
			$b->dinero=$data->messageex[2];$b->save();return 0;
		}
		$k = ORM::for_table('games_users')->where("nick", $data->messageex[1])->find_one();
		if($k){	$k->dinero=$data->messageex[2]; $k->save(); }
	}
	
  public function gamecommandhandler(&$irc, $data, &$core){
		if($data->messageex[0][0]!="!"){return 0;}
		$chan=ORM::for_table("games_channels")->where("channel", strtolower($data->channel))->find_one();
		if(!$chan){return 0;}
		// TODO 1: Verificar si el nick esta registrado y si puso un comando de juegos en un canal con juegos habilitados..
		$bu = ORM::for_table('users')->where("username", strtolower($data->nick))->find_one();
		if($bu){
			if(!$core->authchk($data->from, 0, "*")){
				$this->schan($irc,$data->channel, "Estas usando un nick registrado. Por favor identifiquese o utilice otro nick.", true);
				return 0;
			}
		}
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if(($data->messageex[0]!="!alta")){
			 if(!$k){
				 $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00304Error\003: No estas dado de alta en el juego. Date de alta escribiendo \002!alta\002");
				 return 0;
			 }
			 if($k->congelado!=0){
				 if($k->congelado==2){return 0;}
				 if($k->congelado==3){
					 $r=rand(1,3);
					 if($r==1){return 0;}
				 }else{
				 	 $this->schan($irc,$data->channel, "Esta cuenta esta congelada.", true);
				 	 return 0;
				 } 
			 }
		}
		$data->messageex[0] = strtolower($data->messageex[0]);
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
			case "!lvlp": $this->lvlp($irc,$data);break;
			case "!circulando": $this->circulando($irc,$data);break;
			case "!lvltop": $this->top($irc,$data,5, "nivel");break;
			case "!lvltop10": $this->top($irc,$data,10, "nivel");break;
			case "!prestamo": $this->prestamo($irc,$data);break;
		}
		
		foreach($this->xcommands as $com){
			if($data->messageex[0]=="!".$com['command']){
				$this->core->getModule($com['module'])->$com['command']($irc, $data);
			}
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
			$bu = ORM::for_table('users')->where("username", strtolower($user))->find_one();
			$r="\017En la cuenta de \002$user\002 hay $\002".number_format($k->dinero,0,",",".")."\002. Flags: [\002Lvl\002 {$k->nivel}] ";
			if($k->dinero>1000000){$r.="[\002\00303M\003\002] ";}
			if($k->dinero>3000000){$r.="[\002\00304M\003\002] ";}
			if($k->dinero>1000000000){$r.="[\002\00304MM\003\002] ";}
			if($bu){$r.="[\2\00307R\003\2] ";}
			if($k->congelado!=0){$r.="[\2\00305F\003\2] ";}
			$inf = json_decode($k->extrainf);
			if((isset($inf->prestamo)) && ($inf->prestamo != 0)){$r.="[\2Deuda\2 {$inf->prestamo}]";}
		/*	if($this->core->authchk($data->from, 4,"games")){$r.="[\2\00307A-\003\2] ";}
			if($this->core->authchk($data->from, 6,"games")){$r.="[\2\00310A\003\2] ";}
			if($this->core->authchk($data->from, 8,"games")){$r.="[\2\00311A+\003\2] ";}*/
		}else{
			$r="\00304Error\003: El usuario \002$user\002 no existe.";
		}
		if($user=="banco"){
			$k = ORM::for_table('games_banco')->where("id", 1)->find_one();
			$r="En el banco hay $\002".number_format($k->dinero,0,",",".")."\002. Flags: [\002\00302B\003\002] ";
			if($k->dinero<1000){$r.="[\2\00305Q\003\2] ";}
			if($p = json_decode($k->extrainf)->pozo){$r.="[\2Pozo\2 $p]";}
		}
		$this->schan($irc,$data->channel,$r);
	}
	
	public function prestamo($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		$base = 500;$i=0; $maximo = $base;
		while($i<($k->nivel)){
			$i++;
			$maximo = $maximo + round(($maximo * 25/100),0);
		}
		
		if($data->messageex[1] =="pagar"){
			$inf = json_decode($k->extrainf);
			if((!isset($inf->prestamo)) && ($inf->prestamo == 0)){$this->schan($irc, $data->channel, "No tienes ningun prestamo pendiente de pago.", true); return 0;}
			if($k->dinero < ($inf->prestamo +200)){$this->schan($irc, $data->channel, "No tienes dinero suficiente como para pagar este prestamo.", true); return 0;}
			$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
			$k->dinero = $k->dinero - $inf->prestamo;
			$b->dinero = $b->dinero + $inf->prestamo; $b->save();
			$inf->prestamo=0;
			$k->extrainf = json_encode($inf);
			$k->save();
			$this->schan($irc, $data->channel, "Has pagado tus deudas.");
		}elseif((!isset($data->messageex[1])) || (!is_numeric($data->messageex[1]))){
			$this->schan($irc, $data->channel, "Podes pedir hasta \$\2{$maximo}\2");
		}else{
			if($data->messageex[1] > $maximo){$this->schan($irc,$data->channel, "Solo podes pedir hasta \$\2{$maximo}\2!!", true); return 0;}
			$inf = json_decode($k->extrainf);
			if((isset($inf->prestamo)) && ($inf->prestamo != 0)){$this->schan($irc,$data->channel, "Debes pagar tu prestamo anterior antes de pedir otro!", true); return 0;}
			if(!isset($inf->prestamo)){ $inf['prestamo'] = 0;  $l = json_encode($inf); $inf = json_decode($l);}
			$inf->prestamo = $data->messageex[1] + 100;
			$k->dinero = $k->dinero + $data->messageex[1];
			$k->extrainf = json_encode($inf); $k->save();
			$this->schan($irc, $data->channel, "El banco le ha otorgado el prestamo. Su deuda se incrementará en un 5% cada media hora. Para pagar su prestamo escriba !prestamo pagar");
		}
	}
	
	public function top($irc,$data, $n, $order="dinero"){
		$k = ORM::for_table('games_users')->order_by_desc($order)->find_many();
		$i=0;
		$this->schan($irc,$data->channel, "\00306    NICK                NIVEL  DINERO");
		foreach($k as $key => $val){
			if($val->congelado==2){continue;}
			$i++;
			$bs1=substr("                  ",0,(20-strlen($val->nick)));
			$r="\002".$i.(($i>=10)?". ":".  ")."\002".$val->nick .$bs1.$val->nivel.(($val->nivel>=10)?"     ":"      ").number_format($val->dinero,0,",",".");
			$this->schan($irc,$data->channel,(($val->congelado==0)?"":"\00304"). $r);
			if($i==$n){break;}
		}
		
	}
	public function lvlp($irc,$data){
		if((isset($data->messageex[1])) && (is_numeric($data->messageex[1]))){
			$basecost=125;$i=0;
			while($i<($data->messageex[1])){
				$i++;
				$basecost=$basecost*2;
			}
			$basecost = number_format($basecost, 0, ",", ".");
			$this->schan($irc,$data->channel, "El nivel {$data->messageex[1]} cuesta \$$basecost");
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
		if($k->dinero<($basecost+50)){$this->schan($irc,$data->channel,"Necesitas ".number_format(($basecost+50), 0, ",", ".")." para pasar al nivel ".($k->nivel+1),true);return 0;}
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
		$i = json_decode($f->extrainf);
		if((isset($inf->prestamo)) && ($inf->prestamo != 0)){ $this->schan($irc, $data->channel, "No puedes transferir dinero si tienes una deuda con el banco! para pagarla pon !prestamo pagar", true); return 0; }
		$t = ORM::for_table('games_users')->where("nick", $data->messageex[1])->find_one();
		if(!$t){$this->schan($irc,$data->channel, "El usuario {$data->messageex[1]} no existe.", true); return 0; }
		if($t->congelado != 0){ $this->schan($irc, $data->channel, "No puedes enviarle dinero a una cuenta congelada.",true); return 0;}
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
		if($k->nivel>4){
			$this->lastplayer=$data->nick;
		}
		
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
			$po->pozo=$po->pozo+abs($tot);
			$ba->extrainf=json_encode($po);
		}else{
			$ba->dinero=$ba->dinero - $tot;
		}$ba->save();
		$resp="\002{$data->nick}\002\017: $comb ".(($tot<0)?"\002PERDISTE\002 $".abs($tot):"\002GANASTE\002 $$tot");

		$this->schan($irc,$data->channel,$resp);
	}
	
	public function circulando($irc,$data){
		$k = ORM::for_table('games_users')->find_many();
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
		$to = 0;
		$ba = $b->dinero;
		$bp = json_decode($b->extrainf)->pozo;
		$ia = 0;
		foreach($k as $user){
			if(($user->congelado != 0) && ($user->congelado != 3)){
				$ia = $ia + $user->dinero;
			}else{
				$to = $to + $user->dinero;
			}
		}
		$utot = $to + $ba + $bp + $ia;
		$tb = $ba + $bp;
		$this->schan($irc, $data->channel, "En el juego hay circulando, en total, \$\2$utot\2. \$\2$to\2 en manos de los jugadores, \$\2$tb\2 en el banco (de los cuales \$\2$bp\2 están en el pozo y \$\2$ba\2 son accesibles) y \$\2$ia\2 han quedado fuera de circulación en cuentas congeladas.");
	}
	
	public function dados($irc,$data){
		$k = ORM::for_table('games_users')->where("nick", $data->nick)->find_one();
		if($k->dinero<5){$this->schan($irc,$data->channel, "No tienes suficiente dinero como para jugar a este juego. Necesitas $5.", true); return 0;}
		$d1 = rand(1,6);  $d2 = rand(1,6);  $d3 = rand(1,6);
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
//		$k->dinero=$k->dinero-10;
		$po=json_decode($b->extrainf);
		
		if($b->dinero<1000){$this->schan($irc,$data->channel, "No puedes jugar. El banco está en quiebra.", true);return 0;}
		if($k->nivel>4){
			$this->lastplayer=$data->nick;
		}
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
	public function cimpuesto($irc,$perc = 10){
		$s = ORM::for_table('games_users')->where_gt("dinero", 1000)->find_many();
		$toti=0; $totu = 0;
		foreach($s as $user){
			$imp=round(($user->dinero * $perc/100),0);
			$user->dinero=$user->dinero - $imp;
			$user->save();
			$totu++; $toti = $toti+$imp;
		}
		$b = ORM::for_table('games_banco')->where("id", 1)->find_one();
		$b->dinero = $b->dinero+$toti;$b->save();
		
		$this->sendGlobalNotice($irc,"Se han cobrado \$\2$toti\2 de impuestos a $totu usuarios");
		
		return array('users'=>$totu, 'dinero'=>$toti);
	}
	
	public function sendGlobalNotice($irc,$message){
		$chans = ORM::for_table('games_channels')->find_many();
		foreach($chans as $chan){
			$irc->message(SMARTIRC_TYPE_NOTICE, $chan->channel, $message);
		}
	}
}
