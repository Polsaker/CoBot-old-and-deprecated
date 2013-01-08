<?php
/* 
 * Hecho por Ramiro Bou bajo la licencia CC-By-NC-SA
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 */ 
	define("VER", "0.2.1");
	date_default_timezone_set('UTC');
	require("config.php");
	require("ircbot.class.php");
		echo " - Iniciando [1;31mCo[1;32mBOT[0m [1mv".VER."[0m Por [4mMr. X[0m -\n\n";
		$ircbot=new IRCBot($conf);
		echo " - Cargando módulos.\n";
		require("modules.conf.php");
	while($ircbot->disconn<=$conf['conn']['reconnect']){
		$ircbot->IRCConnect();
	}
?>


