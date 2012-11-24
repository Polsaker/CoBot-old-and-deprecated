m<?php
// SQL: 
/*
CREATE TABLE  `users` (
`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user` VARCHAR( 255 ) NOT NULL ,
`pass` VARCHAR( 255 ) NOT NULL ,
`rng` INT( 3 ) NOT NULL
) ENGINE = MYISAM ;
CREATE TABLE  `nickassoc` (
`ID` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ircnick` VARCHAR( 255 ) NOT NULL ,
`wikinick` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;
CREATE TABLE  `proxys` (
`ip` VARCHAR( 255 ) NOT NULL,
`p` INT( 255 ) NOT NULL
) ENGINE = MYISAM 
CREATE TABLE `defs` (
`pal` VARCHAR( 255 ) NOT NULL ,
`def` VARCHAR( 400 ) NOT NULL ,
PRIMARY KEY (  `pal` )
) ENGINE = MYISAM ;

*/
	
	/* Configuración para la conexión a la base de datos */
	$conf['db']['host']="localhost"; // host de la base de datos mysql
	$conf['db']['user']=""; //usuario mysql
	$conf['db']['pass']=""; // contraseña mysql
	$conf['db']['name']=""; //tabla mysql
	
	
	/* Configuracion de la conexión al servidor IRC */
	$conf['irc']['host']="127.0.0.1";
	$conf['irc']['port']=6667; //puerto
	$conf['irc']['nick']="CoBot"; // Nick
	$conf['irc']['nspass']=""; // Contraseña de nickserv
	$conf['irc']['ssl']=false; // usar SSL para conectarse al servidor IRC.
	
	$conf['irc']['channels']=array("#CoBOT"); // canales a los que el bot entrara al conectarse
		$conf['irc']['prefix']="$"; //prefijo de los comandos
	
	$conf['conn']['reconnect']=15; //numero de reconecciones. dejar en 1 para desactivars
	
	//Configuración para el módulo OPER:
	$conf['m_oper']['operuser']="";
	$conf['m_oper']['operpass']="";
	
	$conf['m_translate']['cid']=""; // Client ID de microsoft translate
	$conf['m_translate']['cs']=""; // Client Secret
	$conf['m_translate']['authurl']="https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/"; // Esto supuestamente no se debe modificar
	$conf['m_translate']['scopeurl']="http://api.microsofttranslator.com"; // Esto tampoco
	$conf['m_translate']['granttype']="client_credentials"; // Esto tampoco debe ser modificado :P
	
	$conf['m_google']['api_key']=""; // google api key
?>
