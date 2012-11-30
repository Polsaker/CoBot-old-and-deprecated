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

	public function deignore(&$irc,$msg,$channel,$param,$who)
	{
		if($irc->checkauth($who,8)!=1){$irc->SendCommand("PRIVMSG ".$channel." :05Error: No tienes permisos suficientes como para ejecutar esta función.");return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$sql="DELETE FROM `ignore` WHERE `host` = '$param[1]'";
		$rsx = mysql_query($sql);
	}
	public function ignorelist(&$irc,$msg,$channel,$param,$who)
	{
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$sqlx="SELECT * FROM `ignore`";
		$rsx = mysql_query($sqlx);
		$list="";
		while(@$rowx=mysql_fetch_array($rsx)){
			$list.=$rowx['host']. ", ";
		}
		$list=trim($list);$list=trim($list,",");
		$irc->SendCommand("PRIVMSG $channel :$list");
	}
}
	?>
