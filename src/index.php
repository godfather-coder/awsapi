<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-type: application/json');

require "../vendor/autoload.php";

use utils\DBConnect;
use services\ProductService;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load("./.env");

$db = new DBConnect($_ENV['DB_HOST'],$_ENV['DB_NAME'],$_ENV['DB_USERNAME'],$_ENV['DB_PASSWORD']);

$conn = $db->connect();

$service = new ProductService($conn);

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        echo $service->getProducts();
        break;
    case "POST":

        $product = json_decode((file_get_contents('php://input')));

        $res = $service->insert($product,$product->type);

        if(is_string($res)) {
            http_response_code(409);
            echo $res;
        }

        break;

    case "DELETE":
        $str = $_SERVER['REQUEST_URI'];
        $str = str_replace('/','',$str);
        $str = str_replace('?','',$str);
        $str = str_replace('&','',$str);
        $delete =explode('=',$str);
        $skus = array_filter($delete);
        $service->deleteProducts($skus);
        break;
}
