<?php

	define("VER", "1.0 Alpha");
	date_default_timezone_set('UTC');
	require("config.php");
	require("mustached-ironman/SmartIRC.php");
	require("cobot.core.php");
	
	$ircbot = new CoBot($conf);
	
	//TODO: Carga de los modulos aqui!!
	
	$ircbot->connect();
