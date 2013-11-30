<?php
/*
 * @name: Písesa
 * @desc: Muestra información de diversos paises
 * @ver: 1.0
 * @author: MRX
 * @id: country
 * @key: subliminalmessagesonthecode
 *
 */

class subliminalmessagesonthecode{
	public function __construct(&$core){
		$core->registerCommand("pais", "country", "Muestra información de un país. Sintaxis: pais <Código de pais>");
		$core->registerCommandAlias("país", "pais");
	}
	
	public function pais(&$irc, $data, &$core){
		$p = file_get_contents("http://restcountries.eu/rest/alpha/{$data->messageex[1]}");
		$j = json_decode($p);
		print_r($j);
		$r = "\2{$j->translations->es}\2: Capital: \2{$j->capital}\2, moneda: \2{$j->currency}\2, población: \2".number_format($j->population,0,",",".")."\2 ".
		"TLD: \2{$j->topLevelDomain}\2. Superficie: \2".number_format($j->area,0,",",".")."\2 km². Idiomas: ";
			foreach($j->languages as $l){
				$r.="\2".$this->idiomas()[$l]."\2, ";
			}
			$r=trim($r,", "). " Zonas horarias: ";
		/* <parseando las zonas horarias..> */
			foreach($j->timezones as $tz){
				if($tz=="UTC"){
					$ts=time();
				}else{
					echo $tz;
					preg_match("#UTC(\+|\-|−)(.+)\:(.+)#i", $tz, $m);
					print_r($m);
					$diff = ($m[2] * 3600) + ($m[3]*60);
					if($m[1]=="+"){
						$ts = time() + $diff;
					}else{
						$ts = time() - $diff;
					}
				}
				$r.= "\2".$tz."\2 (".date("H:i:s", $ts)."), ";
			}
			$r=trim($r, ", ");
		/* </parseo> */
		$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $r);
	}
	
	private function idiomas(){
		return array('aa' => 'afar',
			'ab' => 'abjaso',
			'ae' => 'avéstico',
			'af' => 'afrikaans',
			'ak' => 'akano',
			'am' => 'amárico',
			'an' => 'aragonés',
			'ar' => 'árabe',
			'as' => 'asamés',
			'av' => 'avar',
			'ay' => 'aimara',
			'az' => 'azerí',
			'ba' => 'baskir',
			'be' => 'bielorruso',
			'bg' => 'búlgaro',
			'bh' => 'bhojpurí',
			'bi' => 'bislama',
			'bm' => 'bambara',
			'bn' => 'bengalí',
			'bo' => 'tibetano',
			'br' => 'bretón',
			'bs' => 'bosnio',
			'ca' => 'catalán',
			'ce' => 'checheno',
			'ch' => 'chamorro',
			'co' => 'corso',
			'cr' => 'cree',
			'cs' => 'checo',
			'cu' => 'eslavo eclesiástico antiguo',
			'cv' => 'chuvasio',
			'cy' => 'galés',
			'da' => 'danés',
			'de' => 'alemán',
			'dv' => 'maldivo',
			'dz' => 'dzongkha',
			'ee' => 'ewe',
			'el' => 'griego',
			'en' => 'inglés',
			'eo' => 'esperanto',
			'es' => 'español',
			'et' => 'estonio',
			'eu' => 'euskera',
			'fa' => 'persa',
			'ff' => 'fula',
			'fi' => 'finés',
			'fj' => 'fiyiano',
			'fo' => 'feroés',
			'fr' => 'francés',
			'fy' => 'frisón',
			'ga' => 'irlandés',
			'gd' => 'gaélico escocés',
			'gl' => 'gallego',
			'gn' => 'guaraní',
			'gu' => 'guyaratí',
			'gv' => 'manés',
			'ha' => 'hausa',
			'he' => 'hebreo',
			'hi' => 'hindi',
			'ho' => 'hiri motu',
			'hr' => 'croata',
			'ht' => 'haitiano',
			'hu' => 'húngaro',
			'hy' => 'armenio',
			'hz' => 'herero',
			'ia' => 'interlingua',
			'id' => 'indonesio',
			'ie' => 'occidental',
			'ig' => 'igbo',
			'ii' => 'yi de Sichuán',
			'ik' => 'inupiaq',
			'io' => 'ido',
			'is' => 'islandés',
			'it' => 'italiano',
			'iu' => 'inuktitut',
			'ja' => 'japonés',
			'jv' => 'javanés',
			'ka' => 'georgiano',
			'kg' => 'kongo',
			'ki' => 'kikuyu',
			'kj' => 'kuanyama',
			'kk' => 'kazajo',
			'kl' => 'groenlandés',
			'km' => 'camboyano',
			'kn' => 'canarés',
			'ko' => 'coreano',
			'kr' => 'kanuri',
			'ks' => 'cachemiro',
			'ku' => 'kurdo',
			'kv' => 'komi',
			'kw' => 'córnico',
			'ky' => 'kirguís',
			'la' => 'latín',
			'lb' => 'luxemburgués',
			'lg' => 'luganda',
			'li' => 'limburgués',
			'ln' => 'lingala',
			'lo' => 'lao',
			'lt' => 'lituano',
			'lu' => 'luba-katanga',
			'lv' => 'letón',
			'mg' => 'malgache',
			'mh' => 'marshalés',
			'mi' => 'maorí',
			'mk' => 'macedonio',
			'ml' => 'malayalam',
			'mn' => 'mongol',
			'mr' => 'maratí',
			'ms' => 'malayo',
			'mt' => 'maltés',
			'my' => 'birmano',
			'na' => 'nauruano',
			'nb' => 'noruego bokmål',
			'nd' => 'ndebele del norte',
			'ne' => 'nepalí',
			'ng' => 'ndonga',
			'nl' => 'neerlandés',
			'nn' => 'nynorsk',
			'no' => 'noruego',
			'nr' => 'ndebele del sur',
			'nv' => 'navajo',
			'ny' => 'chichewa',
			'oc' => 'occitano',
			'oj' => 'ojibwa',
			'om' => 'oromo',
			'or' => 'oriya',
			'os' => 'osético',
			'pa' => 'panyabí',
			'pi' => 'pali',
			'pl' => 'polaco',
			'ps' => 'pastú',
			'pt' => 'portugués',
			'qu' => 'quechua',
			'rm' => 'romanche',
			'rn' => 'kirundi',
			'ro' => 'rumano',
			'ru' => 'ruso',
			'rw' => 'ruandés',
			'sa' => 'sánscrito',
			'sc' => 'sardo',
			'sd' => 'sindhi',
			'se' => 'sami septentrional',
			'sg' => 'sango',
			'si' => 'cingalés',
			'sk' => 'eslovaco',
			'sl' => 'esloveno',
			'sm' => 'samoano',
			'sn' => 'shona',
			'so' => 'somalí',
			'sq' => 'albanés',
			'sr' => 'serbio',
			'ss' => 'suazi',
			'st' => 'sesotho',
			'su' => 'sundanés',
			'sv' => 'sueco',
			'sw' => 'suajili',
			'ta' => 'tamil',
			'te' => 'telugú',
			'tg' => 'tayiko',
			'th' => 'tailandés',
			'ti' => 'tigriña',
			'tk' => 'turcomano',
			'tl' => 'tagalo',
			'tn' => 'setsuana',
			'to' => 'tongano',
			'tr' => 'turco',
			'ts' => 'tsonga',
			'tt' => 'tártaro',
			'tw' => 'twi',
			'ty' => 'tahitiano',
			'ug' => 'uigur',
			'uk' => 'ucraniano',
			'ur' => 'urdu',
			'uz' => 'uzbeko',
			've' => 'venda',
			'vi' => 'vietnamita',
			'vo' => 'volapük',
			'wa' => 'valón',
			'wo' => 'wolof',
			'xh' => 'xhosa',
			'yi' => 'yídish',
			'yo' => 'yoruba',
			'za' => 'chuan',
			'zh' => 'chino',
			'zu' => 'zulú'
			);
		}
}
