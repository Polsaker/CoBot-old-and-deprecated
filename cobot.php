<?php

	define("VER", "1.0 Beta");
	date_default_timezone_set('UTC');
	require("config.php");
	require("lib/idiorm/idiorm.php");
	require("lib/SmartIRC/SmartIRC.php");
	require("cobot.core.php");
	
	$cobot = new CoBot($conf);
	
	require("modules.php");
	
	$cobot->connect();
