<?php

class CoBot{
	public $irc;
	public $conf;
	private $module;
	private $modinfo;
	public $prefix;
	private $commands=array();
	public $dbcon;
	public function __construct($config){
		$this->conf = $config;
		$this->prefix= preg_quote($this->conf['irc']['prefix']);
		$this->irc = &new Net_SmartIRC();
		$this->irc->setDebug(SMARTIRC_DEBUG_ALL);
		$this->irc->setUseSockets(TRUE);
		
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'.$this->prefix.'help', $this, "help");
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^'.$this->prefix.'auth', $this, "auth");
		
		$this->dbcon = new SQLiteDatabase('db/cobot.db');
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
	 */ 
	public function registerCommand($name, $module){
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'.$this->prefix.$name, $this->module[$module], $name, $module, $name);
		array_push($this->commands,array('module' => $module, 'name' => $name));
		
	}
	
	# Ayuda del bot (comando)
	public function help(&$irc, $data){
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "03Co04BOT v".VER." Por Mr. X Comandos empezar con ".$this->conf['irc']['prefix'].". Escriba ".$this->conf['irc']['prefix']."help <comando> para mas información acerca de un comando.");
		$commands="";
		foreach($this->commands as $a){
			$commands.="{$a['name']} ";
		}
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Comandos: help auth $commands");
	}
	
	# Autenticación del bot (comando)
	public function auth(&$irc, $data){
		if(isset($data->messageex[2])){
			$result = $this->dbcon->query("SELECT * FROM 'users' WHERE user='{$data->messageex[1]}' AND pass='".sha1($data->messageex[2])."';")->fetch();
			if(isset($result['id'])){
				echo "Login OK";
			}else{
				echo "Fallo el login";
			}
		}
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
