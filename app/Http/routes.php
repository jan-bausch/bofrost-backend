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
use Illuminate\Http\Response;


$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('user/{id}', function ($id) {
   $response = app("db")->selectOne("SELECT id, name, points, image FROM users WHERE id=?", [$id]);
   return response()->json($response);
});

$app->get('leaderboard/', function () {
   $response = app("db")->select("SELECT id, name, points, image FROM users ORDER BY points DESC");
   return response()->json($response);
});

$app->get('product/{id}', function ($id) {
   $path = dirname(dirname(dirname(__FILE__))) . "/storage/app/app_productlist_shop.xml";
   $productXml = new SimpleXMLElement($path, null, true);

   
   foreach ($productXml->product as $product) {      
      if ($product->ArtNumber == $id) {
         $response = ["id" => (string) $id, "name" => (string) $product->Title, "description" => (string) $product->Description, "image" => app("url")->to("product/$id/photo")];
         return response()->json($response);
      }
   }
});


$app->get('product/{id}/photo', function ($id) {
   $path = dirname(dirname(dirname(__FILE__))) . "/storage/app/app_productlist_shop.xml";
   $productXml = new SimpleXMLElement($path, null, true);

   
   foreach ($productXml->product as $product) {      
      if ($product->ArtNumber == $id) {
         $image = file_get_contents($product->ImgUrl);
         return (new Response($image, 200))->header('Content-Type', "image/jpeg");
      }
   }
});

$app->get('product/{id}/completedChallenges', function ($id) {
   $challenges = app("db")->select("SELECT challenges.id, challenges.userid, type, data, IF(challenges.image='',0, 1) as hasimage FROM challenges INNER JOIN users ON (users.id = challenges.userid) WHERE productid=? ORDER BY points DESC", [$id]);
   $response = array();
   foreach ($challenges as $challenge) { 
      $singleResponse = ["id" => $challenge->id, "userid" => $challenge->userid, "type" => $challenge->type, "data" => $challenge->data, "image" => null];
      if ($challenge->hasimage) $singleResponse["image"] = app("url")->to("product/$id/completedChallenges/{$challenge->id}/photo");
      $response[] = $singleResponse;
   }
   return response()->json($response);
});

$app->get('product/{id}/completedChallenges/{challengeid}/photo', function ($id, $challengeid) {
   $photos = app("db")->select("SELECT image FROM challenges WHERE productid=? AND id=?", [$id, $challengeid]);
   foreach ($photos as $photo) { 
      return (new Response($photo->image, 200))->header('Content-Type', "image/png");
   }
   return response()->json($response);
});
