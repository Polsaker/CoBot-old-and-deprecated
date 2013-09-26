<?php
/*
 * @name: Google Libros
 * @desc: Busca libros en Google
 * @ver: 1.0
 * @author: MRX
 * @id: booksearch
 * @key: polsakerrulz
 *
 */

class polsakerrulz{
	public function __construct(&$core){
		$core->registerCommand("book", "booksearch", "Busca libros en Google Books. Sintaxis: book <Libro>");
	}
	
	public function book(&$irc, &$data, &$core){
			$ts=$core->jparam($data->messageex,1);
			if(preg_match("#.*isbn\:.*#",$ts,$m)){
				$ts=str_replace("-","",$ts);
			}
			$gap=file_get_contents("https://www.googleapis.com/books/v1/volumes?country=AR&q=".urlencode($ts)."&key=".$core->conf['m_google']['api_key']);
			$jao=json_decode($gap);
			if($jao->totalItems==0){$resp="No se encontraron resultados..";}else{
				$i=0; 
				while(($i<3) && (@$jao->items[$i])){
					@$resp="".$jao->items[$i]->volumeInfo->title.", Autor: ".$jao->items[$i]->volumeInfo->authors[0].", ".$jao->items[$i]->volumeInfo->pageCount." páginas. ISBN-10: ".$jao->items[$i]->volumeInfo->industryIdentifiers[$i]->identifier.", ISBN-13 ".$jao->items[$i]->volumeInfo->industryIdentifiers[1]->identifier.". 10http://books.google.com.mx/books?id=".$jao->items[$i]->id."";
					$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $resp);
					$i++;
				}
				return 0;
			}
			
			$irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $resp);
	}	
}
