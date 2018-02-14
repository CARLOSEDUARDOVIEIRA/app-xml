<?php

include('bd/Database.class.php');

function downloadZipFile($url, $filepath){
     $fp = fopen($filepath, 'w+');
     $ch = curl_init( $url );

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
     curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); 
     curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");     
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
     curl_setopt($ch, CURLOPT_FILE, $fp);
     curl_exec($ch);

     curl_close($ch);
     fclose($fp);

     return (filesize( $filepath ) > 0)? true : false;
 }

function unzip(){
    $zip = new ZipArchive;
    if ($zip->open('file.zip') === TRUE) {
        $zip->extractTo('./');
        $zip->close();        
    } else {
        echo 'failed';
    }
}

function readXml(){
    
    if (file_exists('0303.xml')) {
        
        $string = file_get_contents('0303.xml');
        $objProdutos = new ArrayObject();

        $string = str_replace(array('<Descricao>','</Descricao>'), array('<Descricao><![CDATA[', ']]></Descricao>'), $string);
        $string = str_replace('&', '&amp;', $string);

        $src = new DOMDocument('1.0');
        $src->formatOutput = true;
        $src->preserveWhiteSpace = false;
        $src->loadXML($string);
        $xmlprodutos = $src->getElementsByTagName('produto');
        
        foreach ($xmlprodutos as $i => $xml) {
            $produto = new stdClass();
            
            $produto->codigo = formatar( $src->getElementsByTagName('Reduzido')->item($i)->nodeValue );
            $produto->descricao = formatar( $src->getElementsByTagName('Descricao')->item($i)->nodeValue );
            $produto->fornecedor = formatar( $src->getElementsByTagName('Fornecedor')->item($i)->nodeValue );
            $produto->preco = formatar( $src->getElementsByTagName('PrecoPor')->item($i)->nodeValue );                        
            
            $objProdutos->append( $produto ); 
        }
        
        $database = new Database();
        $database->insertDB( $objProdutos );
        
        //Mantive o print_r para apresentar os dados do xml objetos na tela
        echo '<pre>';        
        print_r( $objProdutos );
        echo '</pre>';        
            
        } else {
            exit("Arquivo recebido não é o esperado '0303.xml', execute novamente o script");
        }
}

function formatar( $tag ){        
    return mb_substr(str_replace(array('<', '>'), array('&lt;', '&gt;'), $tag), 0, 335);
}

function removeDownload(){
    unlink('file.zip');
    unlink('0303.xml');
}

downloadZipFile('http://testedev.baixou.com.br/processo/zip', 'file.zip');
unzip();
readXml();
removeDownload();