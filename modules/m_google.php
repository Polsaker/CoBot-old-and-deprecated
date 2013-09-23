<?php
/*
 * @name: Búsqueda en google
 * @desc: Agrega un comando para realizar busquedas en google
 * @ver: 1.0
 * @author: MRX
 * @id: google
 * @key: polsakerrulz
 *
 */

class polsakerrulz{
	public function __construct(&$core){
		$core->registerCommand("google", "google", "Realiza una búsqueda en google. Sintaxis: google <texto de la busqueda>");
	}
	
	public function google(&$irc, &$data, &$core){
			$ts=$core->jparam($data->messageex,1);
			$gap=file_get_contents("https://www.googleapis.com/customsearch/v1?num=3&key=".$core->conf['m_google']['api_key']."&cx=001206920739550302428:fozo2qblwzc&q=".urlencode($ts)."&alt=json");
			$jao=json_decode($gap);
			$resp="Resultados de la búsqueda en Google de \"".$ts."\": ".$jao->items[0]->title." 10".$jao->items[0]->link." ".$jao->items[1]->title." 10".$jao->items[1]->link." ".$jao->items[2]->title." 10".$jao->items[2]->link."";

	}	
}
