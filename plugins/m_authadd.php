<?php 
	$name="authadd"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'register', 'authadd');	
		$irc->addcmd($this, 'setpriv', 'authadd');	
		//$irc->addcmd($this, 'addpriv', 'authadd');	
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

	public function register(&$irc,$msg,$channel,$param,$who){		
		if(strtolower($channel)!=strtolower($irc->nick)){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Este comando no se debe usar desde un canal."); return 0;}
		if(!@isset($param[2])){$irc->sendCommand("PRIVMSG ".$irc->mask2nick($who)." :\00305Error:\003 Faltan parametros."); return 0;}
		$myconn=$irc->myiConn();
		$x=$myconn->query("INSERT INTO `users` (`user` ,`pass`, `rng`) VALUES ('".mysqli_real_escape_string($myconn,$param[1]). "',  sha1('".mysqli_real_escape_string($myconn,$param[2])."'), '0,*')");
	//if(!$x){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Error interno \002002\002. Notifique al administrador."); } $myconn->close; 
	}
	// Obsoleto (?)
	
	public function setpriv(&$irc,$msg,$channel,$param,$who){
		if(!@isset($param[2])){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Faltan parametros."); return 0;}
		if($irc->checkauth($who,9)!=1){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 No autorizado."); return 0;}
		if($param[2]>=10){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 No se pueden otorgar privilegios maximos."); return 0;}
		$sqlx="UPDATE `users` SET `rng` =  '".$param[2]."' WHERE  `user`='".$param[1]."'";
		$myconn=$irc->myiConn();
		if(!$myconn->query($sqlx)){$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Error interno \002002\002. Notifique al administrador."); return 0;}$myconn->close();
		$irc->sendCommand("PRIVMSG ".$channel." :Se han otorgado los privilegios.");
	}
	
	public function deluser(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,9)!=1){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 No autorizado."); return 0;}
		if(!@isset($param[1])){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Faltan parametros."); return 0;}
		$myconn=$irc->myiConn();
		$rsx=$myconn->query("DELETE FROM `users` WHERE  `user`='".$param[1]."' AND `rng`<9");
		//if(!$rsx){ $irc->SendCommand("PRIVMSG ".$channel." :\00305Error:\003 no se pudo concretar la operacion."); return 0;}$myconn->close;
		$irc->sendCommand("PRIVMSG ".$channel." :Se ha borrado el usuario.");
	}
	public function listusers(&$irc,$msg,$channel,$param,$who){
		$sqlx="SELECT user FROM users";
		$myconn=$irc->myiConn();
		if(!$rx=$myconn->query($sqlx)){mysqli_close($myconn); $irc->SendCommand("PRIVMSG ".$channel." :\00305Error:\003 no se pudo concretar la operacion."); return 0;}mysqli_close($myconn);
		$ss="";
		while($rowx=$rx->fetch_array()){ $ss.=" ".$rowx['user'].","; }$ss=substr($ss,0,strlen($ss)-1);
		$irc->sendCommand("PRIVMSG ".$channel." :Usuarios actualmente registrados:".$ss);
	}
	public function chgpass(&$irc,$msg,$channel,$param,$who){
		if(strtolower($channel)!=strtolower($irc->nick)){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Este comando no se debe usar desde un canal."); return 0;}
		$myconn=$irc->myiConn();
		$sql="SELECT * FROM users WHERE user='".mysqli_real_escape_string($myconn,$param[1])."' AND pass=sha1('".mysqli_real_escape_string($myconn,$param[2])."')";

		$rsx = $myconn->query($sql);
		if($rsx->num_rows==0){mysqli_close($myconn);$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :05Error: Usuario/Contraseña invalidos");return 0;}
		$sql="UPDATE  `users` SET  `pass` =  sha1('".mysqli_real_escape_string($myconn,$param[3])."') WHERE  `user`='".mysqli_real_escape_string($myconn,$param[1])."' AND pass=sha1('".mysqli_real_escape_string($myconn,$param[2])."')";
		if(!$rsx = mysqli_query($myconn,$sql)){mysqli_close($myconn); $irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :\00305Error:\003 no se pudo concretar la operacion."); return 0;}
		$irc->SendCommand("PRIVMSG ".$irc->mask2nick($who)." :Operación realizada exitosamente.");
		mysqli_close($myconn);
	}
	
	public function listpriv(&$irc,$msg,$channel,$param,$who){
		if(!@isset($param[1])){$irc->sendCommand("PRIVMSG ".$channel." :\00305Error:\003 Faltan parametros."); return 0;}
		$myconn=$irc->myiConn();
		$rsx = $myconn->query("SELECT * FROM `users` WHERE `user`='$param[1]'");
		$rowx=$rsx->fetch_array();
		$myconn->close();
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
