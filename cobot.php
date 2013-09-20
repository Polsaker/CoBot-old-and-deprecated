<?php

	define("VER", "0.2.2.1");
	date_default_timezone_set('UTC');
	require("config.php");
	
	require("cobot.class.php");
	
	$ircbot = new CoBot();
	
