<?php 

	 $name="rae"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'rae', 'rae',array("palabra","real","drae"));	
		$this->help['rae']='Busca una palabra en el diccionario de la RAE';
	}

	public function rae(&$irc,$msg,$channel,$param,$who){
		if(!isset($param[1])){$irc->SendPriv($channel,"03Error: Faltan parámetros");return 0;}
		$f=file_get_contents("http://rae-quel.appspot.com/json?query=".strtolower($param[1]));
		$js=json_decode($f);$div="Definiciones encontradas de la palabra \"".strtolower($param[1])."\": ";
		foreach($js as $key=>$val){
			$div.="\"$val\", ";
		}
		
		$irc->SendPriv($channel,$div,true,400, ", ");
	}
}
?>
