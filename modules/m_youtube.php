<?php
/*
 * @name: youtube
 * @desc: Muestra información de un video de youtube
 * @ver: 1.0
 * @author: MRX
 * @id: youtube
 * @key: qwerty
 *
 */

class qwerty{
	public function __construct(&$core){
		$core->registerMessageHandler('PRIVMSG', "youtube", "ythandler");
	}
	
	public function ythandler(&$irc, $data, &$core){
				$chan=strtolower($data->channel);
				$text=$data->message;
				
				if(preg_match('/youtube\.com\/watch\?.*v=([A-Za-z0-9._%-]*)[&\w;=\+_\-]*/',$text,$m2) || preg_match('/youtu\.be\/([A-Za-z0-9._%-]*)/',$text,$m2)){
					$id=$m2[1];
					$gap=file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&part=id,contentDetails,statistics,snippet&key=".$core->conf["m_google"]["api_key"]);
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
					
					if(preg_match("@PT(.+)H.*@",$duration,$m)){$dh=$m[1];}
					if(preg_match("@PT.+H(.+)M.*@",$duration,$m)){$dm=$m[1];}elseif(preg_match("@PT(.+)M.*@",$duration,$m)){$dm=$m[1];}
					if(preg_match("@PT.+H.+M(.+)S.*@",$duration,$m)){$ds=$m[1];}elseif(preg_match("@PT.+M(.+)S.*@",$duration,$m)){$ds=$m[1];}elseif(preg_match("@PT(.+)S.*@",$duration,$m)){$ds=$m[1];}
					if($dh<10){$dh="0".$dh;} if(!$dh){$dh="00";} 
					if($dm<10){$dm="0".$dm;} if(!$dm){$dm="00";}
					if($ds<10){$ds="0".$ds;} if(!$ds){$ds="00";}
					
					$trnk=$likes+$dlikes;
					$rank=round(($likes / $trnk ) * 100);
					if($rank>50){ $rank="03".$rank; }else{ $rank="05".$rank; }
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "$vname 10Duración: $dh:$dm:$ds, 10Visto $views veces, con 03$likes Me gusta 05$dlikes No me gusta ($rank%) y $coms comentarios" );
					
				}
	}

}
