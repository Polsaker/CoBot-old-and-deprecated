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
        
        public function btc(&$irc, $data, $core){
            $b = file_get_contents("http://www.bitstamp.net/api/ticker/");
            $l = json_decode($b);
            print_r($l);
        }
        
        function diff($start,$end = false) { 
                if(!$end) { $end = time(); } 
                if(!is_numeric($start) || !is_numeric($end)) { return false; } 

                $start  = date('Y-m-d H:i:s',$start); 
                $end    = date('Y-m-d H:i:s',$end); 
                $d_start    = new DateTime($start); 
                $d_end      = new DateTime($end); 
                $diff = $d_start->diff($d_end); 

                $this->diff['ye']    = $diff->format('%y'); 
                $this->diff['mo']    = $diff->format('%m'); 
                $this->diff['da']      = $diff->format('%d'); 
                $this->diff['ho']     = $diff->format('%h'); 
                $this->diff['mi']      = $diff->format('%i'); 
                $this->diff['se']      = $diff->format('%s'); 
                return true; 
        } 
        
}
