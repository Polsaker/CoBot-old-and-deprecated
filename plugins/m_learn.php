<?php 
	/*
	 * m_learn.php
	 * Agrega definiciones de palabras.
	 */
	 $name="learn"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'learn', 'learn');	
			$irc->addcmd($this, 'forget', 'learn');	
			$irc->addcmd($this, 'find', 'learn');	
			$irc->addcmd($this, 'def', 'learn',array("??"));	
			$this->help['learn']='Agrega una definición. Sintaxis: learn <palabra> <definición>';
			$this->help['forget']='Elimina una definición. Sintaxis: forget <palabra>';
			$this->help['def']='Muestra la definición de una palabra. Sintaxis: def <palabra>';
			$this->help['find']='Busca una palabra por definición o nombre. Sintaxis: find <palabra>';
		}

		public function learn(&$irc,$msg,$channel,$param,$who){
			$i=2;
			$ts="";
			while(@isset($param[$i])){
				$ts.=$param[$i]. " ";
				$i++;
			}
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM defs WHERE pal='".mysql_real_escape_string($param[1])."'",$myconn);
			if(mysql_num_rows($rsx)!=0){mysql_close($myconn);$irc->SendPriv($channel,"05Error: La definición ya existe!");return 0;}
			$rsx = mysql_query("INSERT INTO defs (pal,def) VALUES ('".mysql_real_escape_string($param[1])."','".mysql_real_escape_string($ts)."')",$myconn);
			$irc->SendPriv($channel,"Se ha insertado la nueva definición en la base de datos.");
			mysql_close($myconn);
		}
		public function forget(&$irc,$msg,$channel,$param,$who){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$sql="DELETE FROM `defs` WHERE `defs`.`pal` = '".mysql_real_escape_string($param[1])."'";
			$rsx = mysql_query("SELECT * FROM defs WHERE pal='".mysql_real_escape_string($param[1])."'",$myconn);
			if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendPriv($channel,"05Error: La definición no existe!");return 0;}
			$rsx = mysql_query($sql,$myconn);
			if($rsx){$irc->SendPriv($channel,"Se ha borrado la definición.");}
			mysql_close($myconn);
		}
		public function def(&$irc,$msg,$channel,$param,$who){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM defs WHERE pal='".mysql_real_escape_string($param[1])."'",$myconn);
			if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendPriv($channel,"05Error: La definición no existe!");return 0;}
			$rowx=mysql_fetch_array($rsx);
			$irc->SendPriv($channel,"".$param[1]." = ".$rowx['def']);
			mysql_close($myconn);
		}
		public function find(&$irc,$msg,$channel,$param,$who){
			$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
			mysql_select_db($irc->conf['db']['name']);
			$rsx = mysql_query("SELECT * FROM defs WHERE pal LIKE '%".mysql_real_escape_string($param[1])."%' OR def LIKE '%".mysql_real_escape_string($param[1])."%'",$myconn);
			if(mysql_num_rows($rsx)==0){mysql_close($myconn);$irc->SendPriv($channel,"No se encontraron coincidencias.");return 0;}
			$ta="";
			while($rowx=mysql_fetch_array($rsx)){$ta.=$rowx['pal']." ";}
			$irc->SendPriv($channel,"Coincidencias: ".$ta,true);
			mysql_close($myconn);
		}
	}
?>
