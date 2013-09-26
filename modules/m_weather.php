<?php
/*
 * @name: El tiempo
 * @desc: Provee información sobre el tiempo en el mundo.
 * @ver: 1.0
 * @author: MRX
 * @id: weather
 * @key: vaallover
 *
 */

class vaallover{
	public function __construct(&$core){
		$core->registerCommand("weather", "weather", false, -1, "*", "tiempo");
		$core->registerCommand("tiempo", "weather", "Da información sobre el tiempo. Sintaxis: tiempo <Ciudad, Pais/estacion>");	
	}
	
	public function tiempo($irc, $data, $core){
		$ts=$core->jparam($data->messageex, 1);
		$gap=file_get_contents("http://api.wunderground.com/api/".$core->conf['m_weather']['api_key']."/conditions/forecast/lang:es/q/".urlencode(str_replace(" ", "_",$ts)).".json");
		$jao=json_decode($gap);
		
		if(!@isset($jao->current_observation)){
			$resp="05Error: No se pudo encontrar la ciudad. ";
			if(@isset($jao->response->results[0])){
				$resp.="Talvez quiso decir: ";$i=0;
				while((!isset($jao->response->results[$i]))||($i!=20)){
					if(!isset($jao->response->results[$i]->city)){break;}
					$resp.="\"".$jao->response->results[$i]->city.", ".$jao->response->results[$i]->country_name."\" (zmw:".$jao->response->results[$i]->zmw."), ";
					$i++;
				}
			}
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $resp);
			return 0;
		}
		
		$resp="El tiempo en \00310".$jao->current_observation->display_location->full."\003: Viento a ".str_replace("W","O", $jao->current_observation->wind_kph)." Kilómetros por hora (".$jao->current_observation->wind_dir."), Presión ".$jao->current_observation->pressure_mb." hPa,";
		$resp.="  Sensación térmica: ".$jao->current_observation->feelslike_c."ºC";
		$resp.=", [".$this->conv($jao->current_observation->icon)."] Pronostico: ";
		
		$i=0;
		while($i!=3){
			$resp.= "\00303". $this->convday($jao->forecast->simpleforecast->forecastday[$i]->date->weekday);
			$resp.= "\003 [".$this->conv($jao->forecast->simpleforecast->forecastday[$i]->icon)."], ";
			$resp.="Máxima de \00306".$jao->forecast->simpleforecast->forecastday[$i]->high->celsius."ºC\003 ";
			$resp.="Mínima de \00306".$jao->forecast->simpleforecast->forecastday[$i]->low->celsius."ºC\003, ";
			$i++;
		}
		$resp=trim($resp,", ");
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $resp);
	}
	private function conv($estr){
		switch($estr){
			case "clear": $r="Despejado";break;
			case "mostlysunny": $r="Parcialmente despejado";break;
			case "sunny": $r="Soleado";break;
			case "partlycloudy": $r="Parcialmente nublado";break;
			case "mostlycloudy": $r="Parcialmente nublado";break;
			case "mist": $r="Con niebla";break;
			case "chancerain": $r="Posibles precipitaciones";break;
			case "rain": $r="Lluvia";break;
			case "chancestorms": $r="Probabilidad de tormentas";break;
			case "storm": $r="Tormenta";break;
			case "snow": $r="Nieve";break;
			case "cloudy": $r="Nublado";break;
			case "showers": $r="chubasco";break;
			case "thunderstorm": $r="Tormenta de truenos";break;
			case "rain_snow": $r="Nieve y lluvia";break;
			case "foggy": $r="Niebla";break;
			case "fog": $r="Niebla";break;
			case "icy": $r="Helado";break;
			case "tstorms": $r="Tormentas";break;
			case "chancetstorms": $r="Posibles Tormentas";break;
			
		}
		return $r;
	}
	private function convday($estr){
		switch(strtolower($estr)){
			case "monday": $r="Lunes";break;
			case "monday night": $r="Lunes a la noche";break;
			case "sunday": $r="Domingo";break;
			case "sunday night": $r="Domingo a la noche";break;
			case "tuesday": $r="Martes";break;
			case "tuesday night": $r="Martes a la noche";break;
			case "wednesday": $r="Miercoles";break;
			case "wednesday night": $r="Miercoles a la noche";break;
			case "thursday": $r="Jueves";break;
			case "thursday night": $r="Jueves a la noche";break;
			case "friday": $r="Viernes";break;
			case "friday night": $r="Viernes a la noche";break;
			case "saturday": $r="Sabado";break;
			case "saturday night": $r="Sabado a la noche";break;
		}
		return $r;
	}

}
