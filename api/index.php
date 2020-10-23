<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require "../vendor/autoload.php";
require "../src/config/db.php";



$app = new \Slim\App;
$app->get('/stocks', function (Request $request, Response $response) {

    $db = new Db();
    try{
        $db = $db->connect();

        $stocks = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_OBJ);
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson(
                array(
                    "text"  => "success",
                    "code"  => 0,
                    "data"  => $stocks,
                )
            );

    }catch(PDOException $e){
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }
    $db = null;
});

$app->post('/stocks/add', function (Request $request, Response $response) {
    # Auto Increment
    #$product_id      = $request->getParam("product_id");
    $name = $request->getParam("name");
    $stock = $request->getParam("stock");
    # Tarih verisi otomatik atanıyor
    #$created_date      = $request->getParam("created_date");

    $db = new Db();
    try{
        $db = $db->connect();
        $statement = "INSERT INTO products (name, stock) VALUES(:name, :stock)";
        $prepare = $db->prepare($statement);

        $prepare->bindParam("name", $name);
        $prepare->bindParam("stock", $stock);

        $product = $prepare->execute();

        if($product){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text"  => "Urun basarili bir sekilde eklenmistir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text"  => "Ekleme islemi sirasinda bir problem olustu."
                    )
                ));
        }

    }catch(PDOException $e){
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }
    $db = null;
});


$app->run();
