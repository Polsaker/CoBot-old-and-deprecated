<?php
/* 
 * Hecho por Ramiro Bou bajo la licencia CC-By-NC-SA
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 */ 
	declare(ticks = 1);
	define("VER", "0.2.2.1");
	date_default_timezone_set('UTC');
	require("config.php");
	if ((! function_exists('pcntl_fork')) && ($conf['threads']['use']==true)) die("[1;31mERROR[0m: Las funciones PCNTL no están disponibles\n\n");


	require("ircbot.class.php");
	if(!isset($conf['threads']['use']) || ($conf['threads']['use']==false)){$pid=-2;}else{$pid = pcntl_fork();}

	switch($pid){
		case -1:
			die("[1;31mERROR[0m: No se ha podido realizar el 'fork'\n\n");
			break;
		case 0: 
			include("childmgr.php");
			break;
		default:


			
			$ircbot=new IRCBot($conf);
			echo " - Iniciando [1;31mCo[1;32mBOT[0m [1mv".VER."[0m Por [4mMr. X[0m -\n\n";
			echo " - Cargando módulos.\n";
			require("modules.conf.php");
			while($ircbot->disconn<=$conf['conn']['reconnect']){
				$ircbot->IRCConnect();
			}
			pcntl_wait($status);
			break;
	}
	
	
?>


