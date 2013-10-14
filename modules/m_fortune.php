<?php
/*
 * @name: Fortune
 * @desc: Hace que el bot diga mensajes filosoficos aleatoreos usando fortune
 * @ver: 1.0
 * @author: Richzendy
 * @id: fortune
 * @key: sayfortune
 *
 * Usa la librería https://github.com/chrismeller/fortune para conectar a http://wertarbyte.de/gigaset-rss/ y obtener las fortunes.
 * Por defecto se muestran fortunes ofensivas, si no lo desea, cambie el parametro display_offensive de true a false, también se
 * muestran por defecto las categorías asimov, deprimente, informatica, leydemurphy y camioneros en español, si desea agregar más
 * puede conseguir un listado completo de las disponibles en http://wertarbyte.de/gigaset-rss/
 *
 * Requiere ./lib/fortune/fortune.php requiere php-xml para funcionar y debe estar instalado si no, el bot puede morir con este error:
 * PHP Fatal error:  Class 'DOMDocument' not found
 */

class sayfortune{
	public function __construct(&$core){
	if (!class_exists('fortune')) {
		require("./lib/fortune/fortune.php"); 
          }
		$core->registerCommand("fortune", "fortune", "Hace que el bot diga una fortune. Sintaxis: " . $core->conf['irc']['prefix'] . "fortune", -1, "*", null, SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_CHANNEL);
	}
	
  public function fortune(&$irc, &$data, &$core){
    $sayfortune = Fortune::factory()->languages( array('es') )->jars( array( 'asimov.fortunes' , 'deprimente.fortunes' , 'informatica.fortunes' , 'leydemurphy.fortunes', 'camioneros.fortunes' ) )->display_long( true )->display_offensive( true )->get_fortunes( true )->text;
    $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $sayfortune);
  }
}
