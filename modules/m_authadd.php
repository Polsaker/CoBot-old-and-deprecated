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
	}
	
	public function register(&$irc, &$data, &$core){
		if(isset($data->messageex[2])){
			$irc->message(SMARTIRC_TYPE_QUERY, $data->nick, "Faltan parámetros. Sintaxis: register <usuario> <contraseña>");
		}
		$user = ORM::for_table('users')->create();
		$user->username=$data->messageex[1];
		$user->pass=sha1($data->messageex[2]);
		$user->save();

		$priv = ORM::for_table('userpriv')->create();
		$priv->uid = $user->id;
		$priv->rng = "0";
		$priv->sec = "*";
		$priv->save();
	}
}
