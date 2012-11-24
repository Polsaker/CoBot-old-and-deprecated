<?php
/* 
 * Hecho por Ramiro Bou bajo la licencia CC-By-NC-SA
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 */ 
	define("VER", "0.2.0.0.3");
	date_default_timezone_set('UTC');
	require("config.php");
	require("ircbot.class.php");
		echo " - Iniciando CoBOT v".VER." Por Mr. X -\n\n";
		$ircbot=new IRCBot($conf);
		echo " - Cargando módulos.\n";
		require("modules.conf.php");
	while($ircbot->disconn<=$conf['conn']['reconnect']){
		$ircbot->IRCConnect();
	}
?>


