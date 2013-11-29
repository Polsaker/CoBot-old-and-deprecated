<?php
/*
 * @name: BTC
 * @ver: 1.0
 * @author: MRX
 * @id: btc
 * @key: imagenius
 *
 */

class imagenius{
        public function __construct(&$core){
        
                $core->registerCommand("bitcoin", "btc", "Muestra el precio del bitcoin");

        }
        
        public function bitcoin(&$irc, $data, $core){
            $b = file_get_contents("http://www.bitstamp.net/api/ticker/");
            $l = json_decode($b);
            #print_r($l);
            if((isset($data->messageex[1])) && (is_numeric($data->messageex[1]))){
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "BTC \2{$data->messageex[1]}\2 = USD \2".($l->last*$data->messageex[1])."\2");
			}else{
				$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, "Último: \2\${$l->last}\2, Alto: \2\${$l->high}\2, Bajo: \2\${$l->low}\2");
			}
        }
}
