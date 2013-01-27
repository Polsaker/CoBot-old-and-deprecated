<?php
 // Archivo de manejo del proceso hijo
 // Los procesos se comunicaran por texto plano o por mysql

$irc=new IRCBot($conf,true);
//require("modules.conf.php");

while(1){
	usleep(500);
	if(file_exists("child-todo")){
		include("child-todo");
		unlink("child-todo");
	}
}
