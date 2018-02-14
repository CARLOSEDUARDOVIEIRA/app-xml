<?php
/**
 * Class conexao com bd e insert de dados.
 *
 * @author Carlos Eduardo Vieira
 */

class Database {
       
    private $pdo;

    public function __construct(){
        $this->pdo = new PDO('mysql:host=localhost;dbname=app-xml', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 

    public function insertDB($objProdutos) {
        try {            

            date_default_timezone_set('America/Sao_Paulo');            
                    
            foreach ($objProdutos as $obj) {
                $stmt = $this->pdo->prepare("SELECT * FROM produtos where codigo = ? ");
                $stmt->execute(array($obj->codigo));                    

                if($stmt->fetch()){
                    $stmt = $this->pdo->prepare("UPDATE produtos SET descricao = ?, fornecedor = ?, preco = ?, datahoraatualizacao = ?  WHERE codigo = ?");                                                        
                } else{                   
                    $stmt = $this->pdo->prepare('INSERT INTO produtos (descricao, fornecedor, preco, datahoraatualizacao, codigo) VALUES(?,?,?,?,?)');
                }

                $stmt->bindParam(1, htmlspecialchars_decode($obj->descricao));
                $stmt->bindParam(2, $obj->fornecedor);
                $stmt->bindParam(3, $obj->preco);
                $stmt->bindParam(4, date('Y-m-d H:i'));
                $stmt->bindParam(5, $obj->codigo);

                $stmt->execute();                
                $stmt->rowCount();  
            }
            
             
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

}
