<?php

	define("VER", "1.0 (Olivaw)");
	date_default_timezone_set('UTC');
	
	/* Algun dia estas definiciones irán en otro archivo.. */
	define("CUSTOMPRIV", 5000);
	
	require("config.php");
	
	require("core/lib/idiorm/idiorm.php");
	require("core/lib/SmartIRC/SmartIRC.php");
	require("core/cobot.core.php");
	
	$cobot = new CoBot($conf);
	
	require("modules.php");
	
	$cobot->connect();
