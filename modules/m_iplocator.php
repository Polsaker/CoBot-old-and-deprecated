<?php
/*
 * @name: Geolocalizador
 * @desc: Agrega funciones para geolocalizar IPs
 * @ver: 1.0
 * @author: MRX
 * @id: iplocator
 * @key: key
 *
 */

class key{
	public function __construct(&$core){
		$core->registerCommand("ip", "iplocator", "Geolocaliza una dirección IP/Dominio. Sintaxis: ip <IP/Dominio>");
	}
	
	public function ip(&$irc, &$data, &$core){
            $gap = file_get_contents("http://ip-api.com/json/{$data->messageex[1]}?fields=61439");
			$jao=json_decode($gap);
            print_r($jao);
            $r="";
            if($jao->status=="success"){
                $r.="IP: \002{$jao->query}\002";
                if($jao->reverse){ $r.=" = \002{$jao->reverse}\002";}
                if($jao->isp){ $r.=", \002ISP:\002 {$jao->isp}";}
                if($jao->org){ $r.=", \002Organización:\002 {$jao->org}";}
                if($jao->as){ $r.=", \002AS:\002 {$jao->as}";}
                if($jao->country){ $r.=", \002País:\002 {$jao->country}";}
                if($jao->regionName){ $r.=", \002Región:\002 {$jao->regionName}";}
                if($jao->city){ $r.=", \002Ciudad:\002 {$jao->city}";}
                if($jao->lat){ $r.=", \002Latitud:\002 {$jao->lat}";}
                if($jao->lon){ $r.=", \002Longitud:\002 {$jao->lon}";}
                
                
            }else{
                $r.="No se pudo procesar esta dirección.";
            }
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}	
}
