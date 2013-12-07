<?php

class CoBot{
	public $irc;
	public $conf;
	private $module;
	private $modinfo;
	public $prefix;
	private $commands=array();
	public $dbcon;
	private $help=array(); 
	public $onconnect; // blergh!
	
	private $messagehandlers=array();
	private $timehandlers=array();
	private $messagehandlerscount = 0;
	public function __construct($config){
		$this->conf = $config;
		$this->prefix= preg_quote($this->conf['irc']['prefix']);
		$this->irc = &new Net_SmartIRC();
		$this->irc->setDebug(SMARTIRC_DEBUG_IRCMESSAGES);
		$this->irc->setUseSockets(false);
		$this->irc->setCtcpVersion("CoBot/".VER);
		
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL|SMARTIRC_TYPE_QUERY, '^'."(?:{$this->prefix}|¬NICK¬[:, ]? )(help|ayuda)(?!\w+)", $this, "help");
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^'."(?:{$this->prefix}|¬NICK¬[:, ]? )".'auth(?!\w+)', $this, "auth");
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'."(?:{$this->prefix}|¬NICK¬[:, ]? )".'update(?!\w+)', $this, "update");
		$this->irc->cobot=$this;
				
		ORM::configure($config['ormconfig']);
		
		if(!@$config['core']['keepusercache']){if(file_exists("authinf")){unlink("authinf");}} // Borramos la "cache" de usuarios identificados al iniciar
		
		ini_set("user_agent", "CoBot/".VER);
	}
	
	/*
	 * Carga un módulo
	 * @param $name nombre del módulo (extensión incluida)
	 * @return: 2 = error de formato, -6 = Archivo no encontrado, -2 = El modulo ya estaba
	 * cargado, 3 = Errores de sintaxis, -3 = no se encuentra la clase principal, 5 = todo ok
	 */ 
	public function loadModule($name){
		if(!file_exists("modules/$name")){ return -6;}
		copy("modules/$name","modules/tmp/$name"); 
		$pfile=file_get_contents("modules/tmp/$name");
		if(preg_match("#.*@key: (.+)\n.*#",$pfile,$m)){$key=$m[1];}else{return 2;}
		if(preg_match("#.*@id: (.+)\n.*#",$pfile,$m)){$id=$m[1];}else{return 2;}
		if(preg_match("#.*@author: (.+)\n.*#",$pfile,$m)){$author=$m[1];}
		if(preg_match("#.*@ver: (.+).*#",$pfile,$m)){$ver=$m[1];}
		if(preg_match("#.*@name: (.+)\n.*#",$pfile,$m)){$pname=$m[1];}else{return 2;}
		if(preg_match("#.*@desc: (.+)\n.*#",$pfile,$m)){$desc=$m[1];}
		$ts=time();
		$renclass = $id."x".$ts;
		
		echo "Cargando $name ";
		
		if(@isset($this->module[$id])){echo "[1;31m[ERR][0m El modulo ya está cargado\n"; return -2;}
		
		@$r=shell_exec("php -l modules/$name");
		if(!preg_match("@.*No syntax errors detected.*@",$r)){
			echo "[ERR] El plugin parece tener errores de sintáxis!!\n";
			return 3;
		}
		
		$nmod=preg_replace("/class $key{/","class $renclass{",$pfile);
		
		$fp = fopen("modules/tmp/$name", "w+");
		fputs($fp, $nmod);
		fclose($fp);
		
		include("modules/tmp/$name");
		if(!class_exists($renclass)){echo "[ERR] No encuentro la funcion principal!!\n";return -3;}
		
		$this->module[$id]=new $renclass($this);
		@$this->modinfo[$id]['author'] = $author;
		@$this->modinfo[$id]['ver'] = $ver;
		@$this->modinfo[$id]['desc'] = $desc;
		echo "[OK]\n";
		return 5;
	}
	
	/*
	 * Descarga un módulo
	 * @param $module: Nombre del modulo
	 * @return: -6 = El archivo no existe; 2 = Error de formato;  -2 = El modulo no estaba cargado
	 * 5 = todo ok.
	 */ 
	public function unloadModule($module){
		if(!file_exists("modules/tmp/$module")){return -6;}
		$pfile=file_get_contents("modules/tmp/$module");
		if(preg_match("#.*@id: (.+)\n.*#",$pfile,$m)){$id=$m[1];}else{return 2;}
		if(!isset($this->module[$id])){ return -2; }
		foreach($this->commands as $key => $val){
			if($val['module']==$id){
				$this->irc->unregisterActionid($val['handler']);
				unset($this->commands[$key]);
			} 
		}
		
		foreach($this->help as $key => $val){
			if($val['m']==$id){
				unset($this->help[$key]);
			}
		}
		
		foreach($this->messagehandlers as $key => $val){
			if($val['module']==$id){
				unset($this->messagehandlers[$key]);
			}
		}
		
		foreach($this->timehandlers as $key => $val){
			if($val['module']==$id){
				$this->irc->unregisterTimeid($val['tid']);
				unset($this->timehandlers[$key]);
			}
		}
		
		unset($this->module[$id]);
		unset($this->modinfo[$id]);
		return 5;
	}	
	
	/*
	 * Verifica si un módulo esta cargado
	 * @param: $module: @id del modulo
	 * @return: true si el modulo esta cargado, false si no esta cargado
	 */
	public function isLoaded($module){
		if(@isset($this->module[$module])){return true;}else{return false;}
	}
	
	/*
	 * Retorna la clase principal de un modulo
	 * (ideal para integración entre modulos)
	 * @param: @id del modulo
	 * @return: la clase principal del módulo o false si no está cargado
	 */
	public function getModule($module){
		if($this->isLoaded($module)){
			return $this->module[$module];
		}else{return false;}
	}
	
	/*
	 * Registra un comando con el bot.
	 * @param $name: Nombre del comando
	 * @param $module: Nombre del modulo (@id)
	 * @param $help: Ayuda de la funcion (false = funcion oculta)
	 * @param $perm y $sec: Permisos y seccion de permisos. ($perm = -1, no requiere permisos)
	 * @param $method: La función a la que se llamará al ejecutarse el comando (Por defecto = el mismo nombre que el comando)
	 * @param $type: El tipo de handler que se registrara. Por defecto: SMARTIRC_TYPE_CHANNEL
	 */ 
	public function registerCommand($name, $module, $help = false, $perm = -1, $sec = "*", $method = null, $type=SMARTIRC_TYPE_CHANNEL){
		$ac = $this->irc->registerActionhandler($type, '^'."(?:{$this->prefix}|¬NICK¬[:, ]? )".$name.'(?!\w+)', $this, 'commandHandler');
		if($method!=null){$fmethod=$method;}else{$fmethod=$name;}
		if($help != false){
			array_push($this->help,array('m'=>$module,'name' => $name, 'priv' => $perm, 'sec' => $sec));
		}
		$this->commands[$name] = array(
			'module' => $module,
			'perm' 	 => $perm,
			'sec' 	 => $sec,
			'help' 	 => $help,
			'handler'=> $ac,
			'method' => $fmethod,
			'alias'  => false,
			'ctype'	 => $type
		);
		
	}
	
	/*
	 * registerCommandAlias
	 *  Registra un alias de un comando.
	 * @param: $name: nombre del alias (puede ser un array)
	 * @param: $command: nombre del comando
	 * 
	 */ 
	public function registerCommandAlias($name, $command){
		if(!is_array($name)){$name=array($name);}
		foreach($name as $alias){
			$ac = $this->irc->registerActionhandler($this->commands[$command]['ctype'], '^'."(?:{$this->prefix}|¬NICK¬[:, ]? )".$alias.'(?!\w+)', $this, 'commandHandler');
			$this->commands[$alias] = array(
				'module' => $this->commands[$command]['module'],
				'perm' 	 => $this->commands[$command]['perm'],
				'sec' 	 => $this->commands[$command]['sec'],
				'help' 	 => $this->commands[$command]['help'],
				'handler'=> $ac,
				'method' => $this->commands[$command]['method'],
				'ctype'  => $this->commands[$command]['ctype'],
				'alias'  => true,
				'alicom' => $command
			);
		}
	}
	
	private function rsMsgEx($messageex){
		if(preg_match("#".preg_quote($this->conf['irc']['nick'])."(\:|,)#",$messageex[0])){
			$messageex[0] = $messageex[0]. " " . $messageex[1];
			$i=0;
			foreach($messageex as $key => $val){
				if($i>0){
					if(isset($messageex[$i+1])){
						$messageex[$i] = $messageex[$i+1];
						
					}else{
						unset($messageex[$i]);
					}
				}
			$i++;}
		}
		return $messageex;
	}
	
	# Funcion interna: Verifica privilegios y llama a la función correcta
	public function commandHandler(&$irc, &$data){
		if(preg_match("#".preg_quote($this->conf['irc']['nick'])."(\:|,)#",$data->messageex[0])){
			$command = $data->messageex[1];		
		}else{
			$command = substr($data->messageex[0],1);
		}
		$data->messageex = $this->rsMsgEx($data->messageex);
		if(isset($this->commands[$command])){
			if($this->commands[$command]['perm']!=-1){
				$c=true;
				if($this->commands[$command]['sec']==CUSTOMPRIV){
					$fe = $this->commands[$command]['method']."_priv";
					if( $this->module[$this->commands[$command]['module']]->$fe($irc, $data, $this) == false){$c=false;}
				}else{
					if($this->authchk($data->from, $this->commands[$command]['perm'], $this->commands[$command]['sec'])==false){$c=false;}
				}
				if($c==false){if($this->authchk($data->from, $this->commands[$command]['perm'], $this->commands[$command]['module']) == false){$c=false;}else{$c=true;}}
				if($c==false){$irc->message(SMARTIRC_TYPE_CHANNEL, ($data->channel?$data->channel:$data->nick), "\00304Error\003: No autorizado"); return -5;}
				
			}
			$fu = $this->commands[$command]['method'];
			$this->module[$this->commands[$command]['module']]->$fu($irc, $data, $this);
		}
	}
	
	#Funion interna: Actualizador
	public function update(&$irc, $data){
		$k = json_decode(file_get_contents("https://api.github.com/repos/irc-CoBot/CoBot/git/trees/master"));
		$toupdate = array();
		foreach($k->tree as $key => $val){
			switch($val->path){
				case "cobot.core.php":
					array_push($toupdate, array('path'=>"cobot.core.php",'hash'=>$val->sha,'url'=>$val->url,'l'=>$val->size));
					break;
				case "cobot.php":
					array_push($toupdate, array('path'=>"cobot.php",'hash'=>$val->sha,'url'=>$val->url,'l'=>$val->size));
					break;
				case "modules":
					$w = json_decode(file_get_contents($val->url));
					foreach($w->tree as $k => $v){
						if($v->type == "blob"){
							array_push($toupdate, array('path'=>"modules/".$v->path,'hash'=>$v->sha,'url'=>$v->url,'l'=>$v->size));
						}
					}
					break;
			}
		}
		$p = false;$u=false;
		foreach($toupdate as $val){
			if(!file_exists($val['path'])){
				$k=json_decode(file_get_contents($val['url']));
				file_put_contents($val['path'],base64_decode($k->content));$u=true;
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\002Actualizando \00303{$val['path']}\003\002 \00308[Nuevo]", SMARTIRC_CRITICAL);
			}else{
				$hash1 = sha1("blob {$val['l']}\0".file_get_contents($val['path']));
				if($hash1 != $val['hash']){
					$k=json_decode(file_get_contents($val['url']));
					file_put_contents($val['path'],base64_decode($k->content));$u=true;
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\002Actualizando \00303{$val['path']}\003\002", SMARTIRC_CRITICAL);
					if(($val['path']=="cobot.php")||($val['path']=="cobot.core.php")){$p = true;}
					if(preg_match("#modules/(.+)#",$val['path'],$m)){ $this->unloadModule($m[1]); $this->loadModule($m[1]);}
				} 
			} 
		}
		if($p==true){
			$irc->quit("[UPDATE] Aplicando actualizaciones.");
			exec("php cobot.php &");
			exit;
		}
		if($u==false){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "No hay actulizaciones disponibles.");}
	}
	
	# Ayuda del bot (comando)
	public function help(&$irc, $data){
		if(!$data->channel){$data->channel=$data->nick;}
		$data->messageex = $this->rsMsgEx($data->messageex);
		if((!isset($data->messageex[1])) || ($data->messageex[1]== "")){
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "CoBot v".VER."Comandos empezar con ".$this->conf['irc']['prefix'].". Escriba ".$this->conf['irc']['prefix']."help <comando> para mas información acerca de un comando.");
			$commands="";
			foreach($this->help as $a){
				if($a['priv']!=-1){
					$p=false;
					if($a['sec']==CUSTOMPRIV){ // Suponiendo que los privilegios "personalizados" solo aplican a canales..
						if($this->authchk($data->from, $a['priv'], $data->channel)==false){$p=true;}
					}else{
						if($this->authchk($data->from, $a['priv'], $a['sec'])==false){$p=true;}
					}
					if($p==true){if($this->authchk($data->from, $a['priv'], $this->commands[$a['name']]['module']) == false){$p=true;}else{$p=false;}}
					if($p==true){continue;}
				}
				$commands.="{$a['name']} ";
			}
			//$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Comandos: help auth $commands");
			//$this->sendMessage($data->channel, "Comandos: help auth $commands", true);
			$f=" 06\342\206\231";
			$asg = explode("||||",trim(wordwrap("Comandos: help auth $commands",350, $f."||||",false), $f));
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $asg);
		}else{
			if((isset($this->commands[$data->messageex[1]])) && ($this->commands[$data->messageex[1]]['help'] != "")){
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Ayuda de {$data->messageex[1]}".($this->commands[$data->messageex[1]]['alias']==true?" (Alias de {$this->commands[$data->messageex[1]]['alicom']})":"").": {$this->commands[$data->messageex[1]]['help']}");
			}else{
				switch($data->messageex[1]){
					case "auth":$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Ayuda de \2auth\2: Identifica a un usuario registrado con el bot. Uso: /msg {$irc->_nick} {$this->conf['irc']['prefix']}auth <USUARIO> <CONTRASEÑA>");break;
					case "help":$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Ayuda de \2help\2: Muestra ayuda de un comando o la lista de comandos que el usuario puede ejecutar. Sintaxis: help [comando]");break;
				}
			}
		}
	}
	
	public function message($channel, $message){
		$f="\342\206\231";
		$asg = explode("||||",trim(wordwrap($message,350, "06 ". $f."||||",false), $f));
		$this->irc->message(SMARTIRC_TYPE_CHANNEL, $channel, $asg);
	}
	
	# Autenticación del bot (comando)
	public function auth(&$irc, $data){
		if(isset($data->messageex[2])){
			//$result = $this->dbcon->query("SELECT * FROM 'users' WHERE user='{$data->messageex[1]}' AND pass='".sha1($data->messageex[2])."';")->fetch();
			$user = ORM::for_table('users')->where('username', strtolower($data->messageex[1]))->where('pass', sha1($data->messageex[2]))->find_one();

			if($user!=false){
				if(file_exists("authinf")){$authinf=json_decode(file_get_contents("authinf"));}else{$authinf=array();}
				array_push($authinf, array('h' => $data->from, 'u' => $user->id));
				file_put_contents("authinf",json_encode($authinf));
				$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, 'Autenticado exitosamente');
			}else{
				$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, 'Usuario/Contraseña incorrectos');
			}
		}
	}
	
	
	/*
	 * Registra un messageHandler.
	 * @param $messagecode: Código del mensaje (Ej: "PRIVMSG", "NOTICE", "001", "353")
	 * @param $module: @id del modulo.
	 * @param $method: función a la que se llamará cuando se reciva $messagecode
	 * 
	 * @return ID del handler. Utilizada para eliminarlo.
	 */ 
	public function registerMessageHandler($messagecode, $module, $method){
		$this->messagehandlers[$this->messagehandlerscount] = array(
		'id' => $this->messagehandlerscount,
		'module' => $module,
		'type' => $messagecode,
		'method' => $method
		);
		
		$this->messagehandlerscount++;
		return $this->messagehandlerscount - 1;
	}
	
	#Borra un messageHandler. $id = ID del messagehandler
	public function unregisterMessageHandler($id){
		unset($this->messagehandlers[$id]);
	}
	
	# Método interno para procesar message handlers
	public function messageHandler(&$ircdata, $messagecode){
		foreach($this->messagehandlers as $key => $val){
			if($val['type']==$messagecode){
				$this->module[$val['module']]->$val['method']($this->irc, $ircdata, $this);
			}
		}
	}
	
	/*
	 * Registra un TimeHandler 
	 * @param: $miliseconds = Intervalo en milisegundos
	 * @param: $module = @id del modulo
	 * @param: $method = Función que se llamara en $module
	 */
	public function registerTimeHandler($miliseconds, $module, $method){
		$tid = $this->irc->registerTimeHandler($miliseconds, $this->module[$module], $method);
		array_push($this->timehandlers, array('module'=>$module, 'tid' => $tid));
	}
	
	/*
	 * des-registra un TimeHandler 
	 * @param: $id = ID del timehandler
	 */
	public function unregisterTimeHandler($id){
		foreach($this->timehandlers as $key => $val){
			if($val['tid']==$id){
				$this->irc->unregisterTimeid($val['tid']);
				unset($this->timehandlers[$key]);
			}
		}
	}
	
	/*
	 * Junta los valores de un array en una sola cadena.
	 * Util para unir parametros
	 * 
	 * @param $param: Array
	 * @param $from: Desde que parte del array se comenzara a concatenar
	 * 
	 */ 
	public function jparam($param,$from){
		$i=$from;
		$ts="";
		while(@isset($param[$i])){
			$ts.=$param[$i]. " ";
			$i++;
		}
		return trim($ts);
	}

	
	/*
	 * Función para verifica si un usuario se ha identificado con el bot
	 * @param $host: Máscara del usuario ($data->from)
	 * @param $perm: Privilegios a comprobar
	 * @param $permsec: Sección de permisos a verificar (Opcional, si es false se
	 *  verificara por permisos globales.
	 * 
	 * @return: True si el usuario esta identificado y cumple con los privilegios requeridos
	 */ 
	public function authchk($host, $perm, $permsec=false){
		if(!file_exists("authinf")){ return false;}else{$authinf=json_decode(file_get_contents("authinf"));}
		$permsec=strtolower($permsec);
		foreach($authinf as $key => $val){
			if($val->h==$host){
				//$user = ORM::for_table('users')->where('id', $val['u'])->find_one();
				$userpriv = ORM::for_table('userpriv')->where('uid', $val->u)->find_many();
				foreach($userpriv as $priv){
					if($priv==false){break;}
					if(($priv->sec == "*") && ($priv->rng >= $perm)){
						return true;
					}elseif((strtolower($priv->sec) == $permsec) && ($priv->rng >= $perm)){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/* @deprecated
	 * sendMessage
	 *  Envía un mensaje a un canal, con la opción de partirlo, para mensajes largos
	 * @param $chan = Canal a donde el mensaje se enviará
	 * @param $msg = Mensaje que será eniado
	 * @param $arrow = Si se partirá el mensaje (opcional)
	 * @param $len = Cada cuantos caracteres se partirá el mensaje
	 * @param $sep = Que separador se utilizara para basarse al partir el mensaje (por defecto lo parte cuando encuentra un espacio)
	 */ 
	public function sendMessage($chan,$msg,$arrow=false,$len=400, $sep=" "){
		$send="";
		if($arrow==true){
			$a=explode($sep, $msg);
			foreach($a as $key=>$val){
				if(strlen($send)+strlen($val)>=$len){
					time_nanosleep(0,250000000); 
					$this->irc->message(SMARTIRC_TYPE_CHANNEL, $chan, $send." 06\342\206\231");
					$send="";
				}else{$send.=$val.$sep;}
			}
			$send=trim($send,$sep);
			
		}else{$send=$msg;}
		$this->irc->message(SMARTIRC_TYPE_CHANNEL,$chan,$send);
	}
	
	# Funcion para conectarse al irc.
	public function connect(){
		if($this->conf['irc']['ssl']==true){$this->conf['irc']['host']="ssl://".$this->conf['irc']['host'];}
		$this->irc->connect($this->conf['irc']['host'], $this->conf['irc']['port']);
		$this->irc->send($this->onconnect, SMARTIRC_CRITICAL);
		$this->irc->login($this->conf['irc']['nick'], 'CoBot/'.VER.'', 0, $this->conf['irc']['nick']);
		$this->irc->join($this->conf['irc']['channels']);
		$this->irc->listen();
		$this->irc->disconnect();


	}
}
