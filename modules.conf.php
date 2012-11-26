<?php
$ircbot->load("m_joinpart.php"); // agrega los comandos join y part
$ircbot->load("m_quit.php"); // agrega el comando quit
$ircbot->load("m_say.php"); // Agrega el comando say
$ircbot->load("m_google.php"); // Agrega el comando google
$ircbot->load("m_authadd.php"); // Permite el registro de nuevos usuarios
$ircbot->load("m_ping.php"); // agrega el comando ping
//comentado por defecto debido a que requiere la descarga de archivos externos (vease mas en cobot.tk)
#include("geoloc/geoipcity.inc");include("geoloc/geoipregionvars.php");include("geoloc/timezone.php"); //requerido por m_iplocator
#$ircbot->load("m_iplocator.php"); //permite geolocalizar IPs con GeoIP [requiere geoipcity.inc, geoipregionvars.php y timezone.php]
$ircbot->load("m_nick.php"); //agrega el comando nick.
$ircbot->load("m_learn.php");
$ircbot->load("m_protect.php");
#$ircbot->load("m_oper.php");
$ircbot->load("m_translate.php");
$ircbot->load("m_wikichan.php");
$ircbot->load("m_games.php");
$ircbot->load("m_youtube.php");
$ircbot->load("m_hash.php");
$ircbot->load("m_modules.php");
$ircbot->load("m_ignore.php");
$ircbot->load("m_booksearch.php");
$ircbot->load("m_short.php");


?>
