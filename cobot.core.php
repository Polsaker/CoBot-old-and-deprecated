<?php

class CoBot{
	public $irc;
	public $conf;
	private $module;
	private $modinfo;
	public function __construct($config){
		$this->conf = $config;
		$this->irc = &new Net_SmartIRC();
		$this->irc->setDebug(SMARTIRC_DEBUG_ALL);
		$this->irc->setUseSockets(TRUE);

	}
	
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
	
	public function registerCommand($name, $module){
		echo '^'.$this->conf['irc']['prefix'].$name;
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^'.$this->conf['irc']['prefix'].$name, $this->module[$name], $name);
	}
	
	public function connect(){
		$this->irc->connect($this->conf['irc']['host'], $this->conf['irc']['port']);
		$this->irc->login($this->conf['irc']['nick'], 'CoBot/'.VER.'', 0, $this->conf['irc']['nick']);
		$this->irc->join($this->conf['irc']['channels']);
		$this->irc->listen();
		$this->irc->disconnect();


	}
}
