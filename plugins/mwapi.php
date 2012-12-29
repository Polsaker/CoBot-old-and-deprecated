<?php
  /*
   Basado en http://en.wikipedia.org/wiki/User:LivingBot/Wikibot. Optimizado a mi gusto.
   */
 
  class mwApi {
      public $editdetails;
      public $wiki;
      public $max_lag;
      public $username;
      private $ch;
      public function __construct($username, $password, $wiki, $useragent="PHP Bot", $lag = 5){
          if (!isset($username) || !isset($password)) {return -2;} 
          // Se inicia la configuración
          $this->wiki = $wiki;
          $this->max_lag = $lag;
          $this->username = $username;
          $this->useragent = $useragent;
          // Se inicia curl
          $this->ch=curl_init();
          curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
          curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
          curl_setopt($this->ch, CURLOPT_USERAGENT, $this->useragent);
          curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

          if($this->login($username, $password)==-1){return -1;}
      }
      private function login($username, $password) {
          $response = $this->postAPI('action=login&lgname=' . urlencode($username) . '&lgpassword=' . urlencode($password),"php");
          if ($response['login']['result'] == "NeedToken") {
              $token = $response['login']['token'];
              $newresponse = $this->postAPI('action=login&lgname=' . urlencode($username) . '&lgpassword=' . urlencode($password) . '&lgtoken=' . $token,"php");
              if ($newresponse['login']['result'] != "Success") {
                  sleep(10);
                  $this->login($username, $password);
              }
          }else{
              if (isset($response['login']['wait']) || (isset($response['error']['code']) && $response['error']['code'] == "maxlag")) {
                  sleep(10);
                  $this->login($username, $password);
              } else {
                  return -1;
              }
          }
      }
      
      // Funcion para obtener los tokens necesarios para realizar ciertas acciones.
        # Pendiente: Patroll y userrights
      public function get_token($intoken,$page=""){
		if($intoken=="rollback"){ // Token para rollback
			$response = $this->callAPI("api.php?action=query&prop=revisions&titles=$page&rvtoken=rollback");
			//print_r($response);
			foreach($response['query']['pages'] as $key => $val){$token = $val['revisions'][0]['rollbacktoken'];}
		}elseif($intoken=="undelete"){ // Token para undelete
			$response = $this->callAPI("api.php?action=query&list=deletedrevs&drprop=token");
			$token=$response['query']['deletedrevs'][0]['token'];
		}else{ // El resto de los tokens
			$response = $this->callAPI("api.php?action=tokens&type=$intoken");
			$token = $response["tokens"][$intoken."token"];
		}
		return urlencode($token);

	  }

      public function callAPI($url, $format = "php") {
          $wiki = $this->wiki;
          curl_setopt($this->ch, CURLOPT_POST, false);
          curl_setopt($this->ch, CURLOPT_URL, ($wiki . $url . "&maxlag=" . $this->max_lag . "&format=$format"));
          $response = curl_exec($this->ch);
          if (curl_errno($this->ch)) {
              return curl_error($this->ch);
          }
          if($format=="php"){return unserialize($response);}else{return $response;}
      }
      
      public function postAPI($postdata = "",$format="json") {
          $wiki = $this->wiki;
          $url = $wiki . 'api.php';
          if ($postdata !== "") {
              $postdata .= "&";
          }
          $postdata .= "format=$format&maxlag=" . $this->max_lag;
          curl_setopt($this->ch, CURLOPT_URL, $url);
          curl_setopt($this->ch, CURLOPT_POST, 1);
          curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postdata);
          curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'));
          curl_setopt($this->ch, CURLOPT_HEADER, false);
          $response = curl_exec($this->ch);
          if (curl_errno($this->ch)) {
              return curl_error($this->ch);
          }
          if($format=="php"){return unserialize($response);}else{return $response;}
      }
  }
