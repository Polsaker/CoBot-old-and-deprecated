<?php
/* 
 * Hecho por Ramiro Bou bajo la licencia CC-By-NC-SA
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 */ 

class IRCBot{
	public $conf;
	public $serv;
	public $nick;
	public $kvar;
	public $hdf/*=array()*/;
	public $initscript=array();
	public $connscript=array();
	public $joinscript=array();
	private $users;
	private $authd;
	private $plugins;
	private $pcomms;private $pcdc=-1;
	private $iautdc=-1;
	private $chanlst=array();
	public $disconn=0;
	public function __construct($config){
		$this->conf=$config;
		echo "  - Resolviendo '". $this->conf['irc']['host'] ."'";
		$this->serv['ip']=gethostbynamel($this->conf['irc']['host']);
		$this->nick=$this->conf['irc']['nick'];
		
		if($this->serv['ip']==false){die(" [ERR]\n");}else{ echo " [OK] ".$this->serv['ip'][0]."\n";}
		
		$this->serv['myconn']=mysql_connect($this->conf['db']['host'],$this->conf['db']['user'],$this->conf['db']['pass'])or die (exit(mysql_error()));
		mysql_select_db($this->conf['db']['name']);
		$sqlx="select * from users";
		$rsx = mysql_query($sqlx) or die(exit("  - ERROR: verifique que las tablas mysql esten creadas."));
		mysql_close($this->serv['myconn']);

		ini_set('user_agent', 'CoBOT IRC BOT/'.VER);
		
	}
	public function SendCommand($command){
		$command=$command."\r\n";
		$searchutf = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±");
		$replaceutf = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ");
		$command= str_replace($searchutf, $replaceutf, $command);
		fwrite($this->serv['socket'], $command, strlen($command));
	}
	private function remChan($chan){
		foreach ($this->chanlst as $key => $this_channel){
			if($this_channel == $chan){
				unset($this->chanlst[$key]);
			}
		}
		
	}
	public function load($plugin){
		copy("plugins/$plugin","plugins/temp/$plugin"); 
		$fp = fopen("plugins/temp/$plugin", "r");
		$pfile="";
		while(!feof($fp)){$pfile.= fgets($fp);}
		if(preg_match("@.*key=\"(.+)\";.*@",$pfile,$m)){$fkey=$m[1];}else{return -1;}
		if(preg_match("@.*name=\"(.+)\";.*@",$pfile,$m)){$mname=trim($m[1]);}else{return -1;}
		$ts=time();
		
		$nclassname=$mname."x".$ts;
		echo "  - Cargando plugin ". $mname. " ";
		if(@isset($this->plugins[$name])){ echo "[ERR] El plugin ya está cargado\n"; return -2;}
		$nmod=preg_replace("/class $fkey{/","class $nclassname{",$pfile);
		fclose($fp);
		
		$fp = fopen("plugins/temp/$plugin", "w+");
		fputs($fp, $nmod);
		fclose($fp);
		include("plugins/temp/$plugin");
		if(!class_exists($nclassname)){echo "[ERR] No encuentro la funcion principal!!\n"; return -3;}
		$this->plugins[$mname]=new $nclassname($this);
		echo "[OK]\n";
		return 2;
	}
	public function unload($plugin){
		$fp = fopen("plugins/temp/$plugin", "r");
		$pfile="";
		if(!$fp){ echo "[ERR] El plugin no parece estar cargado o no esta en /temp\n"; return -2;}
		while(!feof($fp)){$pfile.= fgets($fp);}
		if(preg_match("@.*name=\"(.+)\";.*@",$pfile,$m)){$name=trim($m[1]);}else{return -1;} //obtenemos el nombre del modulo.
		fclose($fp); 
		//y ahora vamos a des-cargar sus funciones.
		$i=0;
		foreach($this->pcomms as $key=>$val){
			if($this->pcomms[$key]['pgin']==$name){
				unset($this->pcomms[$key]);
			}
			$i++;
		}
		foreach($this->hdf as $c=>$v){
			foreach($this->hdf[$c] as $a=>$k){
				if($this->hdf[$c][$a][0]==$name){ unset($this->hdf[$c]); break;}
			}
		}
		echo "  - Des-cargando plugin ". $name. " ";
		if(!@isset($this->plugins[$name])){ echo "[ERR] El plugin no parece estar cargado\n"; return -2;}
		unset($this->plugins[$name]); // y descargamos el objeto
		echo "[OK]\n";
	}
	
	public function addcmd($oplugin,$command,$plugin,$alias=array()){
		$this->pcdc=$this->pcdc+1;
		$this->pcomms[$this->pcdc]['pgin']=$plugin;
		$this->pcomms[$this->pcdc]['comm']=$command;
		$this->pcomms[$this->pcdc]['ali']=0;
		$i=0;
		while(@$alias[$i]){
			$this->pcdc=$this->pcdc+1;
			$this->pcomms[$this->pcdc]['pgin']=$plugin;
			$this->pcomms[$this->pcdc]['comm']=$alias[$i];
			$this->pcomms[$this->pcdc]['alifn']=$command;
			$this->pcomms[$this->pcdc]['ali']=1;
			$i++;
		}
	}


	private function addChan($chan){
		array_push($this->chanlst, $chan);
	}
	
	private function helpsys($commands, $channel,$who){
		if(@!$commands[1]){
			$clist = "help auth ignore"; //agregamos los comandos nativos...
			//y agregamos todos los comandos cargados via plugin a la lista...
			$i=0;
			while(!($this->pcdc<$i)){
				if(@$this->pcomms[$i]['comm']){
					if($this->pcomms[$i]['ali']!=1){
						if(@isset($this->plugins[$this->pcomms[$i]['pgin']]->help[$this->pcomms[$i]['comm'].'_l'])){
							if($this->checkauth($who,$this->plugins[$this->pcomms[$i]['pgin']]->help[$this->pcomms[$i]['comm'].'_l'])==1){
								$clist.=" ".$this->pcomms[$i]['comm'];
							}
						}else{$clist.=" ".$this->pcomms[$i]['comm'];}
					}
				}
				$i++;
			}
			$this->SendCommand("PRIVMSG ".$channel." :03Co04BOT v".VER." Por Mr. X Comandos empezar con ".$this->conf['irc']['prefix'].". Escriba ".$this->conf['irc']['prefix']."help <comando> para mas información acerca de un comando.");
			$this->SendCommand("PRIVMSG ".$channel." :Lista de comandos: ".$clist);
		}else{
			switch($commands[1]){
				case "help":
					$this->SendCommand("PRIVMSG ".$channel." :help: Proporciona ayuda sobre un comando.");
					break;
				case "auth":
					$this->SendCommand("PRIVMSG ".$channel." :auth: Permite identificarse y usar ciertos comandos especiales. Sintaxis: auth <usuario> <contraseña>");
					break;
				case "ignore":
					$this->SendCommand("PRIVMSG ".$channel." :ignore: Ignora a un usuario. Sintaxis: ignore <mascara (regex)>");
					break;
			}
			$i=0;
			while(!($this->pcdc<$i)){
				if($this->pcomms[$i]['comm']==$commands[1]){
					if($this->pcomms[$i]['ali']==1){
						$this->SendCommand("PRIVMSG ".$channel." :".$command[1]."Alias de ".$this->pcomms[$i]['alifn'].". ".$this->plugins[$this->pcomms[$i]['pgin']]->help[$this->pcomms[$i]['alifn']]);
					}else{$this->SendCommand("PRIVMSG ".$channel." :".$commands[1].": ".$this->plugins[$this->pcomms[$i]['pgin']]->help[$commands[1]]);}
				}
				$i++;
			}
		}
	}
	
	public function checkauth ($uhost,$lvlr, $ch = "*"){
		$i=0;
		while(@$this->authd[$i]['hst']){
		//	echo $this->authd[$i]['hst'] . "  " .$uhost;
			if($this->authd[$i]['hst']==$uhost){
				/*if((is_numeric($this->authd[$i]['rng'])) && ($this->authd[$i]['rng']>=$lvlr)){
					return 1;
				}elseif($this->authd[$i]['rng']==("o".$lvlr)){
					return 1;
				}*/
				
				$pr2=explode("|",$this->authd[$i]['rng']);
				$pr4=array();
				foreach($pr2 as $key=>$val){
					$pr3=explode(",",$val);
					array_push($pr4,$pr3);
				}
				$w=0;
				foreach($pr4 as $key=>$val){
					if(($ch==$pr4[$key][1]) || ($pr4[$key][1]=="*")){$w++;}
					if((is_numeric($pr4[$key][0])) && ($pr4[$key][0]>=$lvlr)){$w++;}elseif($pr4[$key][0]==("o".$lvlr)){$w++;}
					if($w==2){return 1;}	
					$w=0;			
				}
				
			}
			$i++;
		}
		return 0;
	}
	
	public function mask2nick($mask){
		$nick=explode("!",$mask);
		return $nick[0];
	}
	// Ten cuidado!! esto está atado con alambres!
	private function procom($msg, $guy, $channel){
		$param=explode(" ", substr ($msg, 1));
		$nk=explode("!",$guy);

		if($msg[0]==$this->conf['irc']['prefix']){
			switch($param[0]){
				case "help":
					$this->helpsys($param,$channel,$guy);
					break;
				case "auth":
					if($channel==$this->nick){
						$this->serv['myconn']=mysql_connect($this->conf['db']['host'],$this->conf['db']['user'],$this->conf['db']['pass']);
						mysql_select_db($this->conf['db']['name']);
						$sqlx="select * from users where user='".$param[1]."' AND pass=sha1('".$param[2]."')";
						$rsx = mysql_query($sqlx) or die(exit("  - ERROR: verifique que las tablas mysql esten creadas."));
						$i=0;
						while($rowx=mysql_fetch_array($rsx)){$i++;
							$this->SendCommand("PRIVMSG ".$nk[0]." :Autenticado exitosamente.");
							$this->iautdc=$this->iautdc+1;
							$this->authd[$this->iautdc]['rng']=$rowx['rng'];
							$this->authd[$this->iautdc]['hst']=$guy;
							break;
						}
						mysql_close($this->serv['myconn']);

						if($i==0){$this->SendCommand("PRIVMSG ".$nk[0]." :04ERROR: Usuario/Contraseña incorrectos.");}
					}else{$this->SendCommand("PRIVMSG ".$channel." :04ERROR: Este comando no debe ser utilizado en un canal.");}
					break;
				case "ignore":
					if($this->checkauth($guy,8)){
						if(!@$param[1]){break;}
						$this->serv['myconn']=mysql_connect($this->conf['db']['host'],$this->conf['db']['user'],$this->conf['db']['pass']);
						mysql_select_db($this->conf['db']['name']);
						$sqlx="INSERT INTO `ignore` (host) VALUES ('$param[1]')";
						$rsx = mysql_query($sqlx);
					}else{	$this->SendCommand("PRIVMSG ".$channel." :04ERROR: No autorizado");}
					break;
			}
			$i=0;
			while(!($this->pcdc<$i)){
				if(@$this->pcomms[$i]['comm']==$param[0]){
					if($this->pcomms[$i]['ali']!=1){$fn=$this->pcomms[$i]['comm'];}else{$fn=$this->pcomms[$i]['alifn'];}
					$this->plugins[$this->pcomms[$i]['pgin']]->$fn($this,$msg,strtolower($channel),$param,$guy);
				}
				$i++;
			}
		}
	}
	
	public function IRCConnect(){
		echo "  - Conectando a '". $this->serv['ip'][0].":".$this->conf['irc']['port']."'";
		$this->serv['socket']=fsockopen(($this->conf['irc']['ssl']?"ssl://":"").$this->serv['ip'][0], $this->conf['irc']['port'], $errno, $errstr, 20);		
		if($this->serv['socket']){
			echo " [OK] \r\n";
			socket_set_blocking($this->serv['socket'], false);
			$i=0;
			$this->SendCommand("NICK " . $this->nick);
			$this->SendCommand("USER " . $this->nick. " * * :CoBOT, IRC Bot");
			while(@$this->initscript[$i]){
				@$this->SendCommand($this->initscript[$i]);
				$i++;
			}
			while(!@feof($this->serv['socket'])){
				$this->serv['rbuffer'] = mb_convert_encoding(fgets($this->serv['socket'], 1024),"utf8"); 
				echo $this->serv['rbuffer'];
				if(empty($this->serv['rbuffer'])){sleep(1);continue;}	
				preg_match('@^(?:\:.*? )?(.*?) @', $this->serv['rbuffer'], $coi);
				@$this->serv['command'] = $coi[1];
				switch($this->serv['command']){
					case "001":
						$i=0;
						while(@$this->connscript[$i]){
							time_nanosleep(0,250000000); //anti-excess flood
							@$this->SendCommand($this->connscript[$i]);
							$i++;
						}
						if($this->conf['irc']['nspass']){$this->SendCommand("PRIVMSG NickServ :IDENTIFY ".$this->conf['irc']['nspass']);}
						$i=0;
						while(@$this->conf['irc']['channels'][$i]){
							$this->SendCommand("JOIN ".$this->conf['irc']['channels'][$i]);$i++;}
						break;
					case "PING":
						$this->SendCommand('PONG :' . substr($this->serv['rbuffer'], 6)); 
						break;
					case "JOIN":
						if (preg_match('@^:'.preg_quote($this->nick, '@').'!.+ JOIN (.+)$@',$this->serv['rbuffer'], $matches)){
							$this->addChan($matches[1]);
							$i=0;
							while(@$this->joinscript[$i]){
								time_nanosleep(0,250000000);
								$joinscript=str_replace("&c",substr($matches[1],1,strlen($matches[1])-1),$this->joinscript[$i]);
								@$this->SendCommand($joinscript);
								$i++;
							}
						}
						break;
					case "PART":
						if (preg_match('@^:'.preg_quote($this->nick, '@').'!.+ PART (.+)$@',$this->serv['rbuffer'], $matches)){	$this->remChan($matches[1]);}
						break;
					case "KICK":
						if (preg_match('@^:.+!.+ KICK (.+)$@',$this->serv['rbuffer'], $matches)){$this->SendCommand("JOIN ".$matches[1]);}
						break;
					case "PRIVMSG":
						
						
						$msg = explode('PRIVMSG ',$this->serv['rbuffer'],2);
						preg_match('/:(.*)/',$msg[0],$matches);
						$who = $matches[1];
						list($channel,$msg) = explode(' :',$msg[1],2);
						$msg=substr($msg,0,strlen($msg)-2);
						
						$this->serv['myconn']=mysql_connect($this->conf['db']['host'],$this->conf['db']['user'],$this->conf['db']['pass']);
						mysql_select_db($this->conf['db']['name']);
						$sqlx="SELECT * FROM `ignore`";
						$rsx = mysql_query($sqlx);
						$k=0;
					//	if($rsx){
							while(@$rowx=mysql_fetch_array($rsx)){
								if(preg_match("#".$rowx['host']."#",$who,$m)){
									$k=1;
									break;
								}
							}
							
						//}
						
						if($k!=1){
						$this->procom($msg,$who,$channel);}
						break;
					case "433":	$this->nick.="_";return 0;	break; // por si el nick ya está en uso
				}
				
				$this->serv['myconn']=mysql_connect($this->conf['db']['host'],$this->conf['db']['user'],$this->conf['db']['pass']);
						mysql_select_db($this->conf['db']['name']);
						$sqlx="SELECT * FROM `ignore`";
						$rsx = mysql_query($sqlx);
						$k=0;
					//	if($rsx){
							while(@$rowx=mysql_fetch_array($rsx)){
								if(preg_match("#.*".$rowx['host'].".*#",$this->serv['rbuffer'],$m)){
									$k=1;
									break;
								}
							}
							echo "---------------------------------$k";
				if($k!=1){
					if(@isset($this->hdf[$this->serv['command']])){
						$i=0;
						while(@$this->hdf[$this->serv['command']][$i]){
							$fn=$this->hdf[$this->serv['command']][$i][1];
							$this->plugins[$this->hdf[$this->serv['command']][$i][0]]->$fn($this,$this->serv['rbuffer']);
							$i++;
						}
					}
				}
			}
		}else{echo" [ERR]\r\n";$this->disconn++;}
	}
	
	
}
