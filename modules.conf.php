<?php
$ircbot->load("m_atheme.php"); // Integración con atheme (op, deop, etc) [BETA]
$ircbot->load("m_authadd.php"); // Permite el registro de nuevos usuarios [BETA]
$ircbot->load("m_booksearch.php"); // Busca libros en google books
$ircbot->load("m_bot.php"); // Se auto-asigna el modo +b (bot) al conectarse
$ircbot->load("m_divisa.php"); // Conversor de divisas
$ircbot->load("m_games.php"); // Juegos para el bot
$ircbot->load("m_google.php"); // Agrega el comando google [BETA]
$ircbot->load("m_hash.php"); // Permite codificar textos usando hashes 
$ircbot->load("m_ignore.php"); // Agrega mas funciones al ignore
//comentado por defecto debido a que requiere la descarga de archivos externos (vease mas en la documentación)
#include("geoloc/geoipcity.inc");include("geoloc/geoipregionvars.php");include("geoloc/timezone.php"); //requerido por m_iplocator
#$ircbot->load("m_iplocator.php"); //permite geolocalizar IPs con GeoIP [requiere geoipcity.inc, geoipregionvars.php y timezone.php]
$ircbot->load("m_joinpart.php"); // agrega los comandos join y part
$ircbot->load("m_learn.php");
$ircbot->load("m_modules.php"); // Permite la carga y descarga de módulos sin detener el bot
$ircbot->load("m_mwedit.php"); // Corrector ortográfico para mediawiki [BETA]
$ircbot->load("m_nick.php"); //agrega el comando nick.
$ircbot->load("m_nickserv.php"); // Autenticación con nickserv
$ircbot->load("m_op.php"); // Funciones de operador (op, deop, voice, kickban, etc)
#$ircbot->load("m_oper.php");
$ircbot->load("m_ping.php"); // agrega el comando ping
$ircbot->load("m_protect.php"); // No deja que el bot sea baneado o deopeado siempre y cuando tenga acceso a chanserv.
$ircbot->load("m_quit.php"); // agrega el comando quit
$ircbot->load("m_rae.php"); // Diccionario de la Real Academia Española
$ircbot->load("m_say.php"); // Agrega el comando say
$ircbot->load("m_short.php"); // Acortador de URLs
$ircbot->load("m_translate.php"); // Traductor.
$ircbot->load("m_wikichan.php"); // Integración con software mediawiki por canal
$ircbot->load("m_weather.php"); // Comando weather, da el tiempo
$ircbot->load("m_youtube.php"); // Muestra información de links de youtube a un canal

?>
