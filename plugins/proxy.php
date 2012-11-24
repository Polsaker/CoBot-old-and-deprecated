<?php
//$re=checkproxy($_GET['ip']);
//if($re==1){echo "ES UN PUTO PROXY!!!";}else{echo "no es un proxy";}
//checkproxy("127.0.0.1");
function checkproxy($ip){
	$request = "GET http://google.com/ HTTP/1.1\r\nHost: google.com\r\nConnection: close\r\nUser-Agent: user_agent\r\nAccept-Encoding: *\r\n";
	$request.= "Accept-Charset: ISO-8859-1,UTF-8;q=0.7,*;q=0.7\r\nCache-Control: no\r\nAccept-Language: de,en;q=0.7,en-us;q=0.3\r\n\r\n";
	$Ports = array('1080', '8080', '8000', '3128', '8888', '8081'); 	// To hold the list of ports.

	$x = 0;

	while (@$Ports[$x])
	{
		@$fSockPointer = fsockopen($ip, $Ports[$x], $errno, $errstr, 5);
		if ($fSockPointer)
		{
			fwrite($fSockPointer, $request, strlen($request));
			while(!@feof($fSockPointer)){
				$r= fgets($fSockPointer, 1024); 
				if(($r!="")&&(@isset($r))){return 1;}
			}
			fclose($fSockPointer);
		}
		$x++;
	}
}
?>
