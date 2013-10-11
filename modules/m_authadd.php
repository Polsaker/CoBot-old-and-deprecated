<?php
/*
 * @name: Autenticación mejorada
 * @desc: Agrega funciones útiles a la autenticación
 * @ver: 1.0
 * @author: MRX
 * @id: authadd
 * @key: amodkey
 *
 */

class amodkey{
	public function __construct($core){
		$core->registerCommand("register", "authadd", false, -1, "*", null, SMARTIRC_TYPE_QUERY);
		$core->registerCommand("listpriv", "authadd", "Lista los privilegios de un usuario. Sintaxis: listpriv <usuario>");
		$core->registerCommand("addpriv", "authadd", "Da privilegios a un usuario. Sintaxis: addpriv <usuario> <privilegios> <sector>",9);
		$core->registerCommand("delpriv", "authadd", "Quita privilegios a un usuario. Sintaxis: delpriv <usuario> <privilegios> <sector>",9);
		$core->registerCommand("listusers", "authadd", "Lista los usuarios actualmente registrados");

	}
	
	public function listusers(&$irc, &$data, &$core){
		$users = ORM::for_table('users')->find_many();
		$r="Usuarios actualmente registrados: ";
		foreach($users as $val){$r.="{$val->username}, ";}
		$r = trim($r,", ");
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
		
	}
	public function register(&$irc, &$data, &$core){
		if(!isset($data->messageex[2])){
			$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, "Faltan parámetros. Sintaxis: register <usuario> <contraseña>");return 0;
		}
		$user = ORM::for_table('users')->where("username", $data->messageex[1])->find_one();
		if($user){
			$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, "Ese usuario ya esta registrado!");return 0;
		}
		$user = ORM::for_table('users')->create();
		$user->username=strtolower($data->messageex[1]);
		$user->pass=sha1($data->messageex[2]);
		$user->save();

		$priv = ORM::for_table('userpriv')->create();
		$priv->uid = $user->id;
		$priv->rng = "0";
		$priv->sec = "*";
		$priv->save();
		$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, "Registrado exitosamente.");
	}
	
	public function listpriv(&$irc, &$data, &$core){
		if(!isset($data->messageex[1])){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Faltan parámetros!!");return 0;}
		$user = ORM::for_table('users')->where('username', strtolower($data->messageex[1]))->find_one();
		$userpriv = ORM::for_table('userpriv')->where('uid', $user->id)->find_many();
		$r="\002{$data->messageex[1]}\002 tiene los siguientes privilegios: ";
		foreach($userpriv as $privuser){
			$r.="\002{$privuser->rng}\002  en \002{$privuser->sec}\002, ";
		}
		$r = trim($r,", ");
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}
	
	public function addpriv(&$irc, &$data, &$core){
		if(!isset($data->messageex[3])){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Faltan parámetros!!"); return 0;}
		if($data->messageex[2]>9){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Error de usuario. Inserte otro usuario y presione enter. (No se pueden otorgar privilegios de nivel 10!!)"); return 0;}
		$user = ORM::for_table('users')->where('username', strtolower($data->messageex[1]))->find_one(); 
		$k = ORM::for_table('userpriv')->where('uid', $user->id)->where('sec',$data->messageex[3])->find_one();
		if(method_exists($k, "delete")){$k->delete();}
		$priv = ORM::for_table('userpriv')->create();
		$priv->uid = $user->id;
		$priv->rng = $data->messageex[2];
		$priv->sec = $data->messageex[3];
		$priv->save();
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Se han otorgado los privilegios.");
	}
	
	public function delpriv(&$irc, &$data, &$core){
		if(!isset($data->messageex[3])){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Faltan parámetros!!");return 0;}
		if($data->messageex[2]>9){$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "\00305Error:\003 Error de usuario. Inserte otro usuario y presione enter. (No se pueden otorgar privilegios de nivel 10!!)");}
		$user = ORM::for_table('users')->where('username', strtolower($data->messageex[1]))->find_one();
		if(!isset($user->id)){return 0; } //el usuario no existeer 
		$userpriv = ORM::for_table('userpriv')->where('uid', $user->id)->where("rng", $data->messageex[2])->where("sec", $data->messageex[3])->find_one();
		if(method_exists($userpriv, "delete")){$userpriv->delete();}
		
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Se han otorgado los privilegios.");
	}
}
