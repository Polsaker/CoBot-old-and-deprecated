<?php
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
	$conf['conn']['charset']="ISO-8859-1";
	
	//Configuración para el módulo OPER:
	$conf['m_oper']['operuser']="";
	$conf['m_oper']['operpass']="";
	
	$conf['m_translate']['cid']=""; // Client ID de microsoft translate
	$conf['m_translate']['cs']=""; // Client Secret
	$conf['m_translate']['authurl']="https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/"; // Esto supuestamente no se debe modificar
	$conf['m_translate']['scopeurl']="http://api.microsofttranslator.com"; // Esto tampoco
	$conf['m_translate']['granttype']="client_credentials"; // Esto tampoco debe ser modificado :P
	
	$conf['m_google']['api_key']=""; // google api key
	$conf['m_short']['bitly-api']=""; //Bit.ly api key
?>
