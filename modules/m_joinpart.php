<?php
/*
 * @name: Join-Part
 * @desc: Agrega comandos para que el bot entre o salga de un canal
 * @ver: 1.0
 * @author: MRX
 * @id: joinpart
 * @key: polsakervaadominarelmundo
 *
 */

class polsakervaadominarelmundo{
	public function __construct(&$core){
        $core->registerCommand("join", "joinpart", "Hace que el bot entre a un canal. Sintaxis: join <#canal>", 4, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
        $core->registerCommand("part", "joinpart", "Hace que el bot salga de un canal. Sintaxis: part [#canal] [Razón]", 4, CUSTOMPRIV, null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
	}
	
	public function join_priv(&$irc, $data, &$core){return $core->authchk($data->from,1,$data->messageex[1]);}
	public function join(&$irc, $data, &$core){
        $irc->join($data->messageex[1]);
	}
	public function part_priv(&$irc, $data, &$core){
		if(!isset($data->messageex[1]) || substr($data->messageex[1],0,1)!="#"){$chanout=$data->channel;$i=1;}else{$chanout=$data->messageex[1];}
		return $core->authchk($data->from,1,$chanout);
	}
	public function part(&$irc, $data, &$core)
	{
		$partmsg="Salida ordenada por un administrador";$i=2;
        //print_r($data);
		if(!isset($data->messageex[1]) || substr($data->messageex[1],0,1)!="#"){$chanout=$data->channel;$i=1;}else{$chanout=$data->messageex[1];}
		if((isset($data->messageex[1]) && substr($data->messageex[1],0,1)!="#")||isset($data->messageex[2])){
			$ts=$core->jparam($data->messageex,$i);
			$partmsg=$ts;
			
		}
        echo "---$chanout";
        $irc->part($chanout, $partmsg);
	}

	
}
