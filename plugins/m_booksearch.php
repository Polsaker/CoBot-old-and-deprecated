<?php 
	/*
	 * m_google.php
	 * Agrega el comando google.
	 */
	 $name="booksearch"; 
	$key="ee111t1t1172";
class ee111t1t1172{
		public $help;
		public function __construct(&$irc){	
			$irc->addcmd($this, 'book', 'booksearch');	
			$this->help['book']='Busca un libro en Google Books';
		}

		public function book(&$irc,$msg,$channel,$param,$who)
		{
			$i=1;
			$ts="";
			while(@isset($param[$i])){
				$ts.=$param[$i]. " ";
				$i++;
			}
			$ts=substr($ts,0,strlen($ts)-1);
			if(preg_match("#.*isbn\:.*#",$ts,$m)){
				$ts=str_replace("-","",$ts);
			}
			$gap=file_get_contents("https://www.googleapis.com/books/v1/volumes?country=AR&q=".urlencode($ts)."&key=".$irc->conf['m_google']['api_key']);
			$jao=json_decode($gap);
			if($jao->totalItems==0){$resp="No se encontraron resultados..";}else{
				$i=0; 
				while(($i<3) && (@$jao->items[$i])){
					@$resp="".$jao->items[$i]->volumeInfo->title.", Autor: ".$jao->items[$i]->volumeInfo->authors[0].", ".$jao->items[$i]->volumeInfo->pageCount." páginas. ISBN-10: ".$jao->items[$i]->volumeInfo->industryIdentifiers[$i]->identifier.", ISBN-13 ".$jao->items[$i]->volumeInfo->industryIdentifiers[1]->identifier.". 10http://books.google.com.mx/books?id=".$jao->items[$i]->id."";
					$irc->SendCommand("PRIVMSG ".$channel." :".$resp);
					$i++;
				}
				return 0;
			}
			
			$irc->SendCommand("PRIVMSG ".$channel." :".$resp);
		}
	}
?>
