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
	
	private $messagehandlers=array();
	private $messagehandlerscount = 0;
	public function __construct($config){
		$this->conf = $config;
		$this->prefix= preg_quote($this->conf['irc']['prefix']);
		$this->irc = &new Net_SmartIRC();
		$this->irc->setDebug(SMARTIRC_DEBUG_ALL);
		$this->irc->setUseSockets(TRUE);
		
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'."(?:{$this->prefix}|".preg_quote($this->conf['irc']['nick'])."[:,] )(help|ayuda)(?!\w+)", $this, "help");
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^'."(?:{$this->prefix}|".preg_quote($this->conf['irc']['nick'])."[:,] )".'auth(?!\w+)', $this, "auth");
		$this->irc->cobot=$this;
				
		ORM::configure($config['ormconfig']);
		
		if(file_exists("authinf")){unlink("authinf");} // Borramos la "cache" de usuarios identificados al iniciar
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
		$fp = fopen("modules/tmp/$name", "r");
		$pfile="";$i=0;
		while((!feof($fp)) && ($i <= 15)){$pfile.= fgets($fp);}
		if(preg_match("#.*@key: (.+)\n.*#",$pfile,$m)){$key=$m[1];}else{return 2;}
		if(preg_match("#.*@id: (.+)\n.*#",$pfile,$m)){$id=$m[1];}else{return 2;}
		if(preg_match("#.*@author: (.+)\n.*#",$pfile,$m)){$author=$m[1];}else{return 2;}
		if(preg_match("#.*@ver: (.+).*#",$pfile,$m)){$ver=$m[1];}else{}
		if(preg_match("#.*@name: (.+)\n.*#",$pfile,$m)){$pname=$m[1];}else{return 2;}
		if(preg_match("#.*@desc: (.+)\n.*#",$pfile,$m)){$desc=$m[1];}else{return 2;}
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
		fclose($fp);
		
		$fp = fopen("modules/tmp/$name", "w+");
		fputs($fp, $nmod);
		fclose($fp);
		
		include("modules/tmp/$name");
		if(!class_exists($renclass)){echo "[ERR] No encuentro la funcion principal!!\n";return -3;}
		
		$this->module[$id]=new $renclass($this);
		$this->modinfo[$id]['author'] = $author;
		$this->modinfo[$id]['ver'] = $ver;
		$this->modinfo[$id]['desc'] = $desc;
		echo "[OK]\n";
		return 5;
	}
	
	/*
	 * Descarga un módulo
	 * @param $module: Nombre del modulo
	 */ 
	public function unloadModule($module){
		
		$fp = fopen("modules/tmp/$module", "r");
		$pfile="";$i=0;
		while((!feof($fp)) && ($i <= 15)){$pfile.= fgets($fp);}
		if(preg_match("#.*@id: (.+)\n.*#",$pfile,$m)){$id=$m[1];}else{return 2;}
		
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
		$ac = $this->irc->registerActionhandler($type, '^'."(?:{$this->prefix}|".preg_quote($this->conf['irc']['nick'])."[:,] )".$name.'(?!\w+)', $this, 'commandHandler');
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
			'method' => $fmethod
		);
		
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
		print_r($data->messageex);
		if(isset($this->commands[$command])){
			if($this->commands[$command]['perm']!=-1){
				if($this->authchk($data->from, $this->commands[$command]['perm'], $this->commands[$command]['sec'])==false){
					return -5;
				}
			}
			$fu = $this->commands[$command]['method'];
			$this->module[$this->commands[$command]['module']]->$fu($irc, $data, $this);
		}
	}
	
	# Ayuda del bot (comando)
	public function help(&$irc, $data){
		$data->messageex = $this->rsMsgEx($data->messageex);
		if((!isset($data->messageex[1])) || ($data->messageex[1]== "")){
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "03Co04BOT v".VER." Por Mr. X Comandos empezar con ".$this->conf['irc']['prefix'].". Escriba ".$this->conf['irc']['prefix']."help <comando> para mas información acerca de un comando.");
			$commands="";
			foreach($this->help as $a){
				if($a['priv']!=-1){
					if($this->authchk($data->from, $a['priv'], $a['sec'])==false){
						continue;
					}
				}
				$commands.="{$a['name']} ";
				
			}
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Comandos: help auth $commands");
		}else{
			if((isset($this->commands[$data->messageex[1]])) && ($this->commands[$data->messageex[1]]['help'] != "")){
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Ayuda de {$data->messageex[1]}: {$this->commands[$data->messageex[1]]['help']}");
			}
		}
	}
	
	# Autenticación del bot (comando)
	public function auth(&$irc, $data){
		if(isset($data->messageex[2])){
			//$result = $this->dbcon->query("SELECT * FROM 'users' WHERE user='{$data->messageex[1]}' AND pass='".sha1($data->messageex[2])."';")->fetch();
			$user = ORM::for_table('users')->where('username', $data->messageex[1])->where('pass', sha1($data->messageex[2]))->find_one();

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
		foreach($authinf as $key => $val){
			if($val->h==$host){
				//$user = ORM::for_table('users')->where('id', $val['u'])->find_one();
				$userpriv = ORM::for_table('userpriv')->where('uid', $val->u)->find_one();
				if($userpriv==false){continue;}
				if(($userpriv->sec == "*") && ($userpriv->rng >= $perm)){
					return true;
				}elseif(($userpriv->sec == $permsec) && ($userpriv->rng >= $perm)){
					return true;
				}
			}
		}
		return false;
	}
	
	# Funcion para conectarse al irc.
	public function connect(){
		$this->irc->connect($this->conf['irc']['host'], $this->conf['irc']['port']);
		$this->irc->login($this->conf['irc']['nick'], 'CoBot/'.VER.'', 0, $this->conf['irc']['nick']);
		$this->irc->join($this->conf['irc']['channels']);
		$this->irc->listen();
		$this->irc->disconnect();


	}
}
