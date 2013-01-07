<?php 
	/*
	 * m_quit.php
	 * Agrega el comando quit.
	 */
	 $name="ignore"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'deignore', 'ignore');	
		$irc->addcmd($this, 'ignorelist', 'ignore');
		$this->help['deignore']='Quita un ignore';
		$this->help['deignore_l']=8;
		$this->help['ignorelist']='Muestra la lista de usuarios ignorados.';
		
	}

	public function deignore(&$irc,$msg,$channel,$param,$who){
		if($irc->checkauth($who,8)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		$myconn=$irc->myiConn();
		$myconn->query("DELETE FROM `ignore` WHERE `host` = '$param[1]'");
	}
	public function ignorelist(&$irc,$msg,$channel,$param,$who){
		$myconn=$irc->myiConn();
		$rsx=$myconn->query("SELECT * FROM `ignore`");
		$list="";
		while(@$rowx=$rsx->fetch_array()){
			$list.=$rowx['host']. ", ";
		}
		$list=trim($list);$list=trim($list,",");
		$irc->SendCommand("PRIVMSG $channel :$list");
		$myconn->close();
	}
}
	?>
