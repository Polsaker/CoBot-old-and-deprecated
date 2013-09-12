<?php 

	 $name="translate"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'traducir', 'translate', array("translate", "trans", "tr","trad"));	
			$this->help['traducir']='Traduce un texto. Sintaxis: traducir <DE> <A> <TEXTO> (<DE> y <A> deben ser códigos de idiomas, Escriba "auto" en <DE> para autodetectar el idioma o en <A> para traducir directamente al español)';
			include("plugins/translate.php");
		}

		public function traducir(&$irc,$msg,$channel,$param,$who)
		{
			if(!@isset($param[3])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Faltan parametros."); return 0; }
			$i=3;
			$ts="";
			while(@isset($param[$i])){
				$ts.=$param[$i]. " ";
				$i++;
			}
			$ts=substr($ts,0,strlen($ts)-1);

			$clientID     = $irc->conf['m_translate']['cid'];
			$clientSecret = $irc->conf['m_translate']['cs'];
			$authUrl      = $irc->conf['m_translate']['authurl'];
			$scopeUrl     = $irc->conf['m_translate']['scopeurl'];
			$grantType    = $irc->conf['m_translate']['granttype'];
			$locale = 'es';
			
			$authObj      = new AccessTokenAuthentication();
			$translatorObj = new HTTPTranslator();
			
			$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
			$authHeader = "Authorization: Bearer ". $accessToken;
			
			$getLanguageNamesurl = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguageNames?locale=$locale";
				
				
				if($param[1]=="auto"){
					$detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($ts);
					$strResponse = $translatorObj->curlRequest($detectMethodUrl, $authHeader);
					$xmlObj = simplexml_load_string($strResponse);
					foreach((array)$xmlObj[0] as $val){ $param[1] = $val; }
				}
				if($param[2]=="auto"){
					$param[2]="es";
				}
				
				$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
				$authHeader = "Authorization: Bearer ". $accessToken;
				$requestXml = $translatorObj->createReqXML($param[1]);
				$curlResponse = $translatorObj->curlRequest($getLanguageNamesurl, $authHeader, $requestXml);
				$xmlObj = simplexml_load_string($curlResponse);
				
				$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
				$authHeader = "Authorization: Bearer ". $accessToken;
				$requestXml = $translatorObj->createReqXML($param[2]);
				$curlResponse = $translatorObj->curlRequest($getLanguageNamesurl, $authHeader, $requestXml);
				$xmlObj2 = simplexml_load_string($curlResponse);
				
			$resp="Traduciendo del $xmlObj->string al $xmlObj2->string: ";
			
			$TranslatorUrl = "http://api.microsofttranslator.com/v2/Http.svc/Translate?text=".urlencode($ts)."&from=".urlencode($param[1])."&to=".urlencode($param[2]);
			$strResponse = $translatorObj->curlRequest($TranslatorUrl, $authHeader);
						
			$resp.=strip_tags($strResponse);
			$resp = str_replace(array("\r","\n","\r\n","",$resp));
			$irc->SendCommand("PRIVMSG $channel :$resp");
		}
	}
?>
