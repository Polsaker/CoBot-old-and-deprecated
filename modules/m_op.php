<?php
/*
 * @name: Op
 * @desc: Agrega comandos de operador
 * @ver: 1.0
 * @author: MRX
 * @id: op
 * @key: ghasts
 *
 */
class ghasts{
	public function __construct($core){
		$core->registerCommand("op", "op", "Da OP en un canal. Sintaxis: op [canal] [nick]", 5);
		$core->registerCommand("deop", "op", "Quita OP en un canal. Sintaxis: deop [canal] [nick]", 5);
		$core->irc->setChannelSynching(true);
	}
	public function op(&$irc, $data, &$core){
		if(substr($data->messageex[1],0,1)=="#"){$chan=$data->messageex[1];}else{$chan=$data->channel;}
		if($irc->isOpped($chan)){
			$irc->op($chan, "MRX");
		}
		print_r($data);
	}
}
