<?php 
	/*
	 * m_hash.php
	 * Funciones para hashes.
	 */
	 $name="hash"; 
	$key="ee111t1t1172";
class ee111t1t1172{
	public $help;
	public function __construct(&$irc){	
		$irc->addcmd($this, 'hash', 'hash');	
		$this->help['hash']='Encripta una cadena usando un hash. Sintaxis: hash <hash> <cadena>. Hashes disponibles: md5, sha1, md2, md4, sha224, sha256, sha384, sha512, ripemd128, ripemd160, ripemd256, ripemd320, whirlpool, snefru, snefru256, gost, adler32, crc32, crc32b, salsa10, salsa20 También esta hex2str y str2hex (no son hashes!!)';
	}

	public function hash(&$irc,$msg,$channel,$param,$who)
	{
		if(!@isset($param[2])){$irc->SendCommand("PRIVMSG $channel :\00305Error\003: Faltan parรกmetros!");return 0;}
		$ts=$irc->jparam($param,2);
		switch($param[1]){
			case "md5":	$hashed=md5($ts); break;
			case "sha1": $hashed=sha1($ts); break;
			case "md2": $hashed=hash("md2",$ts); break;
			case "md4": $hashed=hash("md4",$ts); break;
			case "sha224": $hashed=hash("sha224",$ts); break;
			case "sha256": $hashed=hash("sha256",$ts); break;
			case "sha384": $hashed=hash("sha384",$ts); break;
			case "sha512": $hashed=hash("sha512",$ts); break;
			case "ripemd128": $hashed=hash("ripemd128",$ts); break;
			case "ripemd160": $hashed=hash("ripemd160",$ts); break;
			case "ripemd256": $hashed=hash("ripemd256",$ts); break;
			case "ripemd320": $hashed=hash("ripemd320",$ts); break;
			case "whirlpool": $hashed=hash("whirlpool",$ts); break;
			case "snefru": $hashed=hash("snefru",$ts); break;
			case "snefru256": $hashed=hash("snefru256",$ts); break;
			case "gost": $hashed=hash("gost",$ts); break;
			case "adler32": $hashed=hash("adler32",$ts); break;
			case "crc32": $hashed=hash("crc32",$ts); break;
			case "crc32b": $hashed=hash("crc32b",$ts); break;
			case "salsa10": $hashed=hash("salsa10",$ts); break;
			case "salsa20": $hashed=hash("salsa20",$ts); break;
			case "str2hex": $hashed= strtoupper(bin2hex($ts)); break;
			case "hex2str": $hashed= $this->hexToStr($ts); break;
		}
		$irc->SendCommand("PRIVMSG $channel :$hashed");
	}
private function hexToStr($hex)
{
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2)
    {
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}


}
	?>
