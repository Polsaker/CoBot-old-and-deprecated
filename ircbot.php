<?php
	define("VER", "0.2.0.0.3s");
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


