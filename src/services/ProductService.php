<?php


namespace services;


use models\Book;
use models\DVD;
use models\Furniture;
use models\Product;
class ProductService {
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getProducts() {
        $stmt = $this->conn->prepare(
            'SELECT P.sku,P.name,P.price,D.size,PT.type_name from products P
            INNER JOIN dvd D ON P.sku=D.sku
            LEFT JOIN product_types PT ON P.type_id=PT.type_id '
        );
        $stmt->execute();
        $dvds = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->conn->prepare('
            SELECT P.sku,P.name,P.price,F.width,F.length,F.height,PT.type_name from products P
            INNER JOIN furniture F ON P.sku=F.sku
            LEFT JOIN product_types PT ON P.type_id=PT.type_id
                                        ');
        $stmt->execute();
        $furnitures = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->conn->prepare('SELECT P.sku,P.name,P.price,B.weight,PT.type_name from products P
                                        INNER JOIN book B ON P.sku=B.sku
                                        LEFT JOIN product_types PT ON P.type_id=PT.type_id
                                        ');
        $stmt->execute();
        $books = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $arr = array_merge($dvds,$furnitures,$books);
        return json_encode($arr);
    }

    public function deleteProducts($skus) {
        $skus = implode("','", $skus);
        $skus = str_replace(',',"','",$skus);
        $skus = str_replace('awsapiisrc',"",$skus);
        $skus = str_replace('src',"",$skus);
        echo "DELETE FROM products WHERE sku IN ('".$skus."')";
        $stmt = $this->conn->prepare("DELETE FROM products WHERE sku IN ('".$skus."')");
        $stmt->execute();
    }

    public function insert($data,$type) {
        $stmt = $this->conn->prepare('SELECT * FROM products WHERE sku=:sku');
        $stmt->bindValue(':sku',$data->sku);
        $stmt->execute();
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($product) {
            return 'Product With Such Sku Already Exists';
        }
          $insertMethod = 'insert'.$type;
          return $this->$insertMethod($data);
    }

    public function insertProduct($obj,$typeId) {
        $stmt = $this->conn->prepare("INSERT INTO products (sku,type_id,name,price) values(:sku,:type_id,:name,:price)");
        $stmt->bindValue(':sku',$obj->getSku());
        $stmt->bindValue(':type_id',$typeId);
        $stmt->bindValue(':name',$obj->getName());
        $stmt->bindValue(':price',$obj->getPrice());
        $stmt->execute();
    }

    public function insertDVD($dvd) {
        $typeId = 1;
        $dvd = new DVD($dvd);
        $this->insertProduct($dvd,$typeId);
        $stmt = $this->conn->prepare("INSERT INTO dvd (sku,type_id,size) values(:sku,:type_id,:size)");
        $stmt->bindValue(':sku',$dvd->getSku());
        $stmt->bindValue(':type_id',$typeId);
        $stmt->bindValue(':size',$dvd->getSize());
        return $stmt->execute();
    }

    public function insertFurniture( $furniture) {
        $typeId = 2;
        $furniture = new Furniture($furniture);
        $this->insertProduct($furniture,$typeId);
        $stmt = $this->conn->prepare("INSERT INTO furniture (sku,type_id,length,width,height) values(:sku,:type_id,:length,:width,:height)");
        $stmt->bindValue(':sku',$furniture->getSku());
        $stmt->bindValue(':type_id',$typeId);
        $stmt->bindValue(':length',$furniture->getLength());
        $stmt->bindValue(':width',$furniture->getWidth());
        $stmt->bindValue(':height',$furniture->getHeight());
        return $stmt->execute();
    }

    public function insertBook( $book) {
        $typeId = 3;
        $book = new Book($book);
        $this->insertProduct($book,$typeId);
        $stmt = $this->conn->prepare("INSERT INTO book (sku,type_id,weight) values(:sku,:type_id,:weight)");
        $stmt->bindValue(':sku',$book->getSku());
        $stmt->bindValue(':type_id',$typeId);
        $stmt->bindValue(':weight',$book->getWeight());

        return $stmt->execute();
    }
}
