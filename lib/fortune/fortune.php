<?php

	class Fortune {

		const BASE_URL = 'http://wertarbyte.de/gigaset-rss/';

		/**
		 * Fortunes with more than this many characters are considered long.
		 * @var integer
		 */
		public $long_fortune_length = 140;

		/**
		 * Should fortunes over $long_fortune_length characters be included?
		 * @var boolean
		 */
		public $display_long_fortunes = false;

		/**
		 * Should offensive fortunes be included? If not set to 'true', it doesn't matter if you pick an offensive jar, you won't get them.
		 * @var boolean
		 */
		public $display_offensive_fortunes = true;

		/**
		 * Include fortunes for these languages:
		 *     de - German
		 *     en - English
		 *     es - Spanish
		 *     fr - French
		 * @var array
		 */
		public $languages = array(
			'de',
			'en',
			'es',
			'fr',
		);

		/**
		 * All the language codes that are available at present.
		 * @var array
		 */
		public $available_languages = array( 'de', 'en', 'es', 'fr' );

		/**
		 * How many fortunes should be generated?
		 * @var integer
		 */
		public $fortunes = 1;

		/**
		 * A list of the available cookie jars in key => description format.
		 *
		 * Note that the description (the array value) begins with the language of the fortunes.
		 * @var array
		 */
		public $available_jars = array(
			'Magic 8-Ball' => 'en: Magic 8-Ball',                            			// approx. 20 fortunes
			'amistad.fortunes' => 'es: amistad.fortunes',                    			// approx. 120 fortunes
			'art' => 'en: art',                                              			// approx. 464 fortunes
			'arte.fortunes' => 'es: arte.fortunes',                          			// approx. 370 fortunes
			'ascii-art' => 'en: ascii-art',                                  			// approx. 10 fortunes
			'asimov.fortunes' => 'es: asimov.fortunes',                      			// approx. 31 fortunes
			'bofh-excuses' => 'en: bofh-excuses',                            			// approx. 453 fortunes
			'ciencia.fortunes' => 'es: ciencia.fortunes',                    			// approx. 253 fortunes
			'computers' => 'en: computers',                                  			// approx. 1028 fortunes
			'cookie' => 'en: cookie',                                        			// approx. 1130 fortunes
			'de/anekdoten' => 'de: de/anekdoten',                            			// approx. 34 fortunes
			'de/asciiart' => 'de: de/asciiart',                              			// approx. 31 fortunes
			'de/bahnhof' => 'de: de/bahnhof',                                			// approx. 18 fortunes
			'de/beilagen' => 'de: de/beilagen',                              			// approx. 6 fortunes
			'de/brot' => 'de: de/brot',                                      			// approx. 3 fortunes
			'de/computer' => 'de: de/computer',                              			// approx. 154 fortunes
			'de/debian' => 'de: de/debian',                                  			// approx. 24 fortunes
			'de/dessert' => 'de: de/dessert',                                			// approx. 9 fortunes
			'de/elefanten' => 'de: de/elefanten',                            			// approx. 33 fortunes
			'de/gedichte' => 'de: de/gedichte',                              			// approx. 15 fortunes
			'de/hauptgericht' => 'de: de/hauptgericht',                      			// approx. 37 fortunes
			'de/holenlassen' => 'de: de/holenlassen',                        			// approx. 36 fortunes
			'de/huhn' => 'de: de/huhn',                                      			// approx. 27 fortunes
			'de/infodrom' => 'de: de/infodrom',                              			// approx. 589 fortunes
			'de/kalt' => 'de: de/kalt',                                      			// approx. 1 fortunes
			'de/kinderzitate' => 'de: de/kinderzitate',                      			// approx. 83 fortunes
			'de/kuchen' => 'de: de/kuchen',                                  			// approx. 7 fortunes
			'de/letzteworte' => 'de: de/letzteworte',                        			// approx. 209 fortunes
			'de/lieberals' => 'de: de/lieberals',                            			// approx. 99 fortunes
			'de/linuxtag' => 'de: de/linuxtag',                              			// approx. 615 fortunes
			'de/loewe' => 'de: de/loewe',                                    			// approx. 21 fortunes
			'de/mathematiker' => 'de: de/mathematiker',                      			// approx. 89 fortunes
			'de/ms' => 'de: de/ms',                                          			// approx. 238 fortunes
			'de/murphy' => 'de: de/murphy',                                  			// approx. 54 fortunes
			'de/namen' => 'de: de/namen',                                    			// approx. 480 fortunes
			'de/plaetzchen' => 'de: de/plaetzchen',                          			// approx. 1 fortunes
			'de/quiz' => 'de: de/quiz',                                      			// approx. 40 fortunes
			'de/regeln' => 'de: de/regeln',                                  			// approx. 306 fortunes
			'de/salat' => 'de: de/salat',                                    			// approx. 4 fortunes
			'de/sauce' => 'de: de/sauce',                                    			// approx. 3 fortunes
			'de/sicherheitshinweise' => 'de: de/sicherheitshinweise',        			// approx. 36 fortunes
			'de/sprichworte' => 'de: de/sprichworte',                        			// approx. 92 fortunes
			'de/sprichwortev' => 'de: de/sprichwortev',                      			// approx. 12 fortunes
			'de/sprueche' => 'de: de/sprueche',                              			// approx. 261 fortunes
			'de/stilblueten' => 'de: de/stilblueten',                        			// approx. 104 fortunes
			'de/suppe' => 'de: de/suppe',                                    			// approx. 1 fortunes
			'de/tips' => 'de: de/tips',                                      			// approx. 46 fortunes
			'de/translations' => 'de: de/translations',                      			// approx. 33 fortunes
			'de/unfug' => 'de: de/unfug',                                    			// approx. 1115 fortunes
			'de/vornamen' => 'de: de/vornamen',                              			// approx. 19 fortunes
			'de/vorspeise' => 'de: de/vorspeise',                            			// approx. 3 fortunes
			'de/warmduscher' => 'de: de/warmduscher',                        			// approx. 160 fortunes
			'de/witze' => 'de: de/witze',                                    			// approx. 883 fortunes
			'de/woerterbuch' => 'de: de/woerterbuch',                        			// approx. 239 fortunes
			'de/wusstensie' => 'de: de/wusstensie',                          			// approx. 129 fortunes
			'de/zitate' => 'de: de/zitate',                                  			// approx. 9459 fortunes
			'debian' => 'en: debian',                                        			// approx. 85 fortunes
			'debian-hints' => 'en: debian-hints',                            			// approx. 32 fortunes
			'definitions' => 'en: definitions',                              			// approx. 1202 fortunes
			'deprimente.fortunes' => 'es: deprimente.fortunes',              			// approx. 84 fortunes
			'disclaimer' => 'en: disclaimer',                                			// approx. 284 fortunes
			'drugs' => 'en: drugs',                                          			// approx. 208 fortunes
			'education' => 'en: education',                                  			// approx. 203 fortunes
			'ethnic' => 'en: ethnic',                                        			// approx. 161 fortunes
			'familia.fortunes' => 'es: familia.fortunes',                    			// approx. 144 fortunes
			'filosofia.fortunes' => 'es: filosofia.fortunes',                			// approx. 138 fortunes
			'food' => 'en: food',                                            			// approx. 198 fortunes
			'fortunes' => 'en: fortunes',                                    			// approx. 431 fortunes
			'fr/GDP' => 'fr: fr/GDP',                                        			// approx. 25 fortunes
			'fr/bd' => 'fr: fr/bd',                                          			// approx. 68 fortunes
			'fr/cinema' => 'fr: fr/cinema',                                  			// approx. 127 fortunes
			'fr/debian-fr' => 'fr: fr/debian-fr',                            			// approx. 59 fortunes
			'fr/droit' => 'fr: fr/droit',                                    			// approx. 27 fortunes
			'fr/gcu' => 'fr: fr/gcu',                                        			// approx. 102 fortunes
			'fr/gfa' => 'fr: fr/gfa',                                        			// approx. 152 fortunes
			'fr/glp' => 'fr: fr/glp',                                        			// approx. 113 fortunes
			'fr/gpj' => 'fr: fr/gpj',                                        			// approx. 73 fortunes
			'fr/haiku' => 'fr: fr/haiku',                                    			// approx. 16 fortunes
			'fr/humoristes' => 'fr: fr/humoristes',                          			// approx. 146 fortunes
			'fr/humour' => 'fr: fr/humour',                                  			// approx. 62 fortunes
			'fr/informatique' => 'fr: fr/informatique',                      			// approx. 23 fortunes
			'fr/linuxfr-undernet' => 'fr: fr/linuxfr-undernet',              			// approx. 69 fortunes
			'fr/litterature_etrangere' => 'fr: fr/litterature_etrangere',    			// approx. 106 fortunes
			'fr/litterature_francaise' => 'fr: fr/litterature_francaise',    			// approx. 827 fortunes
			'fr/mauriceetpatapon' => 'fr: fr/mauriceetpatapon',              			// approx. 269 fortunes
			'fr/multidesk' => 'fr: fr/multidesk',                            			// approx. 117 fortunes
			'fr/multidesk2' => 'fr: fr/multidesk2',                          			// approx. 97 fortunes
			'fr/mysoginie' => 'fr: fr/mysoginie',                            			// approx. 22 fortunes
			'fr/oulipo' => 'fr: fr/oulipo',                                  			// approx. 325 fortunes
			'fr/personnalites' => 'fr: fr/personnalites',                    			// approx. 36 fortunes
			'fr/philosophie' => 'fr: fr/philosophie',                        			// approx. 149 fortunes
			'fr/politique' => 'fr: fr/politique',                            			// approx. 45 fortunes
			'fr/proverbes' => 'fr: fr/proverbes',                            			// approx. 42 fortunes
			'fr/religion' => 'fr: fr/religion',                              			// approx. 83 fortunes
			'fr/sciences' => 'fr: fr/sciences',                              			// approx. 214 fortunes
			'fr/tolkien_fr' => 'fr: fr/tolkien_fr',                          			// approx. 28 fortunes
			'fr/tribune-linuxfr' => 'fr: fr/tribune-linuxfr',                			// approx. 168 fortunes
			'goedel' => 'en: goedel',                                        			// approx. 54 fortunes
			'humanos.fortunes' => 'es: humanos.fortunes',                    			// approx. 394 fortunes
			'humorists' => 'en: humorists',                                  			// approx. 197 fortunes
			'informatica.fortunes' => 'es: informatica.fortunes',            			// approx. 147 fortunes
			'kids' => 'en: kids',                                            			// approx. 150 fortunes
			'knghtbrd' => 'en: knghtbrd',                                    			// approx. 540 fortunes
			'lao-tse.fortunes' => 'es: lao-tse.fortunes',                    			// approx. 11 fortunes
			'law' => 'en: law',                                              			// approx. 201 fortunes
			'leydemurphy.fortunes' => 'es: leydemurphy.fortunes',            			// approx. 372 fortunes
			'libertad.fortunes' => 'es: libertad.fortunes',                  			// approx. 176 fortunes
			'linux' => 'en: linux',                                          			// approx. 335 fortunes
			'linuxcookie' => 'en: linuxcookie',                              			// approx. 103 fortunes
			'literature' => 'en: literature',                                			// approx. 262 fortunes
			'love' => 'en: love',                                            			// approx. 150 fortunes
			'magic' => 'en: magic',                                          			// approx. 30 fortunes
			'medicine' => 'en: medicine',                                    			// approx. 74 fortunes
			'men-women' => 'en: men-women',                                  			// approx. 581 fortunes
			'miscellaneous' => 'en: miscellaneous',                          			// approx. 651 fortunes
			'news' => 'en: news',                                            			// approx. 53 fortunes
			'nietzsche.fortunes' => 'es: nietzsche.fortunes',                			// approx. 82 fortunes
			'off/art' => 'en: off/art',                                      			// approx. 1 fortunes
			'off/astrology' => 'en: off/astrology',                          			// approx. 42 fortunes
			'off/atheism' => 'en: off/atheism',                              			// approx. 2167 fortunes
			'off/black-humor' => 'en: off/black-humor',                      			// approx. 270 fortunes
			'off/camioneros.fortunes.u8' => 'es: off/camioneros.fortunes.u8',			// approx. 521 fortunes
			'off/cookie' => 'en: off/cookie',                                			// approx. 6 fortunes
			'off/debian' => 'en: off/debian',                                			// approx. 21 fortunes
			'off/definitions' => 'en: off/definitions',                      			// approx. 333 fortunes
			'off/drugs' => 'en: off/drugs',                                  			// approx. 73 fortunes
			'off/ethnic' => 'en: off/ethnic',                                			// approx. 127 fortunes
			'off/feministas.fortunes.u8' => 'es: off/feministas.fortunes.u8',			// approx. 101 fortunes
			'off/fortunes' => 'en: off/fortunes',                            			// approx. 2 fortunes
			'off/hphobia' => 'en: off/hphobia',                              			// approx. 40 fortunes
			'off/knghtbrd' => 'en: off/knghtbrd',                            			// approx. 13 fortunes
			'off/limerick' => 'en: off/limerick',                            			// approx. 993 fortunes
			'off/linux' => 'en: off/linux',                                  			// approx. 8 fortunes
			'off/machosno.fortunes.u8' => 'es: off/machosno.fortunes.u8',    			// approx. 25 fortunes
			'off/misandry' => 'en: off/misandry',                            			// approx. 24 fortunes
			'off/miscellaneous' => 'en: off/miscellaneous',                  			// approx. 64 fortunes
			'off/misogino.fortunes.u8' => 'es: off/misogino.fortunes.u8',    			// approx. 237 fortunes
			'off/misogyny' => 'en: off/misogyny',                            			// approx. 60 fortunes
			'off/politics' => 'en: off/politics',                            			// approx. 349 fortunes
			'off/privates' => 'en: off/privates',                            			// approx. 131 fortunes
			'off/proverbios.fortunes.u8' => 'es: off/proverbios.fortunes.u8',			// approx. 242 fortunes
			'off/racism' => 'en: off/racism',                                			// approx. 4 fortunes
			'off/religion' => 'en: off/religion',                            			// approx. 229 fortunes
			'off/riddles' => 'en: off/riddles',                              			// approx. 269 fortunes
			'off/sex' => 'en: off/sex',                                      			// approx. 808 fortunes
			'off/songs-poems' => 'en: off/songs-poems',                      			// approx. 206 fortunes
			'off/varios.fortunes.u8' => 'es: off/varios.fortunes.u8',        			// approx. 86 fortunes
			'off/vulgarity' => 'en: off/vulgarity',                          			// approx. 194 fortunes
			'off/zippy' => 'en: off/zippy',                                  			// approx. 548 fortunes
			'paradoxum' => 'en: paradoxum',                                  			// approx. 72 fortunes
			'people' => 'en: people',                                        			// approx. 1244 fortunes
			'perl' => 'en: perl',                                            			// approx. 273 fortunes
			'pets' => 'en: pets',                                            			// approx. 52 fortunes
			'pintadas.fortunes' => 'es: pintadas.fortunes',                  			// approx. 238 fortunes
			'platitudes' => 'en: platitudes',                                			// approx. 500 fortunes
			'poder.fortunes' => 'es: poder.fortunes',                        			// approx. 337 fortunes
			'politics' => 'en: politics',                                    			// approx. 700 fortunes
			'proverbios.fortunes' => 'es: proverbios.fortunes',              			// approx. 295 fortunes
			'refranes.fortunes' => 'es: refranes.fortunes',                  			// approx. 4989 fortunes
			'riddles' => 'en: riddles',                                      			// approx. 132 fortunes
			'sabiduria.fortunes' => 'es: sabiduria.fortunes',                			// approx. 730 fortunes
			'schopenhauer.fortunes' => 'es: schopenhauer.fortunes',          			// approx. 13 fortunes
			'science' => 'en: science',                                      			// approx. 624 fortunes
			'sentimientos.fortunes' => 'es: sentimientos.fortunes',          			// approx. 572 fortunes
			'songs-poems' => 'en: songs-poems',                              			// approx. 720 fortunes
			'sports' => 'en: sports',                                        			// approx. 147 fortunes
			'startrek' => 'en: startrek',                                    			// approx. 227 fortunes
			'translate-me' => 'en: translate-me',                            			// approx. 12 fortunes
			'varios.fortunes' => 'es: varios.fortunes',                      			// approx. 597 fortunes
			'varios.fortunes-pre' => 'es: varios.fortunes-pre',              			// approx. 23 fortunes
			'verdad.fortunes' => 'es: verdad.fortunes',                      			// approx. 153 fortunes
			'vida.fortunes' => 'es: vida.fortunes',                          			// approx. 392 fortunes
			'wisdom' => 'en: wisdom',                                        			// approx. 422 fortunes
			'work' => 'en: work',                                            			// approx. 630 fortunes
			'zippy' => 'en: zippy',                                          			// approx. 548 fortunes
		);

		/**
		 * The jars from $available_jars that you want included.
		 * @var array
		 */
		public $jars = array(
			'riddles',
			'politics',
			'startrek',
		);

		public function get_url ( ) {

			$options = array(
				'limit' => $this->long_fortune_length,
				'cookies' => $this->fortunes,
				'format' => 'rss',
				'long' => (int)$this->display_long_fortunes,
				'offensive' => (int)$this->display_offensive_fortunes,
			);

			// we have to handle the arrays on our own, unfortunately
			$options['jar'] = implode( ',', $this->jars );
			$options['lang'] = implode( ',', $this->languages );

			$query = http_build_query( $options );

			$url = self::BASE_URL . '?' . $query;

			return $url;

		}

		public static function factory ( ) {
			$class = __CLASS__;

			return new $class();
		}

		public function fortunes ( $fortunes ) {
			$this->fortunes = $fortunes;

			return $this;
		}

		public function languages ( $languages ) {
			$this->languages = $languages;

			return $this;
		}

		public function jars ( $jars ) {
			$this->jars = $jars;

			return $this;
		}

		public function display_offensive ( $offensive ) {
			$this->display_offensive_fortunes = $offensive;

			return $this;
		}

		public function display_long ( $display_long ) {
			$this->display_long_fortunes = $display_long;

			return $this;
		}

		public function long_length ( $limit ) {
			$this->long_length = $limit;

			return $this;
		}

		/**
		 * Generates the URL, fetches the RSS feed, and parses out fortunes into objects with two properties:
		 *    text: the text of the fortune
		 *    jar: the cookie jar it came from
		 * @param  boolean $single If true, returns a single object for the first fortune, rather than an array of fortune objects.
		 * @return object|array          Either a single instance of stdClass (if $single is true) or an array of stdClass objects (if $single is false).
		 */
		public function get_fortunes ( $single = true ) {

			$url = $this->get_url();

			$contents = file_get_contents( $url );

			$dom = new DOMDocument();
			@$dom->loadXML( $contents );

			$xpath = new DOMXpath( $dom );
			$items = $xpath->query( './/item' );

			$fortunes = array();
			foreach ( $items as $item ) {
				$title = $xpath->query( './title', $item );
				$description = $xpath->query( './description', $item );

				if ( $title->length < 1 || $description->length < 1 ) {
					continue;
				}

				$fortune = new stdClass();
				$fortune->text = $title->item(0)->nodeValue;
				$fortune->jar = $description->item(0)->nodeValue;

				$fortunes[] = $fortune;
			}

			// if we only want to return a single value, do that now
			if ( $single ) {
				return reset( $fortunes );
			}
			else {
				return $fortunes;
			}

		}

	}

?>
