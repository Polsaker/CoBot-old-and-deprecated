<?php 
$name="games"; 
$key="ee111t1t1172";
	class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'enablegame', 'games');	
			$irc->addcmd($this, 'disablegame', 'games');	
			$irc->addcmd($this, 'changemoney', 'games');	
			$irc->addcmd($this, 'impuesto', 'games');	
			$irc->addcmd($this, 'confiscar', 'games');	
			$irc->addcmd($this, 'imp', 'games');	
			$irc->addcmd($this, 'congelar', 'games');	
			$irc->addcmd($this, 'piedrapapelotijeras', 'games');	
			$irc->addcmd($this, 'ppt', 'games');	
			$irc->addcmd($this, 'pptj', 'games');	
			$irc->addcmd($this, 'infinity', 'games');	
			$irc->addcmd($this, 'delgameuser', 'games');	
			$irc->addcmd($this, 'dist', 'games');	
			$this->help['enablegame']='Activa los juegos en un canal';
			$this->help['enablegame_l']=4;
			$this->help['disablegame']='Desactiva los juegos en un canal';
			$this->help['disablegame_l']=4;
			$this->help['changemoney']='Cambia el dinero en la cuenta de un nick.';
			$this->help['changemoney_l']=4;
			$this->help['impuesto']='Cobra un impuesto del 5% a todos los que tienen mas de $100000 en el juego.';
			$this->help['impuesto_l']=4;
			$this->help['confiscar']='Confisca parte del dinero de un usuario.';
			$this->help['confiscar_l']=4;
			$this->help['imp']='Configura la exenciÛn a impuestos.';
			$this->help['imp_l']=4;
			$this->help['congelar']='Cierra o abre una cuenta cerrada.';
			$this->help['congelar_l']=4;
			$this->help['delgameuser']='Elimina una cuenta de los juegos.';
			$this->help['delgameuser_l']=4;
			$this->help['piedrapapelotijeras']='Invita a alguien a jugar a piedra papel o tijeras. Sintaxis: piedrapapelotijeras <NICK>';
			$this->help['infinity']='Regula las opciones de dinero infinito.';$this->help['infinity_l']=8;
			$this->help['dist']='Nombra un a un usuario como distinguido.';$this->help['dist_l']=8;
			$this->help['ppt_l']=11;$this->help['pptj_l']=11; // "truco" para que no se muestren esos comandos
			if(!@isset($irc->hdf['PRIVMSG'])){$irc->hdf['PRIVMSG']=array();}
			array_push($irc->hdf['PRIVMSG'],array("games","comecom")); 
		}

		public function enablegame(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("INSERT INTO games_channels (chan) VALUES ('$param[1]')",$myconn);
			mysql_close($myconn);
		}
		
		public function disablegame(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("DELETE FROM games_channels WHERE chan='$param[1]",$myconn);
			mysql_close($myconn);
		}
		public function delgameuser(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("DELETE FROM `games_users` WHERE `nick`='$param[1]'",$myconn);
			$rsx = mysql_query("DELETE FROM `games_stats` WHERE `nick`='$param[1]'",$myconn);
			mysql_close($myconn);
		}
		
		public function changemoney(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			if($param[2]=="*"){$irc->SendCommand("PRIVMSG ".$channel." :05Error: Eso no se debe hacer desde aqui"); return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$param[1]'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			if($rowx['dinero']=="*"){$irc->SendCommand("PRIVMSG ".$channel." :05Error: Eso no se debe hacer desde aqui"); return 0;}

			if($param[1]!="banco"){
				$rsx = mysql_query("UPDATE  games_users SET dinero='".$param[2]."' WHERE nick='".$param[1]."'",$myconn);
			}else{
				$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
				$rowx2=mysql_fetch_array($rsx);
				$rsx = mysql_query("UPDATE games_banco SET plata ='$param[2]' WHERE  plata=$rowx2[plata]",$myconn);
			}
			mysql_close($myconn);
		}
		
		public function infinity(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,8,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			mysql_select_db($irc->conf['db']['name']);
			if($param[2]=="*"){
				$rsx = mysql_query("UPDATE  games_users SET dinero='*' WHERE nick='".$param[1]."'",$myconn);
			}else{
				$rsx = mysql_query("UPDATE  games_users SET dinero='$param[2]' WHERE nick='".$param[1]."'",$myconn);
			}
			mysql_close($myconn);
		}
		
		public function imp(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("UPDATE  games_users SET imp='".$param[2]."' WHERE nick='".$param[1]."'",$myconn);
			mysql_close($myconn);
		}
		
		public function dist(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,8,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("UPDATE  games_users SET dist='".$param[2]."' WHERE nick='".$param[1]."'",$myconn);
			mysql_close($myconn);
		}
		
		public function congelar(&$irc,$msg,$channel,$param,$who){
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("UPDATE  games_users SET frozen='".$param[2]."' WHERE nick='".$param[1]."'",$myconn);
			mysql_close($myconn);
		}
		
		public function impuesto(&$irc,$msg,$channel,$param,$who){
		$chproc='	if($irc->checkauth("'.$who.'",4,"games")!=1){$irc->SendCommand("PRIVMSG '.$channel.' :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf[\'db\'][\'host\'],$irc->conf[\'db\'][\'user\'],$irc->conf[\'db\'][\'pass\']);mysql_select_db($irc->conf[\'db\'][\'name\']);
			$rsxq = mysql_query("SELECT * FROM games_users",$myconn);
			if("'.$param[1].'"=="s"){$s=0;}else{$s=1;}
			$u=0;$r=0;$c=0;
			while($rowx=mysql_fetch_array($rsxq)){
				$c++;
				if(($rowx["imp"]==1)&&($rowx["dinero"]<500000000)){if($s){$irc->SendCommand("PRIVMSG '.$channel.' :$rowx[nick] esta exento de cobrar impuestos.");} continue; 
				}elseif(($rowx["imp"]==1)&&($rowx["dinero"]>=500000000)){
					$imp= $rowx["dinero"] * 1/100;
					$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
					$rowx2=mysql_fetch_array($rsx);
					
					if($s){$irc->SendCommand("PRIVMSG '.$channel.' :$rowx[nick] Tiene mas de 03$500000000 y ha comprado una exenciÛn, se le cobra un impuesto del 1% (03$$imp), le quedan 03$".($rowx["dinero"]-$imp)."");}
					$rsx = mysql_query("UPDATE games_banco SET plata =\'".($rowx2["plata"]+$imp)."\' WHERE  plata=$rowx2[plata]",$myconn);
					$r=$r+$imp;$u++;
					$rsx = mysql_query("UPDATE  games_users SET dinero=\'".($rowx["dinero"]-$imp)."\' WHERE nick=\'".$rowx["nick"]."\'",$myconn);
					continue;
				}
				if($rowx["imp"]==2){
					if($rowx["dinero"]>100000){
	
						$imp= $rowx["dinero"] * 15/100;
						if($s){$irc->SendCommand("PRIVMSG '.$channel.' :$rowx[nick] Tiene mas de 03$100000 y tiene el hiperimpuesto se le cobra un impuesto del 15% (03$$imp), le quedan 03$".($rowx["dinero"]-$imp)."");}
						$rsx = mysql_query("UPDATE games_banco SET plata =\'".($rowx2["plata"]+$imp)."\' WHERE  plata=$rowx2[plata]",$myconn);
						$r=$r+$imp;$u++;
						$rsx = mysql_query("UPDATE  games_users SET dinero=\'".($rowx["dinero"]-$imp)."\' WHERE nick=\'".$rowx["nick"]."\'",$myconn);
					}else{
						if($s){$irc->SendCommand("PRIVMSG '.$channel.' :$rowx[nick] no tiene mas de 03$100000, no se le cobra impuesto.");}
					}
					continue;
				}
				if($s){sleep(1);}
				if($rowx["dinero"]>100000){
					$imp= $rowx["dinero"] * 5/100;
					$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
					$rowx2=mysql_fetch_array($rsx);
					
					if($s){$irc->SendCommand("PRIVMSG '.$channel.' :$rowx[nick] Tiene mas de 03$100000, se le cobra un impuesto del 5% (03$$imp), le quedan 03$".($rowx["dinero"]-$imp)."");}
					$rsx = mysql_query("UPDATE games_banco SET plata =\'".($rowx2["plata"]+$imp)."\' WHERE  plata=$rowx2[plata]",$myconn);
					$r=$r+$imp;$u++;
					$rsx = mysql_query("UPDATE  games_users SET dinero=\'".($rowx["dinero"]-$imp)."\' WHERE nick=\'".$rowx["nick"]."\'",$myconn);
				}else{
					if($s){$irc->SendCommand("PRIVMSG '.$channel.' :$rowx[nick] no tiene mas de 03$100000, no se le cobra impuesto.");}
				}
			}
			$irc->SendPriv("'.$channel.'","Se ha terminado de cobrar impuestos: Se han recaudado 03$$r, a $u de $c usuarios.");
			';
			file_put_contents("child-todo","<?php".$chproc);
		}
		
		public function confiscar(&$irc,$msg,$channel,$param,$who)
		{
			if($irc->checkauth($who,4,"games")!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta funci√≥n.");return 0;}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			mysql_select_db($irc->conf['db']['name']);
			if($param[1]!="banco"){
				$rsx = mysql_query("SELECT * FROM games_users WHERE nick='".$irc->mask2nick($who)."'",$myconn);$rowx=mysql_fetch_array($rsx);
				$rsx = mysql_query("SELECT * FROM games_users WHERE nick='".$param[1]."'",$myconn);$rowx2=mysql_fetch_array($rsx);
				
				if($rowx2["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx2["dinero"]-$param[2])."' WHERE nick='".$rowx2["nick"]."'",$myconn);}
				if($rowx["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+$param[2])."' WHERE nick='".$rowx["nick"]."'",$myconn);}
			}else{
				$rsx = mysql_query("SELECT * FROM games_users WHERE nick='".$irc->mask2nick($who)."'",$myconn);$rowx=mysql_fetch_array($rsx);
				$rsx = mysql_query("SELECT * FROM games_banco",$myconn);$rowx2=mysql_fetch_array($rsx);
				
				if($rowx["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+$param[2])."' WHERE nick='".$rowx["nick"]."'",$myconn);}
				if($rowx2["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_banco SET plata='".($rowx2["plata"]-$param[2])."' WHERE plata='".$rowx2["plata"]."'",$myconn);}
				
			}
			mysql_close($myconn);
		}
		
		public function piedrapapelotijeras(&$irc,$msg,$channel,$param,$who)
		{
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			mysql_select_db($irc->conf['db']['name']);
			
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='".$irc->mask2nick($who)."'",$myconn);
			if(mysql_num_rows($rsx)==0){$irc->SendCommand("PRIVMSG $channel :".$irc->mask2nick($who).": 05Error: Parece que no est·s registrado en los juegos del bot! Date de alta escribiendo !alta"); return 0;}
			$rowx=mysql_fetch_array($rsx);
			if($rowx['nivel']<3){ $irc->SendCommand("05Error: Debes ser nivel 3 o superior para jugar a este juego!!"); return 0; }
			if($rowx['frozen']!=0){ $irc->SendCommand("05Error: Esta cuenta ha sido congelada por un administrador!!"); return 0; }
			if($rowx["dinero"]!="*"){if($rowx['dinero']<100011500){ $irc->SendCommand("PRIVMSG $channel :05Error: Debes tener al menos $100011500 para jugar a este juego!!"); return 0; }}
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='".$param[1]."'",$myconn);
			if(mysql_num_rows($rsx)==0){$irc->SendCommand("PRIVMSG $channel :".$irc->mask2nick($who).": 05Error: Parece que el nick $param[1] no est· registrado en el juego!"); return 0;}$rowx2=mysql_fetch_array($rsx);
			if($rowx2["dinero"]!="*"){if($rowx2['dinero']<100011500){ $irc->SendCommand("PRIVMSG $channel :05Error: $param[1] no tiene dinero suficiente como para jugar a este juego!! (necesita, por lo menos, $100011500)"); return 0; }}
			$irc->SendCommand("PRIVMSG $channel :".$irc->mask2nick($who).": Se ha invitado a $param[1] a jugar. Se te notificara si acepta o no la partida (yo esperare 10 minutos a que responda).");
			$ts=time();
			$rsx= mysql_query("INSERT INTO games_ppt (n1,n2,ts,dn) VALUES ('".$irc->mask2nick($who)."','$param[1]','$ts',0)");
			$irc->SendCommand("PRIVMSG $param[1] :Has sido invitado a jugar al piedra papel o tijera por ".$irc->mask2nick($who).". Si quiere jugar, escriba  sin comillas \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}ppt $ts 1\" Si quiere rechazar la invitaciÛn, escriba sin comillas \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}ppt $ts 0\"");
			
			mysql_close($myconn);
		}
		public function ppt(&$irc,$msg,$channel,$param,$who){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			mysql_select_db($irc->conf['db']['name']);
			
			$rsx = mysql_query("SELECT * FROM games_ppt WHERE ts='$param[1]'",$myconn);
			if(mysql_num_rows($rsx)==0){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Partida inexistente"); return 0;}
			$rowx=mysql_fetch_array($rsx);
			if($rowx['dn']==2){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Esta partida ya ha terminado."); return 0;}
			if($rowx['dn']==-1){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Esta partida ya ha terminado."); return 0;}
			if($rowx['dn']==1){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Esta partida ya se est· jugando."); return 0;}
			if($rowx['n2']!=$irc->mask2nick($who)){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: No v·lido."); return 0;}
			if($param[2]==1){
				$ts=$rowx['ts'];
				$rsx = mysql_query("UPDATE  games_ppt SET dn='1' WHERE ts='$param[1]'",$myconn);
				$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Has aceptado la partida de piedra papel o tijera. Escribe sin comillas \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}pptj $ts piedra\" para jugar con la piedra. \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}pptj $ts papel\" para jugar con el papel o \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}pptj $ts tijeras\" para jugar con las tijeras");
				$irc->SendCommand("PRIVMSG ".$rowx['n1']." :$rowx[n2] ha aceptado su solicitud de juego, para  jugar, escriba sin comillas \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}pptj $ts piedra\" para jugar con la piedra. \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}pptj $ts papel\" para jugar con el papel o \"/msg ".$irc->nick." {$irc->conf['irc']['prefix']}pptj $ts tijeras\" para jugar con las tijeras");
			}else{
				$rsx = mysql_query("UPDATE  games_ppt SET dn='-1' WHERE ts='$param[1]'",$myconn);
				$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Has rechazado la partida.");
				$irc->SendCommand("PRIVMSG ".$rowx['n1']." :$rowx[n2] ha rechazado su solicitud de juego");
			}
			mysql_close($myconn);
		}
		
		public function pptj(&$irc,$msg,$channel,$param,$who)
		{
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			mysql_select_db($irc->conf['db']['name']);
			
			$rsx = mysql_query("SELECT * FROM games_ppt WHERE ts='$param[1]'",$myconn);
			if(mysql_num_rows($rsx)==0){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Partida inexistente"); return 0;}
			$rowx=mysql_fetch_array($rsx);
			if($rowx['dn']==2){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Esta partida ya ha terminado."); return 0;}
			if($rowx['dn']==-1){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Esta partida ya ha terminado."); return 0;}
			if(($rowx['n2']!=$irc->mask2nick($who)) && ($rowx['n1']!=$irc->mask2nick($who))){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: No v·lido."); return 0;}
			
			if($irc->mask2nick($who)==$rowx['n2']){$n=2;}else{$n=1;}

			switch ($param[2]){
				case "piedra":if($n==1){$qp="n1m=1";}else{$qp="n2m=1";}break;
				case "papel": if($n==1){$qp="n1m=2";}else{$qp="n2m=2";}break;
				case "tijeras": if($n==1){$qp="n1m=3";}else{$qp="n2m=3";}break;
			}
			$rsx = mysql_query("UPDATE  games_ppt SET $qp WHERE ts='$param[1]'",$myconn);
			$rsx = mysql_query("SELECT * FROM games_ppt WHERE ts='$param[1]'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			if(($rowx['n1m']!=0) && ($rowx['n2m']!=0)){
				$r=111; // Resultado: 0 = empate; 1 = Gana el jugador 1; 2 = Gana el jugador 2
				// Nota mental: simplificar esto:
				$m1=$rowx['n1m'];$m2=$rowx['n2m'];
				if($m1==$m2){$r=0;} 			//empates
				if(($m1==1)&&($m2==2)){$r=2;}	//Piedra vs Papel, Gana Papel
				if(($m1==1)&&($m2==3)){$r=1;}	//Piedra vs Tijeras, Gana Tijeras
				if(($m1==2)&&($m2==1)){$r=1;}	//Papel vs Piedra, Gana Papel
				if(($m1==2) && ($m2==3)){$r=2;}	//Papel vs Tijeras, Gana Tijeras
				if(($m1==3)&&($m2==1)){$r=2;}	//Tijeras vs Piedra, Gana Piedra
				if(($m1==3)&&($m2==2)){$r=1;}	//Tijeras vs Papel, Gana Tijeras
				$o1="";$o2="";
				switch($m1){case 1: $o1="piedra";break; case 2:$o1="papel";break;case 3: $o1="tijeras";break;}
				switch($m2){case 1: $o2="piedra";break; case 2:$o2="papel";break;case 3: $o2="tijeras";break;}
				
				switch($r){
					case 0:
						$irc->SendCommand("PRIVMSG $rowx[n1] :[$rowx[n1] escogiÛ $o1 y $rowx[n2] escogiÛ $o2]: Empate, no ha ganado nadie. El banco se lleva $30000 de cada uno.");
						$irc->SendCommand("PRIVMSG $rowx[n2] :[$rowx[n1] escogiÛ $o1 y $rowx[n2] escogiÛ $o2]: Empate, no ha ganado nadie. El banco se lleva $30000 de cada uno.");
						$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$rowx[n1]'",$myconn);$rowx2=mysql_fetch_array($rsx);if($rowx2["dinero"]!="*"){$d=$rowx2['dinero'] - 30000;$rsx = mysql_query("UPDATE games_users SET dinero='$d' WHERE nick='$rowx[n1]'",$myconn);}
						$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$rowx[n2]'",$myconn);$rowx2=mysql_fetch_array($rsx);if($rowx2["dinero"]!="*"){$d=$rowx2['dinero'] - 30000;$rsx = mysql_query("UPDATE games_users SET dinero='$d' WHERE nick='$rowx[n2]'",$myconn);}
						$rsx = mysql_query("SELECT * FROM games_banco",$myconn);$rowx2=mysql_fetch_array($rsx);$rsx = mysql_query("UPDATE games_banco SET plata='".($rowx2['plata']+60000)."' WHERE plata='$rowx2[plata]'",$myconn);
						break;
					case 1:
						$irc->SendCommand("PRIVMSG $rowx[n1] :[$rowx[n1] escogiÛ $o1 y $rowx[n2] escogiÛ $o2]: Ganaste!!! Te llevas $100000000 de $rowx[n2]");
						$irc->SendCommand("PRIVMSG $rowx[n2] :[$rowx[n1] escogiÛ $o1 y $rowx[n2] escogiÛ $o2]: PERDISTE!!! $rowx[n1] se lleva $100000000 tuyos!");
						$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$rowx[n1]'",$myconn);$rowx2=mysql_fetch_array($rsx);if($rowx2["dinero"]!="*"){$d=$rowx2['dinero'] + 100000000;$rsx = mysql_query("UPDATE games_users SET dinero='$d' WHERE nick='$rowx[n1]'",$myconn);}
						$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$rowx[n2]'",$myconn);$rowx2=mysql_fetch_array($rsx);if($rowx2["dinero"]!="*"){$d=$rowx2['dinero'] - 100000000;$rsx = mysql_query("UPDATE games_users SET dinero='$d' WHERE nick='$rowx[n2]'",$myconn);}
						break;
					case 2:
						$irc->SendCommand("PRIVMSG $rowx[n1] :[$rowx[n1] escogiÛ $o1 y $rowx[n2] escogiÛ $o2]: PERDISTE!!! $rowx[n2] se lleva $100000000 tuyos!");
						$irc->SendCommand("PRIVMSG $rowx[n2] :[$rowx[n1] escogiÛ $o1 y $rowx[n2] escogiÛ $o2]: Ganaste!!! Te llevas $100000000 de $rowx[n1]");
						$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$rowx[n1]'",$myconn);$rowx2=mysql_fetch_array($rsx);if($rowx2["dinero"]!="*"){$d=$rowx2['dinero'] - 100000000;$rsx = mysql_query("UPDATE games_users SET dinero='$d' WHERE nick='$rowx[n1]'",$myconn);}
						$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$rowx[n2]'",$myconn);$rowx2=mysql_fetch_array($rsx);if($rowx2["dinero"]!="*"){$d=($rowx2['dinero'] + 100000000);$rsx = mysql_query("UPDATE games_users SET dinero='$d' WHERE nick='$rowx[n2]'",$myconn);}
						break;
				}
				$rsx = mysql_query("UPDATE  games_ppt SET dn='2' WHERE ts='$param[1]'",$myconn);
			}
			
			
		}
		
		public function comecom(&$irc,$txt){
			if(preg_match('@^:(.+) PRIVMSG (.+) :(.+)@', $txt, $m)){
				@$ppl = $irc->mask2nick($m[1]);
				@$chn = $m[2];

				@$msg = $m[3];
				if($ppl=="banco"){return 0;}
				if(preg_match('@^!(.+)@', $msg, $m2)){
					
					$cmd=trim($m2[1]);
					
					$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);

					$rsx = mysql_query("SELECT * FROM users WHERE user='$ppl'",$myconn);
					if(mysql_num_rows($rsx)!=0){
						preg_match('/:(.*)/',$txt,$matches);
						if($irc->checkauth(($m[1]." "),0)!=1){
							mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: Estas usando un nick registrado por otra persona. Identificate o usa otro nick.");return 0;
						}
					}
					
					$rsx = mysql_query("SELECT * FROM games_channels WHERE chan='$chn'",$myconn);
					if(mysql_num_rows($rsx)==0){mysql_close($myconn);return 0;}
					if($cmd=="alta"){mysql_close($myconn);$this->alta($irc,$ppl,$chn);return 0;}
					$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$ppl'",$myconn);
					if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: No estas dado de alta, date de alta con el comando !alta");return 0;}
					$rowu=mysql_fetch_array($rsx);
					if($rowu["frozen"]!=0){	mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: Esta cuenta ha sido congelada por un administrador.");return 0; }
					mysql_close($myconn);
					$cmd=explode(" ", $cmd);
					//print_r($cmd);
					time_nanosleep(0,5300000);
					switch($cmd[0]){
						case "dinero":$this->saldo($irc,$ppl,$chn,$cmd, $m[1]);break;
						case "saldo":$this->saldo($irc,$ppl,$chn,$cmd, $m[1]);break;
						case "transferir":$this->transferir($irc,$ppl,$chn,$cmd);break;
						case "transferencia":$this->transferir($irc,$ppl,$chn,$cmd);break;
						case "dados":
							time_nanosleep(0,250000000);
							$this->dados($irc,$ppl,$chn);
							break;
						case "ayuda":
							$irc->SendCommand("PRIVMSG ".$chn." :No implementado.");
							break;
						case "arcas":
							$this->arcas($irc,$ppl,$chn);
							break;
						case "tragaperras":
							time_nanosleep(0,250000000);
							$this->tragaperras($irc,$ppl,$chn);
							break;
						case "rueda":
							$this->rueda($irc,$ppl,$chn);
							break;
						case "circulando":
							$this->circ($irc,$ppl,$chn);
							break;
						case "bono":
							$this->bono($irc,$ppl,$chn,$cmd);
							break;
						case "nivel":
							$this->nivel($irc,$ppl,$chn,$cmd);
							break;
						case "top":
							$this->top($irc,$ppl,$chn,$cmd,5);
							break;
						case "top10":
							$this->top($irc,$ppl,$chn,$cmd,10);
							break;
						case "comprar":
							$this->comprar($irc,$ppl,$chn,$cmd);
							break;
						case "cobre": $this->cobre($irc,$ppl,$chn,$cmd);break;
						case "plata": $this->plata($irc,$ppl,$chn,$cmd);break;
						case "oro": $this->oro($irc,$ppl,$chn,$cmd);break;
					}
				}
			}
			
		}
		private function alta(&$irc,$nick,$chn){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			if(mysql_num_rows($rsx)!=0){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: Ese usuario ya est√° registrado!!");return 0;}
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			if($rowx2["plata"]<8000){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: El banco no tiene dinero!! Espera a que el banco obtenga dinero y registrate! (se necesitan $6000 [$1000 saldo de bienvenida+$5000 bono de bienvenida])");return 0;}
			$rsx = mysql_query("INSERT INTO games_users (nick,dinero,bono) VALUES ('$nick','3000',1)",$myconn);
			$rsx = mysql_query("INSERT INTO games_stats (nick) VALUES ('$nick')",$myconn);
			$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]-8000)."' WHERE  plata=$rowx2[plata]",$myconn);
			mysql_close($myconn);
			$irc->SendCommand("PRIVMSG ".$chn." :Te has dado de alta!! ahora tienes $3000 y un bono para empezar a jugar!!");
		}
		private function saldo(&$irc,$nick,$chn,$cmd, $hs){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			if(@$cmd[1]){
				if($cmd[1]=="banco"){
					$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
					$rowx=mysql_fetch_array($rsx);
					$irc->SendCommand("PRIVMSG ".$chn." :En el banco hay $$rowx[plata] Flags: [02B] [04Co ".$rowx["cobre"]."] [15Pl ".$rowx["plat"]."] [08Oro ".$rowx["oro"]."]");
					return 0;
				}
				$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$cmd[1]'",$myconn);
				if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: El nick $cmd[1] no esta registrado o no existe.");return 0;}
				$rowx=mysql_fetch_array($rsx);
				$flags="Flags: ";
				if($rowx['dinero']!="*"){
					if($rowx["dinero"]<10000){$flags.="[11P] ";} //flag Pobre
					if($rowx["dinero"]>1000000){$flags.="[03M] ";} //flag millonario
					if($rowx["dinero"]>100000000){$flags.="[05M] ";} //flag multimillonario
				}else{$flags.="[03M] [05M] [02M+] ";}
				if($rowx["imp"]==1){$flags.="[05I] ";} //flag exento
				if($rowx["imp"]==2){$flags.="[11I] ";} //flag hiperimpuesto
				if($rowx["frozen"]!=0){$flags.="[04F] ";} // flag congelado
				$rsx = mysql_query("SELECT * FROM users WHERE user='$cmd[1]'",$myconn);
				if(mysql_num_rows($rsx)!=0){
					$rowx2=mysql_fetch_array($rsx);
					$pr2=explode("|",$rowx2["rng"]);
					$pr4=array();
					foreach($pr2 as $key=>$val){
						$pr3=explode(",",$val);
						array_push($pr4,$pr3);
					}
					$a=0;$ap=0;$o=0;
					foreach($pr4 as $key=>$val){
						if(("games"==$pr4[$key][1]) || ($pr4[$key][1]=="*")){
								if(($pr4[$key][0]>=4) || ($pr4[$key][0]>="o4")){$a=1;}	
								if(($pr4[$key][0]>=8) || ($pr4[$key][0]>="o8")){$ap=1;}
								if($pr4[$key][0]>=10){$o=1;}								
						}
					}
					if($a){$flags.="[11A] ";} //flag admin
					if($ap){$flags.="[15+A] ";} //flag +admin
					if($o){$flags.="[10O] ";} //flag dueÒo
					$flags.="[07R] "; // flag registrado
				}
				if($rowx["dist"]!=0){$flags.="[03D] ";}

				if($rowx["caja"]>0){$flags.="[06C ".$rowx["caja"]."] ";}
				if($rowx["cobre"]>0){$flags.="[04Co ".$rowx["cobre"]."] ";}
				if($rowx["plata"]>0){$flags.="[15Pl ".$rowx["plata"]."] ";}
				if($rowx["oro"]>0){$flags.="[08Oro ".$rowx["oro"]."] ";}

				$flags=trim($flags);
				if($rowx['dinero']=="*"){$rowx['dinero']=mb_convert_encoding("&#8734;", 'UTF-8',  'HTML-ENTITIES')." (infinito)";}
				$irc->SendCommand("PRIVMSG ".$chn." :En la cuenta de ".($rowx["dist"]?"":"")."$cmd[1]".($rowx["dist"]?"":"")." hay $$rowx[dinero] $flags [N $rowx[nivel]]");
			}else{
				$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
				$rowx=mysql_fetch_array($rsx);
				$flags="Flags: ";
				if($rowx['dinero']!="*"){
					if($rowx["dinero"]<10000){$flags.="[11P] ";} //flag Pobre
					if($rowx["dinero"]>1000000){$flags.="[03M] ";} //flag millonario
					if($rowx["dinero"]>100000000){$flags.="[05M] ";} //flag multimillonario
				}else{$flags.="[03M] [05M] [02M+] ";}
				if($rowx["imp"]==1){$flags.="[05I] ";} //flag exento
				if($rowx["imp"]==2){$flags.="[11I] ";} 
				if($rowx["frozen"]!=0){$flags.="[04F] ";} // flag congelado
								$rsx = mysql_query("SELECT * FROM users WHERE user='$nick'",$myconn);
							if(mysql_num_rows($rsx)!=0){
					$rowx2=mysql_fetch_array($rsx);
					$pr2=explode("|",$rowx2["rng"]);
					$pr4=array();
					foreach($pr2 as $key=>$val){
						$pr3=explode(",",$val);
						array_push($pr4,$pr3);
					}
					$a=0;$ap=0;$o=0;
					foreach($pr4 as $key=>$val){
						if(("games"==$pr4[$key][1]) || ($pr4[$key][1]=="*")){
								if(($pr4[$key][0]>=4) || ($pr4[$key][0]>="o4")){$a=1;}	
								if(($pr4[$key][0]>=8) || ($pr4[$key][0]>="o8")){$ap=1;}
								if($pr4[$key][0]>=10){$o=1;}								
						}
					}
					if($a){$flags.="[11A] ";} //flag admin
					if($ap){$flags.="[15+A] ";} //flag +admin
					if($o){$flags.="[10O] ";} //flag dueÒo
					$flags.="[07R] "; // flag registrado
					
				}
				if($rowx["dist"]!=0){$flags.="[03D] ";}

				
				if($rowx["caja"]>0){$flags.="[06C ".$rowx["caja"]."] ";}
				if($rowx["cobre"]>0){$flags.="[04Co ".$rowx["cobre"]."] ";}
				if($rowx["plata"]>0){$flags.="[15Pl ".$rowx["plata"]."] ";}
				if($rowx["oro"]>0){$flags.="[08Oro ".$rowx["oro"]."] ";}


				$flags=trim($flags);
if($rowx['dinero']=="*"){$rowx['dinero']=mb_convert_encoding("&#8734;", 'UTF-8',  'HTML-ENTITIES')." (infinito)";}
				$irc->SendCommand("PRIVMSG ".$chn." :".($rowx["dist"]?"":"")."$nick".($rowx["dist"]?"":"").": en tu cuenta tienes $$rowx[dinero] $flags [N $rowx[nivel]]");
			}
			$rowx=mysql_fetch_array($rsx);
			
			mysql_close($myconn);
			
		}
		public function transferir(&$irc,$nick,$chn, $cmd){
				$cmd[2]=abs($cmd[2]);
				$cmd[2]=trim($cmd[2],"+-*/");
				if(!is_numeric($cmd[2])){return 0;}
				if(strtolower($cmd[1])==strtolower($nick)){return 0;}
				if(!is_numeric(substr($cmd[2],0,1))){return 0;}
				$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
				$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
				$rowx=mysql_fetch_array($rsx);
				if($cmd[1]!="banco"){$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$cmd[1]'",$myconn);
				}else{$rsx = mysql_query("SELECT * FROM games_banco",$myconn);}
				
				if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: El nick $cmd[1] no esta registrado o no existe.");return 0;}
				$rowx2=mysql_fetch_array($rsx);
				if($cmd[1]!="banco"){if($rowx2["frozen"]!=0){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: No puedes enviarle dinero a una cuenta congelada");return 0;}}
				if($rowx["dinero"]!="*"){
					if($rowx["dinero"]<$cmd[2]){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: No posees dinero suficiente como para hacer eso!!");return 0;}
					if(($rowx["dinero"]-$cmd[2])<5000){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: NO DEBES transferir todo tu dinero!!! SIEMPRE deben quedarte por lo menos $5000");return 0;}
				}
				if($rowx["dinero"]!="*"){
				$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-$cmd[2])."' WHERE nick='".$nick."'",$myconn);}
				
				if($cmd[1]!="banco"){
					if($rowx2["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx2["dinero"]+$cmd[2])."' WHERE nick='".$cmd[1]."'",$myconn);}
				}else{
					$rsx = mysql_query("UPDATE  games_banco SET plata='".($rowx2["plata"]+$cmd[2])."' WHERE plata='".$rowx2["plata"]."'",$myconn);
				}
				$irc->SendCommand("PRIVMSG ".$chn." :$nick: se han transferido $$cmd[2] a $cmd[1]");
				mysql_close($myconn);
			}
		private function dados(&$irc,$nick,$chn){
			$d1=rand(1,6);
			$d2=rand(1,6);
			$d3=rand(1,6);
			$d=$d1+$d2+$d3;
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);

			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowxa=mysql_fetch_array($rsx);
			if ($d%2==0){
				$m=1;
				switch($rowx["nivel"]){
					case 0:	$w=rand(20, 1000);	break;
					case 1: $w=rand(30, 2500);	break;
					case 2:	$w=rand(40, 3500);	break;
					case 3:	$w=rand(50, 3000);	break;
					case 4:	$w=rand(90, 5000);	break;
				}
				if($rowx["nivel"]>3){$w=rand(1, 700);}
					
			}else{
				$m=0;
				switch($rowx["nivel"]){
					case 0:	$w=rand(20, 1000);	break;
					case 1:	$w=rand(500, 3000);	break;
					case 2:	$w=rand(1000, 8000);break;
					case 3:	$w=rand(5000, 11000);break;
					case 4:	$w=rand(6000, 17000);break;
				}
				if($rowx["nivel"]>3){$w=rand(5000, 10000);}
			}
			
			if($rowxa["dinero"]!="*"){
				if($rowx["dinero"]<25){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, necesitas $25 para poder jugar a los dados.");mysql_close($myconn);return 0;}else{$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-25)."' WHERE nick='".$nick."'",$myconn);}
			}
			
			$q1=mysql_query("SELECT * FROM games_stats WHERE nick='$nick'");$n=mysql_fetch_array($q1);
			$q2=mysql_query("UPDATE games_stats SET dados ='".($n["dados"]+1)."' WHERE  nick='$nick'",$myconn);

			
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]+25)." WHERE  plata=$rowx2[plata]",$myconn);
			if($rowx["dinero"]!="*"){$rowx["dinero"]=$rowx["dinero"]-25;}
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			
			if($m==0){
				if($rowx2["plata"]<=500){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, el banco no puede pagarte por que no tiene dinero...");return 0;}
					if($rowxa["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+$w)."' WHERE nick='".$nick."'",$myconn); }
					$irc->SendCommand("PRIVMSG ".$chn." :$nick: [$d1+$d2+$d3=$d] Ganaste $$w!!!");
					$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]-$w)." WHERE  plata=$rowx2[plata]",$myconn);
					$d=$rowx["dinero"]-abs($w);
			}else{  if($rowxa["dinero"]!="*"){$rsx = mysql_query("UPDATE  games_users SET dinero=".($rowx["dinero"]-$w)." WHERE nick='".$nick."'",$myconn);}$irc->SendCommand("PRIVMSG ".$chn." :$nick: [$d1+$d2+$d3=$d] Has perdido $$w");
			$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]+$w)." WHERE  plata=$rowx2[plata]",$myconn);
			}
		}
		
		private function arcas(&$irc,$nick,$chn){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$irc->SendCommand("PRIVMSG ".$chn." :$nick: en las arcas del banco hay $$rowx[plata]");
			mysql_close($myconn);
			
		}
		private function tragaperras(&$irc,$nick,$chn){
			$d=rand(1,10);
			
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowxa=mysql_fetch_array($rsx);
			if($rowxa["dinero"]!="*"){
			if($rowx["nivel"]<1){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, Debes ser por lo menos nivel 1 para poder jugar.");return 0;}

			if($rowx["dinero"]<700){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, necesitas $700 para poder jugar.");mysql_close($myconn);return 0;}else{$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-500)."' WHERE nick='".$nick."'",$myconn);}
			}
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]+500)."' WHERE  plata=$rowx2[plata]",$myconn);
			$rowx["dinero"]=$rowx["dinero"]-700;
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			$fig="";$win=0;$los=0;
			$d1=rand(1,3);
			$d2=rand(1,5);
			$d3=rand(1,3);
			$n1=0;$n2=0;$r=0;
			switch($rowx["nivel"]){
				case 1:	$atv=50;$ptv=250;$xtv=700;$ca=0;break;
				case 2:	$atv=100;$ptv=500;$xtv=1000;$ca=1;break;
				case 3:$atv=200;$ptv=700;$xtv=2000;$ca=2;break;
				case 4:$atv=500;$ptv=1000;$xtv=5000;$ca=3;break;
				case 5:$atv=700;$ptv=2000;$xtv=6000;$ca=3;break;
				case 6:$atv=800;$ptv=2500;$xtv=7000;$ca=3;break;
				case 7:$atv=800;$ptv=2000;$xtv=5000;$ca=3;break;
			}
			$q1=mysql_query("SELECT * FROM games_stats WHERE nick='$nick'");$n=mysql_fetch_array($q1);
			$q2=mysql_query("UPDATE games_stats SET traga ='".($n["traga"]+1)."' WHERE  nick='$nick'",$myconn);

			if($rowx["nivel"]>4){$atv=500;$ptv=2000;$xtv=7000;$ca=5;}
			switch($d1){case 1:$fig ="[03@] ";$n1=$atv;break;case 2:$fig ="[07%] ";$n1=$ptv;break;case 3:$fig ="[04$] ";$n1=$xtv;break;}
			switch($d2){case 1:$fig.="[03@] ";break;         case 2:$fig.="[07%] ";break;         case 3:$fig.="[04$] ";break;          case 4:$fig.="[11Q] ";break;case 5:$fig.="[13X] ";break;}
			switch($d3){case 1:$fig.="[03@] ";$n2=100;break;case 2:$fig.="[07%] ";$n2=500;break;case 3:$fig.="[04$] ";$n2=5000;break;}
			
			switch($d2){case 1:$r=$n1+$n2;break;case 2:$r=$n1-$n2;break;case 3:$r=$n1*$n2;break;case 4:$r=$n1/$n2;break;case 5:if($ca==0){$r=0-$n1;}elseif(($ca==1)||($ca==2)){$r=0-($n1+$n2);}else{$r=0-($n1*$n2);}break;}
		//	if($rowx["nivel"]<2){if($r>10000){if($rowx["nivel"]<1){$r=10000;}else{$r=50000;}}}
		//	if($rowx["nivel"]<2){if($r<-10000){if($rowx["nivel"]<1){$r=10000;}else{$r=30000;}}}
			if(($d1==1)&&($d2==1)&&($d3==1)){if($rowx["nivel"]<1){$r=2500000;}else{$r=500000000;}}
			if(($d1==2)&&($d2==2)&&($d3==2)){if($rowx["nivel"]<1){$r=2500000;}else{$r=500000000;}}
			if(($d1==3)&&($d2==3)&&($d3==3)){if($rowx["nivel"]<1){$r=2500000;}else{$r=500000000;}}
			if(($r>=0)&&($r<50)){$r=50;}
		
			if($r<0){
				if(($rowx["dinero"]-abs($r))<5000){$r=$rowx["dinero"]-($rowx["dinero"]-5000);}
				$irc->SendCommand("PRIVMSG ".$chn." :$nick: $fig : Has perdido 03$".abs($r)."!!!");
				
				$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]+abs($r))."' WHERE  plata=$rowx2[plata]",$myconn);
				if($rowxa["dinero"]!="*"){
				$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-(abs($r)))."' WHERE nick='".$nick."'",$myconn);}
			}else{
				if($rowx2["plata"]<=$r){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, el banco no puede pagarte por que no tiene dinero...");return 0;}

				$irc->SendCommand("PRIVMSG ".$chn." :$nick: $fig : Ganaste 03$$r!!!!");
				if($rowxa["dinero"]!="*"){
				$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+abs($r))."' WHERE nick='".$nick."'",$myconn);}
				$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]-abs($r))."' WHERE  plata=$rowx2[plata]",$myconn);
			}
		}
		
		private function is_negative($valor){
			if(@is_int(strpos($x, "-"))) {
				return true;
			} else {
				return false;
			}
		}
		private function rueda(&$irc,$nick,$chn){
			$d=rand(1,6);
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsxa = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowxa=mysql_fetch_array($rsxa);
			if($rowx["dinero"]!="*"){
				if($rowx["nivel"]<4){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, Debes ser por lo menos nivel 4 para poder jugar.");return 0;}
				if($rowx["dinero"]<50000){$irc->SendCommand("PRIVMSG ".$chn." :$nick: Lo siento, necesitas $50000 para poder jugar.");mysql_close($myconn);return 0;}else{$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-50000)."' WHERE nick='".$nick."'",$myconn);}
			}
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
if($rowx2["plata"]<10000000){ $irc->SendCommand("PRIVMSG $chn :Lo siento, el banco no tiene suficiente dinero..."); return 0; }
			$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]+5000)."' WHERE  plata=$rowx2[plata]",$myconn);
			$rowx["dinero"]=$rowx["dinero"]-50000;
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			$final=$rowx["dinero"];
			$finald=$rowx["dinero"];
			if($rowxa["dinero"]=="*"){$finala=mb_convert_encoding("&#8734;", 'UTF-8',  'HTML-ENTITIES')." (infinito)";}
			$irc->SendCommand("PRIVMSG ".$chn." :$nick tira con fuerza de la rueda y....");
			sleep(2);
			$q1=mysql_query("SELECT * FROM games_stats WHERE nick='$nick'");$n=mysql_fetch_array($q1);
			$q2=mysql_query("UPDATE games_stats SET rued ='".($n["rued"]+1)."' WHERE  nick='$nick'",$myconn);
			switch($d){
				case 1:	$final=round($final+($rowx2["plata"] * 20/100));$finald=round($final);if($rowxa["dinero"]=="*"){$final="*";$finald=$finala;}  $rsx = mysql_query("UPDATE  games_users SET dinero=$final WHERE nick='$nick'",$myconn); $rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]-($rowx2["plata"] * 25/100))." WHERE  plata=$rowx2[plata]",$myconn);$irc->SendCommand("PRIVMSG ".$chn." :$nick: 04GANASTE11 EL 20% DEL DINERO DEL BANCO!!! Ahora tienes03 $$finald");break;
				case 2:	$final=round($final * 25/100);$finald=round($final);if($rowxa["dinero"]=="*"){$final="*";$finald=$finala;} $rsx = mysql_query("UPDATE  games_users SET dinero=$final WHERE nick='$nick'",$myconn);	$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]+($rowx["dinero"] * 75/100))." WHERE  plata=$rowx2[plata]",$myconn);$irc->SendCommand("PRIVMSG ".$chn." :$nick: 04PERDISTE11 el 75% de tu dinero!! Ahora tienes 03$$finald");break;
				case 3:	$final=round($final+($rowx2["plata"] * 15/100));$finald=round($final);if($rowxa["dinero"]=="*"){$final="*";$finald=$finala;} $rsx = mysql_query("UPDATE  games_users SET dinero=$final WHERE nick='$nick'",$myconn);$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]-($rowx2["plata"] * 15/100))." WHERE  plata=$rowx2[plata]",$myconn);$irc->SendCommand("PRIVMSG ".$chn." :$nick: 04GANASTE11 EL 15% DEL DINERO DEL BANCO!!! Ahora tienes03 $$finald");break;
				case 4: $final=round($final * 50/100);$finald=round($final);if($rowxa["dinero"]=="*"){$final="*";$finald=$finala;} $rsx = mysql_query("UPDATE  games_users SET dinero=$final WHERE nick='$nick'",$myconn);$rsx = mysql_query("UPDATE  games_users SET imp=2 WHERE nick='$nick'",$myconn);	$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]+$final)." WHERE  plata=$rowx2[plata]",$myconn);$irc->SendCommand("PRIVMSG ".$chn." :$nick: 04PERDISTE11 el 50% de tu dinero y tendras que pagar el triple de impuestos!! Ahora tienes 03$$final" );break;
				case 5: $final=round($final * 75/100);$finald=round($final);if($rowxa["dinero"]=="*"){$final="*";$finald=$finala;}$rsx = mysql_query("UPDATE  games_users SET dinero=$final WHERE nick='$nick'",$myconn);	$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]+($rowx["dinero"] * 25/100))." WHERE  plata=$rowx2[plata]",$myconn);$irc->SendCommand("PRIVMSG ".$chn." :$nick: 04PERDISTE11 el 25% de tu dinero!! Ahora tienes 03$$finald");break;
				case 6:	$final=1000;$finald=$final;if($rowxa["dinero"]=="*"){$final="*";$finald=$finala;}$rsx = mysql_query("UPDATE  games_users SET dinero=$final WHERE nick='$nick'",$myconn);	$rsx = mysql_query("UPDATE games_banco SET plata =".($rowx2["plata"]+($rowx["dinero"]-500))." WHERE  plata=$rowx2[plata]",$myconn);	$irc->SendCommand("PRIVMSG ".$chn." :$nick: 04PERDISTE11 TODO TU DINERO!! Quedaron 03$1000 para amortizar la perdida.");break;
			}
			
		
		}
		private function circ(&$irc,$nick,$chn){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			
			$rsx = mysql_query("SELECT * FROM games_users",$myconn);
			$tm=0;
			while($rowx=mysql_fetch_array($rsx)){if($rowx["dinero"]!="*"){$tm=$tm+$rowx["dinero"];}}
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			
			$irc->SendCommand("PRIVMSG ".$chn." :$nick: En total, hay circulando 03$".($tm+$rowx2['plata']).": 03$$rowx2[plata] en el banco y 03$$tm en el bolsillo de los usuarios.");	

		}
		
		private function bono(&$irc,$nick,$chn, $cmd){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			
			switch(@$cmd[1]){
				case "comprar":
					if($rowx["bono"]==0){
						if(($rowx["dinero"]>=5000)||($rowx["dinero"]=="*")){
							if($rowx["dinero"]!="*"){
							$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-5000)."' WHERE nick='".$nick."'",$myconn);}
							$rsx = mysql_query("UPDATE  games_users SET bono='1' WHERE nick='".$nick."'",$myconn);
							$irc->SendCommand("PRIVMSG ".$chn." :$nick: Has comprado un bono!!");
						}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: No tienes dinero suficiente para comprar un bono!! Necesitas $5000");return 0;}
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: Ya tienes un bono!!");return 0;}
					break;
				case "gastar":
					if($rowx["bono"]!=0){
						if($rowx["bono"]==1){
							if($rowx["dinero"]!="*"){
							$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+5000)."' WHERE nick='".$nick."'",$myconn);}
							$rsx = mysql_query("UPDATE  games_users SET bono='0' WHERE nick='".$nick."'",$myconn);
							$irc->SendCommand("PRIVMSG ".$chn." :$nick: Se han acreditado los $5000 de tu bono.");
						}else{
							if($rowx["dinero"]!="*"){
							$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+10000)."' WHERE nick='".$nick."'",$myconn);}
							$rsx = mysql_query("UPDATE  games_users SET bono='0' WHERE nick='".$nick."'",$myconn);
							$irc->SendCommand("PRIVMSG ".$chn." :$nick: Se han acreditado los $10000 de tu bono.");
						}
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: No tienes un bono!!");return 0;}
					break;
				case "ultrabono":
					if($rowx["bono"]==0){
						if(($rowx["dinero"]>=10000)||($rowx["dinero"]=="*")){
							if($rowx["dinero"]!="*"){
							$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-10000)."' WHERE nick='".$nick."'",$myconn);}
							$rsx = mysql_query("UPDATE  games_users SET bono='2' WHERE nick='".$nick."'",$myconn);
							$irc->SendCommand("PRIVMSG ".$chn." :$nick: Has comprado un ultrabono!!");
						}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: No tienes dinero suficiente para comprar un ultrabono!! Necesitas $10000");return 0;}
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$chn." :05Error: Ya tienes un bono!!");return 0;}
					
					break;
			}
			mysql_close($myconn);
		}
		
		private function nivel(&$irc,$nick,$chn, $cmd){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			switch(@$cmd[1]){
				case 1:
					if(($rowx["dinero"]>35000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>1){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 1!!");return 0;}

						$rsx = mysql_query("UPDATE  games_users SET nivel=1 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,25000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 1!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 35000 para poder pasar al nivel 1!!");return 0;}
					break;
				case 2:
					if(($rowx["dinero"]>100000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>2){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 2!!");return 0;}
						if($rowx['nivel']!=1){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 1 antes de ir al 2!");return 0;}
						$rsx = mysql_query("UPDATE  games_users SET nivel=2 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,75000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 2!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 100000 para poder pasar al nivel 2!!");return 0;}
					break;
				case 3:
					if(($rowx["dinero"]>250000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>3){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 3!!");return 0;}
						if($rowx['nivel']!=2){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 2 antes de ir al 3!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=3 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,200000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 3!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 250000 para poder pasar al nivel 3!!");return 0;}
					break;
				case 4:
					if(($rowx["dinero"]>600000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>4){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 4!!");return 0;}
						if($rowx['nivel']!=3){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 3 antes de ir al 4!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=4 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,500000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 4!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 600000 para poder pasar al nivel 4!!");return 0;}
					break;
				case 5:
					if(($rowx["dinero"]>1100000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>5){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 5!!");return 0;}
						if($rowx['nivel']!=4){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 4 antes de ir al 5!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=5 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,1000000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 5!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 1100000 para poder pasar al nivel 5!!");return 0;}
					break;
				case 6:
					if(($rowx["dinero"]>6000000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>6){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 6!!");return 0;}
						if($rowx['nivel']!=5){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 5 antes de ir al 6!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=6 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,5000000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 6!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 6000000 para poder pasar al nivel 6!!");return 0;}
					break;
				case 7:
					if(($rowx["dinero"]>11000000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>7){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 7!!");return 0;}
						if($rowx['nivel']!=6){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 6 antes de ir al 7!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=7 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,10000000,false,$myconn);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 7!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 11000000 para poder pasar al nivel 7!!");return 0;}
					break;
				case 8:
					if(($rowx["dinero"]>70000000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>8){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 8!!");return 0;}
						if($rowx['nivel']!=7){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 7 antes de ir al 8!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=8 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,50000000);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 8!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 70000000 para poder pasar al nivel 8!!");return 0;}
					break;
				case 9:
					if(($rowx["dinero"]>700000000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']>9){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Ya estas en un nivel superior al 9!!");return 0;}
						if($rowx['nivel']!=8){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 8 antes de ir al 9!");return 0;}
						
						$rsx = mysql_query("UPDATE  games_users SET nivel=9 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,500000000,false,$myconn);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 9!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 700000000 para poder pasar al nivel 9!!");return 0;}
					break;
				case 10:
					if(($rowx["dinero"]>7000000000)||($rowx["dinero"]=="*")){
						if($rowx['nivel']!=9){mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Tienes que pasar por el nivel 9 antes de ir al 10!");return 0;}
						$rsx = mysql_query("UPDATE  games_users SET nivel=10 WHERE nick='$nick'",$myconn);
						$this->user2bank($irc,$nick,5000000000,false,$myconn);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora eres Nivel 10!!");
					}else{mysql_close($myconn);$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas 7000000000 para poder pasar al nivel 10!!");return 0;}
					break;
				default: $irc->SendPriv($chn, "Sintaxis: !nivel <numero de nivel>. Solo hay niveles del 1 al 10.");
			}
			mysql_close($myconn);
		}
			
		private function top(&$irc,$nick,$chn, $cmd,$num){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$q = mysql_query("SELECT * FROM games_users",$myconn);

			$din=array();
			while(@$a=mysql_fetch_array($q)){
				if($a['dinero']=="*"){if($param[1]=="*"){$dinero=1000000000000000000000000123;}else{$dinero="*";}}else{$dinero=$a['dinero'];}
				array_push($din,array('din'=>round($dinero),'niv'=>$a['nivel'],'nick'=>$a['nick'],'c'=>$a['frozen'],'d'=>$a['dist']));
			}
			rsort($din);
			$irc->SendCommand("PRIVMSG $chn :07TOP $num DEL JUEGO:");
			$irc->SendCommand("PRIVMSG $chn :     Nick                Nivel   Dinero");
			
			$i=0;
			foreach($din as $key=>$val){if($val['c']!=2){$i++;}else{continue;}
				time_nanosleep(0,500000000);
				$s="                  "; $s2="           ";
				$s=substr($s,0,(strlen($s)-strlen($val['nick'])));
				if(!($val['niv']>=10)){$s2="       ";}else{$s2="      ";}
				$irc->SendCommand("PRIVMSG $chn :".($i)." -  ".($val["c"]?"05":"").($val["d"]?"":"").$val['nick'].($val["d"]?"":"").($val["c"]?"05":""). $s .$val['niv']."$s2".$val['din']);
				if($i==$num){break;}

			}
			
			mysql_close($myconn);
		}
		
		private function comprar(&$irc,$nick,$chn, $cmd){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			switch($cmd[1]){
				case "noimp":
					if((($rowx["dinero"]>50000000)&&($rowx["nivel"]>=8))||($rowx["dinero"]=="*")){
						$this->user2bank($irc,$nick,false,$myconn);
						$rsx = mysql_query("UPDATE  games_users SET imp='1' WHERE nick='$nick'",$myconn);
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora no pagar·s mas impuestos.");
					}else{ if($rowx["nivel"]<=8){$irc->SendCommand("PRIVMSG $chn :05Error: Debes ser nivel 8 o superior para comprar este articulo!");}else{$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas $50000000 para comprar este articulo!");}mysql_close($myconn); return 0;}
					break;
				case "nimp":
					if(($rowx["dinero"]>10000000)&&($rowx["nivel"]>=5)||($rowx["dinero"]=="*")){
						$this->user2bank($irc,$nick,10000000,false,$myconn);
						$rsx = mysql_query("UPDATE  games_users SET imp='0' WHERE nick='$nick'",$myconn);
						
						$irc->SendCommand("PRIVMSG $chn :$nick: Ahora podr·s pagar impuestos normalmente.");
					}else{ if($rowx["nivel"]<8){$irc->SendCommand("PRIVMSG $chn :05Error: Debes ser nivel 8 o superior para comprar este articulo!");}else{$irc->SendCommand("PRIVMSG $chn :05Error: Necesitas $50000000 para comprar este articulo!");}mysql_close($myconn); return 0;}
					break;
				case "caja":
						if(isset($cmd[2])){
							switch($cmd[2]){ // muuuy repetitivo
								case "1":
									if($rowx["nivel"]<6){$irc->SendPriv($chn,"05Error: Debes ser por lo menos nivel 6 para comprar una caja fuerte nivel 1");return 0;}
									$r=$this->user2bank($irc,$nick,5000000,false,$myconn);
									if($r==-3){$irc->SendPriv($chn,"05Error: Necesitas por lo menos $5000000 para comprar este producto");return 0;}
									$rsx = mysql_query("UPDATE  games_users SET caja='1' WHERE nick='$nick'",$myconn);
									$irc->SendPriv($chn,"Has comprado una caja fuerte nivel 1");
									break;
								case "2":
									if($rowx["nivel"]<6){$irc->SendPriv($chn,"05Error: Debes ser por lo menos nivel 6 para comprar una caja fuerte nivel 2");return 0;}
									$r=$this->user2bank($irc,$nick,50000000,false,$myconn);
									if($r==-3){$irc->SendPriv($chn,"05Error: Necesitas por lo menos $50000000 para comprar este producto");return 0;}
									$rsx = mysql_query("UPDATE  games_users SET caja='2' WHERE nick='$nick'",$myconn);
									$irc->SendPriv($chn,"Has comprado una caja fuerte nivel 2");
									break;
								case "3":
									if($rowx["nivel"]<6){$irc->SendPriv($chn,"05Error: Debes ser por lo menos nivel 6 para comprar una caja fuerte nivel 2");return 0;}
									$r=$this->user2bank($irc,$nick,100000000,false,$myconn);
									if($r==-3){$irc->SendPriv($chn,"05Error: Necesitas por lo menos $100000000 para comprar este producto");return 0;}
									$rsx = mysql_query("UPDATE  games_users SET caja='3' WHERE nick='$nick'",$myconn);
									$irc->SendPriv($chn,"Has comprado una caja fuerte nivel 3");
									break;
								case "4":
									if($rowx["nivel"]<7){$irc->SendPriv($chn,"05Error: Debes ser por lo menos nivel 7 para comprar una caja fuerte nivel 2");return 0;}
									$r=$this->user2bank($irc,$nick,500000000,false,$myconn);
									if($r==-3){$irc->SendPriv($chn,"05Error: Necesitas por lo menos $500000000 para comprar este producto");return 0;}
									$rsx = mysql_query("UPDATE  games_users SET caja='4' WHERE nick='$nick'",$myconn);
									$irc->SendPriv($chn,"Has comprado una caja fuerte nivel 4");
									break;
								case "5":
									if($rowx["nivel"]<8){$irc->SendPriv($chn,"05Error: Debes ser por lo menos nivel 8 para comprar una caja fuerte nivel 2");return 0;}
									$r=$this->user2bank($irc,$nick,500000000,false,$myconn);
									if($r==-3){$irc->SendPriv($chn,"05Error: Necesitas por lo menos $500000000 para comprar este producto");return 0;}
									$rsx = mysql_query("UPDATE  games_users SET caja='5' WHERE nick='$nick'",$myconn);
									$irc->SendPriv($chn,"Has comprado una caja fuerte nivel 5");
									break;
								case "6":
									if($rowx["nivel"]<8){$irc->SendPriv($chn,"05Error: Debes ser por lo menos nivel 8 para comprar una caja fuerte nivel 2");return 0;}
									$r=$this->user2bank($irc,$nick,1000000000,false,$myconn);
									if($r==-3){$irc->SendPriv($chn,"05Error: Necesitas por lo menos $1000000000 para comprar este producto");return 0;}
									$rsx = mysql_query("UPDATE  games_users SET caja='6' WHERE nick='$nick'",$myconn);
									$irc->SendPriv($chn,"Has comprado una caja fuerte nivel 6");
									break;
							}
						}else{$irc->SendPriv($chn,"Sintaxis: !comprar caja <nivel>");}
					break;
				
			}
			mysql_close($myconn);
		}
		
		private function cobre(&$irc,$nick,$chn, $cmd){
			$conn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$conn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_banco",$conn);
			$rowx2=mysql_fetch_array($rsx);

			if(!isset($cmd[1])){$irc->SendPriv($chn,"Sintaxis: !cobre <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
			if(!isset($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !cobre <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
			switch($cmd[1]){
				case "comprar":
					if(!is_numeric($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !cobre <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$cmd[2]=abs($cmd[2]);
					switch($rowx['caja']){
						case 1:$bmax=2;break;
						case 2:$bmax=8;break;
						case 3:$bmax=10;break;
						case 4:$bmax=10;break;
						case 5:$bmax=10;break;
						case 6:$bmax=10;break;
					}
					if($rowx2['cobre']>=$cmd[2]){
						if(($cmd[2]+$rowx['cobre'])<=$bmax){
							if(!$rowx['cobre']){$rowx['cobre']=0;}
							$rsx = mysql_query("UPDATE games_users SET cobre='".($rowx['cobre']+$cmd[2])."' WHERE nick='$nick'",$conn);
							$rsx = mysql_query("UPDATE games_banco SET cobre='".($rowx2['cobre']-$cmd[2])."' WHERE cobre='$rowx2[cobre]'",$conn);
							$i=0;while($i!=$cmd[2]){$i++;$this->user2bank($irc,$nick,500000);}
							$irc->SendPriv($chn, "Has comprado $cmd[2] cobres");
						}else{$irc->SendPriv($chn,"05Error: Con la caja que tienes solo puedes comprar como m·ximo $bmax cobres");return 0;}
					}else{$irc->SendPriv($chn,"05Error: No hay suficiente stock de cobres como para que puedas comprar tantos..");return 0;}
					break;
				case "transferir":
					if(!isset($cmd[3])){$irc->SendPriv($chn,"Sintaxis: !cobre <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$cmd[2]'",$conn);if(mysql_num_rows($rsx)==0){$irc->SendPriv($chn, "05Error: El usuario $cmd[2] no existe");}
					$rowx3=mysql_fetch_array($rsx);
					$cant=abs($cmd[3]);
					switch($rowx3['caja']){
						case 1:$bmax=2;break;
						case 2:$bmax=8;break;
						case 3:$bmax=10;break;
						case 4:$bmax=10;break;
						case 5:$bmax=10;break;
						case 6:$bmax=10;break;
					}
					if(($rowx3['cobre']+$cant)<=$bmax){
						if($rowx['cobre']>=$cant){
							$rsx = mysql_query("UPDATE games_users SET cobre='".($rowx['cobre']-$cant)."' WHERE nick='$nick'",$conn);
							$rsx = mysql_query("UPDATE games_users SET cobre='".($rowx3['cobre']+$cant)."' WHERE cobre='$rowx3[cobre]'",$conn);
							$irc->SendPriv($chn,"Se han transferido $cant cobres a $cmd[2]");
						}else{$irc->SendPriv($chn,"05Error: No tienes suficiente cobre!!");return 0;}
					}else{$irc->SendPriv($chn,"05Error: La caja de $cmd[2] solo soporta hasta $bmax cobres");return 0;}
					
					break; 
				case "vender":
					if(!is_numeric($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !cobre <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$cant=abs($cmd[2]);
					if($rowx['cobre']>=$cant){
						$rsx = mysql_query("UPDATE games_users SET cobre='".($rowx['cobre']-$cant)."' WHERE nick='$nick'",$conn);
						$rsx = mysql_query("UPDATE games_banco SET cobre='".($rowx2['cobre']+$cant)."' WHERE cobre='$rowx2[cobre]'",$conn);
						$i=0;while($i!=$cant){$i++;$this->user2bank($irc,$nick,350000,true);}

						$irc->SendPriv($chn,"Has vendido $cant cobres.");

					}else{$irc->SendPriv($chn,"05Error: No tienes suficiente cobre!!");return 0;}
					break;
				default:
					$irc->SendPriv($chn,"Sintaxis: !cobre <comprar|transferir|vender> [nick] <cantidad>.");return 0;
			}
			mysql_close($conn);
		}
		// y se siigue repitiendo hasta el infinito
		private function plata(&$irc,$nick,$chn, $cmd){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			if(!isset($cmd[1])){$irc->SendPriv($chn,"Sintaxis: !plata <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
			if(!isset($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !plata <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
			switch($cmd[1]){
				case "comprar":
					if(!is_numeric($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !plata <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$cmd[2]=abs($cmd[2]);
					switch($rowx['caja']){
						case 1:$bmax=0;break;
						case 2:$bmax=3;break;
						case 3:$bmax=8;break;
						case 4:$bmax=10;break;
						case 5:$bmax=10;break;
						case 6:$bmax=10;break;
					}
					if($rowx2['plata']>=$cmd[2]){
						if(($cmd[2]+$rowx['plata'])<=$bmax){
							if(!$rowx['plata']){$rowx['plata']=0;}
							$rsx = mysql_query("UPDATE games_users SET plata='".($rowx['plata']+$cmd[2])."' WHERE nick='$nick'",$myconn);
							$rsx = mysql_query("UPDATE games_banco SET plat='".($rowx2['plat']-$cmd[2])."' WHERE plat='$rowx2[plat]'",$myconn);
							$i=0;while($i!=$cmd[2]){$i++;$this->user2bank($irc,$nick,1000000);}

							$irc->SendPriv($chn, "Has comprado $cmd[2] medidas de plata ");
						}else{$irc->SendPriv($chn,"05Error: Con la caja que tienes solo puedes comprar como m·ximo $bmax medidas de plata");return 0;}
					}else{$irc->SendPriv($chn,"05Error: No hay suficiente stock de cobres como para que puedas comprar tantos..");return 0;}
					break;
				case "transferir":
					if(!isset($cmd[3])){$irc->SendPriv($chn,"Sintaxis: !plata <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$cmd[2]'",$myconn);if(mysql_num_rows($rsx)==0){$irc->SendPriv($chn, "05Error: El usuario $cmd[2] no existe");}
					$rowx3=mysql_fetch_array($rsx);
					$cant=abs($cmd[3]);
					switch($rowx3['caja']){
						case 1:$bmax=0;break;
						case 2:$bmax=3;break;
						case 3:$bmax=8;break;
						case 4:$bmax=10;break;
						case 5:$bmax=10;break;
						case 6:$bmax=10;break;
					}
					if(($rowx3['plata']+$cant)<=$bmax){
						if($rowx['plata']>=$cant){
							$rsx = mysql_query("UPDATE games_users SET plata='".($rowx['plata']-$cant)."' WHERE nick='$nick'",$myconn);
							$rsx = mysql_query("UPDATE games_users SET plata='".($rowx3['plata']+$cant)."' WHERE nick='$rowx3[nick]'",$myconn);
							$irc->SendPriv($chn,"Se han transferido $cant medidas de plata a $cmd[2]");
						}else{$irc->SendPriv($chn,"05Error: No tienes suficiente plata!!");return 0;}
					}else{$irc->SendPriv($chn,"05Error: La caja de $cmd[2] solo soporta hasta $bmax medidas de plata");return 0;}
					
					break; 
				case "vender":
					if(!is_numeric($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !plata <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$cant=abs($cmd[2]);
					if($rowx['plata']>=$cant){
						$rsx = mysql_query("UPDATE games_users SET plata='".($rowx['plata']-$cant)."' WHERE nick='$nick'",$myconn);
						$rsx = mysql_query("UPDATE games_banco SET plat='".($rowx2['plat']+$cant)."' WHERE plat='$rowx2[plat]'",$myconn);
						$i=0;while($i!=$cant){$i++;$this->user2bank($irc,$nick,750000,true);}

						$irc->SendPriv($chn,"Has vendido $cant medidas de plata.");

					}else{$irc->SendPriv($chn,"05Error: No tienes suficiente plata!!");return 0;}
					break;
				default:
					$irc->SendPriv($chn,"Sintaxis: !plata <comprar|transferir|vender> [nick] <cantidad>.");return 0;
			}
			mysql_close($myconn);
		}
		
		private function oro(&$irc,$nick,$chn, $cmd){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$nick'",$myconn);
			$rowx=mysql_fetch_array($rsx);
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			if(!isset($cmd[1])){$irc->SendPriv($chn,"Sintaxis: !oro <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
			if(!isset($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !oro <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
			switch($cmd[1]){
				case "comprar":
					if(!is_numeric($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !oro <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$cmd[2]=abs($cmd[2]);
					switch($rowx['caja']){
						case 1:$bmax=0;break;
						case 2:$bmax=0;break;
						case 3:$bmax=0;break;
						case 4:$bmax=3;break;
						case 5:$bmax=8;break;
						case 6:$bmax=10;break;
					}
					if($rowx2['oro']>=$cmd[2]){
						if(($cmd[2]+$rowx['oro'])<=$bmax){
							if(!$rowx['oro']){$rowx['oro']=0;}
							$rsx = mysql_query("UPDATE games_users SET oro='".($rowx['oro']+$cmd[2])."' WHERE nick='$nick'",$myconn);
							$rsx = mysql_query("UPDATE games_banco SET oro='".($rowx2['oro']-$cmd[2])."' WHERE oro='$rowx2[oro]'",$myconn);
							$i=0;while($i!=$cmd[2]){$i++;$this->user2bank($irc,$nick,10000000);}

							$irc->SendPriv($chn, "Has comprado $cmd[2] medidas de oro ");
						}else{$irc->SendPriv($chn,"05Error: Con la caja que tienes solo puedes comprar como m·ximo $bmax medidas de oro");return 0;}
					}else{$irc->SendPriv($chn,"05Error: No hay suficiente stock de oro como para que puedas comprar tantos..");return 0;}
					break;
				case "transferir":
					if(!isset($cmd[3])){$irc->SendPriv($chn,"Sintaxis: !oro <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$cmd[2]'",$myconn);if(mysql_num_rows($rsx)==0){$irc->SendPriv($chn, "05Error: El usuario $cmd[2] no existe");}
					$rowx3=mysql_fetch_array($rsx);
					$cant=abs($cmd[3]);
					switch($rowx3['caja']){
						case 1:$bmax=0;break;
						case 2:$bmax=0;break;
						case 3:$bmax=0;break;
						case 4:$bmax=3;break;
						case 5:$bmax=8;break;
						case 6:$bmax=10;break;
					}
					if(($rowx3['oro']+$cant)<=$bmax){
						if($rowx['oro']>=$cant){
							$rsx = mysql_query("UPDATE games_users SET oro='".($rowx['oro']-$cant)."' WHERE nick='$nick'",$myconn);
							$rsx = mysql_query("UPDATE games_users SET oro='".($rowx3['oro']+$cant)."' WHERE nick='$rowx3[nick]'",$myconn);
							$irc->SendPriv($chn,"Se han transferido $cant medidas de oro a $cmd[2]");
						}else{$irc->SendPriv($chn,"05Error: No tienes suficiente oro!!");return 0;}
					}else{$irc->SendPriv($chn,"05Error: La caja de $cmd[2] solo soporta hasta $bmax medidas de oro");return 0;}
					
					break; 
				case "vender":
					if(!is_numeric($cmd[2])){$irc->SendPriv($chn,"Sintaxis: !oro <comprar|transferir|vender> [nick] <cantidad>.");return 0;}
					$cant=abs($cmd[2]);
					if($rowx['oro']>=$cant){
						$rsx = mysql_query("UPDATE games_users SET oro='".($rowx['plata']-$cant)."' WHERE nick='$nick'",$myconn);
						$rsx = mysql_query("UPDATE games_banco SET oro='".($rowx2['plat']+$cant)."' WHERE oro='$rowx2[oro]'",$myconn);
						$i=0;while($i!=$cant){$i++;$this->user2bank($irc,$nick,7500000,true);}

						$irc->SendPriv($chn,"Has vendido $cant medidas de oro.");

					}else{$irc->SendPriv($chn,"05Error: No tienes suficiente oro!!");return 0;}
					break;
				default:
					$irc->SendPriv($chn,"Sintaxis: !oro <comprar|transferir|vender> [nick] <cantidad>.");return 0;
			}
			mysql_close($myconn);
		}
		
		//Pasa el dinero de un usuario al banco y viceversa si se usan numeros negativos
		// Retorna:
		#	-1 = No se encontro el usuario
		#	-2 = el usuario tiene infinito (no se hace nada)
		#	-3 = el usuario no tiene dinero suficiente
		#	0  = Todo bien
		private function user2bank(&$irc,$usuario,$cantidad,$inv=false,$myconn=false){
			if($myconn==false){$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);mysql_select_db($irc->conf['db']['name']);}

			$rsx = mysql_query("SELECT * FROM games_users WHERE nick='$usuario'",$myconn);if(mysql_num_rows($rsx)==0){mysql_close($myconn);return -1;}
			$rowx=mysql_fetch_array($rsx);
			
			$rsx = mysql_query("SELECT * FROM games_banco",$myconn);
			$rowx2=mysql_fetch_array($rsx);
			
			if($rowx["dinero"]!="*"){
				if(($rowx["dinero"]<$cantidad)&&($inv==false)){mysql_close($myconn);return -3;
				}else{
					if($inv==false){
						if($rowx['dist']==1){$cantidad=$cantidad-($cantidad * 5/100);} //descuento del 5% a algunos privilegiados
						$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]-$cantidad)."' WHERE nick='$usuario'",$myconn);
						$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]+$cantidad)."' WHERE  plata=$rowx2[plata]",$myconn);
					}else{
						$rsx = mysql_query("UPDATE  games_users SET dinero='".($rowx["dinero"]+$cantidad)."' WHERE nick='$usuario'",$myconn);
						$rsx = mysql_query("UPDATE games_banco SET plata ='".($rowx2["plata"]-$cantidad)."' WHERE  plata=$rowx2[plata]",$myconn);
					}
					//mysql_close($myconn);
				}
			}else{return -2;}
			
		}
	}
?>
