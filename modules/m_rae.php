<?php
/*
 * @name: Diccionario RAE
 * @desc: Muestra entradas del diccionario de la Real Academia Española
 * @ver: 1.0
 * @author: MRX
 * @id: rae
 * @key: ear
 *
 */

class ear{
	public function __construct(&$core){
		$core->registerCommand("rae", "rae", "Muestra la definición de una palabra según el diccionario de la Real Academia Española. Sintaxis: rae <Palabra>");
	}
	
  public function rae(&$irc, $data, &$core){
    	$f=file_get_contents("http://rae-quel.appspot.com/json?query=".urlencode(strtolower($data->messageex[1])));
		$js=json_decode($f);$div="Definiciones encontradas de la palabra \"".strtolower($data->messageex[1])."\": ";
		foreach($js as $key=>$val){
			$div.="\"$val\", ";
		}
		$div = trim($div, ", ");
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $div);
  }
}
