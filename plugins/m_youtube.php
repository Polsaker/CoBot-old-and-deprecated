<?php 
	/*
	 * m_youtube.php
	 * Muestra información de links de youtube.
	 * Renombrado a m_links pendiente
	 */
	 $name="youtube"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		private $chans;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'lchadd', 'youtube');
			$irc->addcmd($this, 'lchrem', 'youtube');
			if(!@is_array($irc->hdf['PRIVMSG'])){$irc->hdf['PRIVMSG']=array();}
			array_push($irc->hdf['PRIVMSG'],array("youtube","captalink"));
			
			
		}
		
		public function captalink(&$irc,$txt){
		
			if(preg_match('@^:.+ PRIVMSG (.+) :(.+)$@', $txt, $m2)){
				$chan=$m2[1];
				$text=$m2[2];
				
				$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
				mysql_select_db($irc->conf['db']['name']);
				$rsx = mysql_query("SELECT * FROM linkchans ẂHERE `chan`='$chan'",$myconn);
				if(mysql_num_rows($rsx)==0){return 0;}
				mysql_close($myconn);
				//uso dos preg_match para mayor comodidad mia :P
				// talvez los una algun dia..
				// si, todo esto parece un poco lioso, pero.. asi me gusta :p
				if(preg_match('/youtube\.com\/watch\?v=([A-Za-z0-9._%-]*)[&\w;=\+_\-]*/',$text,$m2)){
					$id=$m2[1];
					$gap=file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&part=id,contentDetails,statistics,snippet&key=".$irc->conf["m_google"]["api_key"]);
					$jao=json_decode($gap);
					//print_r($jao);
					$vname=$jao->items[0]->snippet->title;
					//$vpubl=$jao->items[0]->snippet->publishedAt;
					$views=$jao->items[0]->statistics->viewCount;
					$likes=$jao->items[0]->statistics->likeCount;
					$dlikes=$jao->items[0]->statistics->dislikeCount;
					$favs=$jao->items[0]->statistics->favoriteCount;
					$coms=$jao->items[0]->statistics->commentCount;
					$duration=$jao->items[0]->contentDetails->duration;
					
					//esto tambien se puede simplificar, pero no se me da la gana :P
					if(preg_match("@PT(.+)H.*@",$duration,$m)){$dh=$m[1];}
					if(preg_match("@PT.+H(.+)M.*@",$duration,$m)){$dm=$m[1];}elseif(preg_match("@PT(.+)M.*@",$duration,$m)){$dm=$m[1];}
					if(preg_match("@PT.+H.+M(.+)S.*@",$duration,$m)){$ds=$m[1];}elseif(preg_match("@PT.+M(.+)S.*@",$duration,$m)){$ds=$m[1];}elseif(preg_match("@PT(.+)S.*@",$duration,$m)){$ds=$m[1];}
					if($dh<10){$dh="0".$dh;} if(!$dh){$dh="00";} 
					if($dm<10){$dm="0".$dm;} if(!$dm){$dm="00";}
					if($ds<10){$ds="0".$ds;} if(!$ds){$ds="00";}
					
					$trnk=$likes+$dlikes;
					$rank=round(($likes / $trnk ) * 100);
					if($rank>50){ $rank="03".$rank; }else{ $rank="05".$rank; }
					$irc->SendCommand("PRIVMSG $chan :$vname 10Duración: $dh:$dm:$ds, 10Visto $views veces, con 03$likes Me gusta, 05$dlikes No me gusta ($rank%) y $coms comentarios");
				}
			}
			
			
			
		}
		
	public function lchadd(&$irc,$msg,$channel,$param,$who){
		if(!$irc->checkauth($who,5)==1){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: No posees los privilegios suficientes para usar este comando.");return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query("INSERT INTO linkchans (chan) VALUES ('".strtolower($param[1])."')",$myconn);
		mysql_close($myconn);
	}
	public function lchrem(&$irc,$msg,$channel,$param,$who){
		if(!$irc->checkauth($who,5)==1){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: No posees los privilegios suficientes para usar este comando.");return 0;}
		$myconn=mysql_connect($irc->conf['db']['host'],$irc->conf['db']['user'],$irc->conf['db']['pass']);
		mysql_select_db($irc->conf['db']['name']);
		$rsx = mysql_query("DELETE FROM linkchans WHERE chan='".strtolower($param[1])."'",$myconn);
		mysql_close($myconn);
}
	}
?>
