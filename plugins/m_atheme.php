<?php 
	/* Integración con Atheme-Services */
	 $name="atheme"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	private $irc;
	public function __construct(&$irc){	
		/* ... */
		$this->irc=$irc;
	}

	public function cs_op($chan,$user){$this->irc->SendPriv("ChanServ","OP $chan $user");}
	public function cs_deop($chan,$user){
		if($this->irc->is_loaded("protect")){
			$pclass=$this->irc->get_class("protect");
			$pclass->ex=true;//sleep(3);
		}
		$this->irc->SendPriv("ChanServ","DEOP $chan $user");
	}
}
