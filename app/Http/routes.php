<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('user/{id}', function ($id) {
   $response = ["id"=>$id, "username" => "moritzbruder", "points" => 50];
   return response()->json($response);
});

$app->get('product/{id}', function ($id) {
   $path = dirname(dirname(dirname(__FILE__))) . "/storage/app/app_productlist_shop.xml";
   $productXml = new SimpleXMLElement($path, null, true);

   
   foreach ($productXml->product as $product) {      
      if ($product->ArtNumber == $id) {
         $response = ["name" => (string) $product->Title, "description" => (string) $product->Description, "image" => (string) $product->ImgUrl];
         return response()->json($response);
      }
   }
});

$app->get('product/{id}/completedChallenges', function ($id) {
   $results = app("db")->select("SELECT * FROM challenges");
   return var_dump($results);
});
