<?php 
	/*
	 * m_google.php
	 * Agrega el comando google.
	 */
	 $name="protect"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public $ex;
		private $ch=array();
		public function __construct(&$irc){	
			if(!@isset($irc->hdf['MODE'])){$irc->hdf['MODE']=array();}
			array_push($irc->hdf['MODE'],array("protect","modeprot"));
			if(!@isset($irc->hdf['474'])){$irc->hdf['474']=array();}
			array_push($irc->hdf['474'],array("protect","banprot"));
			if(!@isset($irc->hdf['473'])){$irc->hdf['473']=array();}
			array_push($irc->hdf['473'],array("protect","invprot"));
			
		}

		public function modeprot(&$irc,$txt){
			//:.+ MODE (.+) -o CoBot
			$txt=strip_tags(trim($txt), '\n\t\r\h\v\0');  
		//	echo ".............:". $txt;
			if (preg_match('#^:.+ MODE (.+) -o.* '.$irc->nick.'$#',$txt, $matches)){
				if($this->ex!=true){$irc->SendCommand("PRIVMSG ChanServ :OP ".$matches[1]);}else{$this->ex==true;}
			}

		}
		
		public function banprot(&$irc,$txt){
			if (preg_match('#^:.+ 474 '.$irc->nick.' (.+) :.+#',$txt, $matches)){
				if(!@isset($this->ch[$matches[1]])){$this->ch[$matches[1]]=0;}
				if($this->ch[$matches[1]]<10){
					$irc->SendCommand("PRIVMSG ChanServ :UNBAN ".$matches[1]);
					$irc->SendCommand("JOIN ".$matches[1]);
					$this->ch[$matches[1]]++;
				}
			}
		}
		
		public function invprot(&$irc,$txt){
			if (preg_match('#^:.+ 473 '.$irc->nick.' (.+) :.+#',$txt, $matches)){
				if(!@isset($this->ch[$matches[1]])){$this->ch[$matches[1]]=0;}
				if($this->ch[$matches[1]]<10){
					$irc->SendCommand("PRIVMSG ChanServ :INVITE ".$matches[1]);
					$irc->SendCommand("JOIN ".$matches[1]);
					$this->ch[$matches[1]]++;
				}
			}
		}
		
		
	}
?>
