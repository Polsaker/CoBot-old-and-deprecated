<?php

	define("VER", "1.0 (Olivaw)");
	date_default_timezone_set('UTC');
	
	/* Algun dia estas definiciones irán en otro archivo.. */
	define("CUSTOMPRIV", 5000);
	
	require("config.php");
	require("lib/idiorm/idiorm.php");
	require("lib/SmartIRC/SmartIRC.php");
	require("cobot.core.php");
	
	$cobot = new CoBot($conf);
	
	require("modules.php");
	try{
		$cobot->connect();
	}catch(Exception $e){
		$cobot->irc->send("QUIT :CALL NINE ONE ONE, PHP Fatal Error D:", SMARTIRC_CRITICAL);
		sleep(1); // >:D
		exec("php cobot.php > log/cobot.log &");
	}
