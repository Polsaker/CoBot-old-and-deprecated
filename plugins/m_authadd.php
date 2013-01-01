<?php 
	/*
	 * m_authadd.php
	 * agrega el comando register y givepriv
	 */
	 $name="authadd"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'register', 'authadd');	
		$irc->addcmd($this, 'setpriv', 'authadd');	
		$irc->addcmd($this, 'listpriv', 'authadd');	
		$irc->addcmd($this, 'deluser', 'authadd');	
		$irc->addcmd($this, 'listusers', 'authadd');	
		$irc->addcmd($this, 'chgpass', 'authadd');	

		$this->help['register']='Registra un usuario en la base de datos del bot. Sintaxis: register <usuario> <contraseña>. Es recomendable enviar este comando por mensaje privado.';
		$this->help['setpriv']='Otorga privilegios a un usuario (unicamente para usuarios nivel 9 o superior)';
		$this->help['setpriv_l']=9;
		$this->help['deluser']='Borra un usuario de la base de datos del bot (unicamente para usuarios nivel 9 o superior)';
		$this->help['deluser_l']=9;
		$this->help['listusers']='Lista todos los usuarios registrados en la base de datos del bot.';
		$this->help['chgpass']='Modifica la contraseña de un usuario. Sintaxis: chgpass <usuario> <antigua contraseña> <nueva contraseña>';
	}

	public function register(&$irc,$msg,$channel,$param,$who)
	{
		
#		if(substr(trim($channel),0,1)!="#"){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Este comando no se debe usar desde un canal."); return 0;}
		if(!@isset($param[2])){$irc->sendCommand("PRIVMSG ".$irc->mask2nick($who)." :\00305Error:\003 Faltan parametros."); return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$sqlx="INSERT INTO `users` (`user` ,`pass`, `rng`) VALUES ('".$param[1]. "',  sha1('".$param[2]."'), '0,*')";
		if(!$rsx = mysql_query($sqlx,$myconn)){mysql_close($myconn); $irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Error interno \002002\002. Notifique al administrador."); return 0;} mysql_close($myconn);

	}
	// Obsoleto (?)
	
	public function setpriv(&$irc,$msg,$channel,$param,$who)
	{
		if(!@isset($param[2])){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Faltan parametros."); return 0;}
		if($irc->checkauth($who,9)!=1){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 No autorizado."); return 0;}
		if($param[2]>=10){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 No se pueden otorgar privilegios maximos."); return 0;}
		$sqlx="UPDATE `users` SET `rng` =  '".$param[2]."' WHERE  `user`='".$param[1]."'";
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		if(!$rsx = mysql_query($sqlx,$myconn)){mysql_close($myconn); $irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Error interno \002002\002. Notifique al administrador."); return 0;}mysql_close($myconn);
		$irc->sendCommand("PRIVMSG ".$channel." :Se han otorgado los privilegios.");
	}
	
	public function deluser(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,9)!=1){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 No autorizado."); return 0;}
		if(!@isset($param[1])){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Faltan parametros."); return 0;}
		$sqlx="DELETE FROM `users` WHERE `user`='".$param[1]."'";
		if(!$rsx = mysql_query($sqlx,$myconn)){ $irc->SendCommand("PRIVMSG ".$channel." :\00305Error:\003 no se pudo concretar la operacion."); return 0;}
		$irc->sendCommand("PRIVMSG ".$channel." :Se ha borrado el usuario.");
	}
	public function listusers(&$irc,$msg,$channel,$param,$who)
	{
		$sqlx="SELECT user FROM users";
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		if(!$rsx = mysql_query($sqlx,$myconn)){mysql_close($myconn); $irc->SendCommand("PRIVMSG ".$channel." :\00305Error:\003 no se pudo concretar la operacion."); return 0;}mysql_close($myconn);
		$ss="";
		while($rowx=mysql_fetch_array($rsx)){ $ss.=" ".$rowx['user'].","; }$ss=substr($ss,0,strlen($ss)-1);
		$irc->sendCommand("PRIVMSG ".$channel." :Usuarios actualmente registrados:".$ss);
	}
	public function chgpass(&$irc,$msg,$channel,$param,$who)
	{
		if($channel!=$irc->nick){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Este comando no se debe usar desde un canal."); return 0;}
		$sql="SELECT * FROM users WHERE user='".$param[1]."' AND pass=sha1('".$param[2]."')";
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query($sql,$myconn);
		if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Usuario/Contraseña invalidos");return 0;}
		$sql="UPDATE  `users` SET  `pass` =  sha1('".$param[3]."') WHERE  `user`='".$param[1]."' AND pass=sha1('".$param[2]."')";
		if(!$rsx = mysql_query($sql,$myconn)){mysql_close($myconn); $irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :\00305Error:\003 no se pudo concretar la operacion."); return 0;}
		$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Operación realizada exitosamente.");
		mysql_close($myconn);
	}
	
	public function listpriv(&$irc,$msg,$channel,$param,$who)
	{
		if(!@isset($param[1])){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Faltan parametros."); return 0;}
		
		
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query("SELECT * FROM `users` WHERE `user`='$param[1]'",$myconn);$rowx=mysql_fetch_array($rsx);
		$pr2=explode("|",$rowx['rng']);
		$pr4=array();
		foreach($pr2 as $key=>$val){
			$pr3=explode(",",$val);
			array_push($pr4,$pr3);
		}
		$tx="El usuario \002$param[1]\002 ";
		foreach($pr4 as $key=>$val){
			$tx.="tiene privilegios de nivel \002".$pr4[$key][0]."\002 en \002".$pr4[$key][1]."\002 ";
		}
		$irc->sendCommand("PRIVMSG ".$channel." :$tx");
	}
	
	
}
	?>
