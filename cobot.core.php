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
	public function __construct($config){
		$this->conf = $config;
		$this->prefix= preg_quote($this->conf['irc']['prefix']);
		$this->irc = &new Net_SmartIRC();
		$this->irc->setDebug(SMARTIRC_DEBUG_ALL);
		$this->irc->setUseSockets(TRUE);
		
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'.$this->prefix.'help', $this, "help");
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^'.$this->prefix.'auth', $this, "auth");
		
		ORM::configure($config['ormconfig']);
		
		if(file_exists("authinf")){unlink("authinf");} // Borramos la "cache" de usuarios identificados al iniciar
	}
	
	/*
	 * Carga un módulo
	 * @param $name nombre del módulo (extensión incluida)
	 */ 
	public function loadModule($name){
		# TODO: Admitir carga-descarga de modulos, como antes...
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
		
		if(@isset($this->module[$id])){echo "[1;31m[ERR][0m El plugin ya está cargado\n"; return -2;}
		
		@$r=shell_exec("php -l modules/$name");
		if(!preg_match("@.*No syntax errors detected.*@",$r)){
			echo "[1;31m[ERR][0m El plugin parece tener errores de sintáxis!!\n";
			return 3;
		}
		
		$nmod=preg_replace("/class $key{/","class $renclass{",$pfile);
		fclose($fp);
		
		$fp = fopen("modules/tmp/$name", "w+");
		fputs($fp, $nmod);
		fclose($fp);
		
		include("modules/tmp/$name");
		if(!class_exists($renclass)){echo "[1;31m[ERR][0m No encuentro la funcion principal!!\n";return -3;}
		
		$this->module[$id]=new $renclass($this);
		$this->modinfo[$id]['author'] = $author;
		$this->modinfo[$id]['ver'] = $ver;
		$this->modinfo[$id]['desc'] = $desc;
		echo "[1;32m[OK][0m\n";
		return 2;
	}
	
	
	/*
	 * Registra un comando con el bot.
	 * @param $name: Nombre del comando
	 * @param $module: Nombre del modulo (@id)
	 * @param $help: Ayuda de la funcion (false = funcion oculta)
	 * @param $perm y $sec: Permisos y seccion de permisos. ($perm = -1, no requiere permisos)
	 */ 
	public function registerCommand($name, $module, $help = false, $perm = -1, $sec = "*"){
		$ac = $this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'.$this->prefix.$nameIASY.'(?!\w+)', $this, 'commandHandler');
		
		if($help != false){
			array_push($this->help,array('name' => $name, 'priv' => $perm, 'sec' => $sec));
		}
		$this->commands[$name] = array(
			'module' => $module,
			'perm' 	 => $perm,
			'sec' 	 => $sec,
			'help' 	 => $help,
			'handler'=> $ac
		);
		
	}
	
	public function commandHandler(&$irc, &$data){
		print_r($data);
		$command = substr($data->messageex[0],1);
		if(isset($this->commands[$command])){
			if($this->commands[$command]['perm']!=-1){
				if($this->authchk($data->from, $this->commands[$command]['perm'], $this->commands[$command]['sec'])==false){
					return -5;
				}
			}
			$this->module[$this->commands[$command]['module']]->$command(&$irc, $data, &$this);
		}
	}
	
	# Ayuda del bot (comando)
	public function help(&$irc, $data){
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
		print_r($authinf);
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
