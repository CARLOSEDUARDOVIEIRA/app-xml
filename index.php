<?php


downloadZipFile('http://testedev.baixou.com.br/processo/zip', 'file.zip');

 function downloadZipFile($url, $filepath){
     $fp = fopen($filepath, 'w+');
     $ch = curl_init($url);

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
     curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); 
     curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");     
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
     curl_setopt($ch, CURLOPT_FILE, $fp);
     curl_exec($ch);

     curl_close($ch);
     fclose($fp);

     return (filesize($filepath) > 0)? true : false;
 }

unzip();

function unzip(){
    $zip = new ZipArchive;
    if ($zip->open('file.zip') === TRUE) {
        $zip->extractTo('./');
        $zip->close();        
    } else {
        echo 'failed';
    }
}

readXml();

function readXml(){
    $out = array();
    if (file_exists('0303.xml')) {
        
    $string = file_get_contents('0303.xml');

    $objProdutos = new ArrayObject();

    $string = str_replace(array('<Descricao>','</Descricao>'), array('<Descricao><![CDATA[', ']]></Descricao>'), $string);
    $string = str_replace('&', '&amp;', $string);
    
    $src = new DOMDocument('1.0');
    $src->formatOutput = true;
    $src->preserveWhiteSpace = false;
    $src->loadXML($string);
    $books = $src->getElementsByTagName('produto');
    foreach ($books as $i => $book) {
        $produto = new stdClass();
        
        $produto->codigo = formatar( $src->getElementsByTagName('Reduzido')->item($i)->nodeValue );
        $produto->descricao = formatar( $src->getElementsByTagName('Descricao')->item($i)->nodeValue );
        $produto->fornecedor = formatar( $src->getElementsByTagName('Fornecedor')->item($i)->nodeValue );
        $produto->preco = formatar( $src->getElementsByTagName('PrecoPor')->item($i)->nodeValue );                        
        
        $objProdutos->append( $produto ); 
    }

        echo '<pre>';
        // print_r(utf8_encode($objProdutos));
        print_r($objProdutos);
        echo '</pre>';
      
        
    } else {
        exit("Arquivo recebido não é o esperado '0303.xml', execute novamente o script");
    }
}

    function formatar($tag){        
        return mb_substr(str_replace(array('<', '>'), array('&lt;', '&gt;'), $tag), 0, 335);
    }
