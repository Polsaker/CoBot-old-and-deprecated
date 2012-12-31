<?php
	/*
	 *  MW Api V2. Por Mr. X
	 * 
	 * Clase para trabajar con el API de MediaWiki.
	 */
	 
	class mwApi{
		private $wiki;
		private $ch;
		 
		public function __construct($user,$password,$wiki,$useragent="PHP Bot"){
			if(!isset($user) || !isset($password) || !isset($wiki)){return -1;}
			 
			$this->wiki = $wiki;
			
			$this->ch=curl_init();
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookiefile');
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookiefile');
			curl_setopt($this->ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
			
			$r = $this->login($user,$password);
		}
		
		private function login($user,$pass){
			$r=$this->callApi("action=login&lgname=".urlencode($user)."&lgpassword=".urlencode($pass),false);
			if(!$r){return -1;}
			
			if($r["login"]["result"]=="NeedToken"){
				$nr=$this->callApi("action=login&lgname=".urlencode($user)."&lgpassword=".urlencode($pass)."&lgtoken=".urlencode($r["login"]["token"]),false);
				if($nr["login"]["result"]!="Success"){return -2;}
			}
		}
		
		public function get_token($intoken,$page=""){
			if($intoken=="rollback"){
				$response = $this->callApi("action=query&prop=revisions&titles=$page&rvtoken=rollback");
				foreach($response['query']['pages'] as $key => $val){$token = $val['revisions'][0]['rollbacktoken'];}
			}elseif($intoken=="undelete"){ 
				$response = $this->callApi("action=query&list=deletedrevs&drprop=token");
				$token=$response['query']['deletedrevs'][0]['token'];
			}else{
				$response = $this->callApi("action=tokens&type=$intoken");
				$token = $response["tokens"][$intoken."token"];
			}
			return urlencode($token);
		}
		
		// CallApi: Llama a la api para realizar una función.
		// Parametros:
		  # URL: los parametros enviados al API, sin el nombre del archivo. Ejemplo: "action=query&list=recentchanges"
		  # get: 1 si usa get, 0 si usa post
		public function callApi($url,$get=1,$format="php"){
			$url=$url."&format=$format";
			if($get==1){
				$addr=$this->wiki."/api.php?".$url;
				curl_setopt($this->ch, CURLOPT_POST, false);
				curl_setopt($this->ch, CURLOPT_URL, $addr);
				$r = curl_exec($this->ch);
			}else{
				curl_setopt($this->ch, CURLOPT_URL, $this->wiki."/api.php");
				curl_setopt($this->ch, CURLOPT_POST, true);
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, $url);
				curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'));
				curl_setopt($this->ch, CURLOPT_HEADER, false);
				$r = curl_exec($this->ch);
			}
			if (curl_errno($this->ch)) {
				return curl_error($this->ch);
			}
			if($format=="php"){return unserialize($r);}elseif($format=="json"){return json_decode($r);}
		}
	} 
?>
