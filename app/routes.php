<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // Database configuration
    $dsn = 'mysql:host=localhost;dbname=rumah_sakit;charset=utf8mb4';
    $username = 'root';
    $password = '';

    // Set up PDO connection
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }



    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

 //FARMASI
    $app->get('/obat', function (Request $request, Response $response) use ($pdo) {

        $stmt = $pdo->query('SELECT * FROM obat');
        $obats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($obats));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->post("/obat", function (Request $request, Response $response) use ($pdo){

        $data = $request->getParsedBody();

        $stmt = $pdo->prepare('INSERT INTO obat (sku, label_catatan, jumlah) VALUE (:sku, :label_catatan, :jumlah)');

        $data = [
            ":sku" => $data["sku"],
            ":label_catatan" => $data["label_catatan"],
            ":jumlah" => $data["jumlah"]
        ];
    
        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Get data by SKU untuk menampilkan data yang akan di
    $app->get("/obat/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('SELECT * FROM obat WHERE sku = :sku');
        $stmt->execute([':sku' => $sku]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'not_found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    });


    // Update data
    $app->put("/obat/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];
        $requestData = $request->getParsedBody();

        $stmt = $pdo->prepare('UPDATE obat SET label_catatan = :label_catatan, jumlah = :jumlah WHERE sku = :sku');

        $data = [
            ":sku" => $sku,
            ":label_catatan" => $requestData["label_catatan"],
            ":jumlah" => $requestData["jumlah"]
        ];

        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });


    // Delete data
    $app->delete("/obat/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('DELETE FROM obat WHERE sku = :sku');

        if($stmt->execute([':sku' => $sku]))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

//END FARMASI

//Rawat Jalan
        
    $app->get('/rawatjalan', function(Request $request, Response $response) use ($pdo) {
        // $response-> getBody()->write(json_encode(['foo'=>'bar']));
        $stmt = $pdo->query('
        SELECT
            *
        FROM tindakan 
        INNER JOIN pasien ON tindakan.no_rm = pasien.no_rm 
        ;      
            ');
        $obats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($obats));

        return $response->withHeader('Content-Type','application/json')->withStatus(201);
    });

    $app->get('/pasien', function (Request $request, Response $response) use($pdo){
        // $response->getBody()->write(json_encode(['foo'=> 'bar']));
        $stmt = $pdo->query('SELECT * FROM pasien');
        $pasien = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($pasien));
        return $response->withHeader('Content-Type','application/json')->withStatus(201);
        //return $response->withJson(["status"=>"success"],200);
    });

    $app->post("/formedit", function (Request $request, Response $response) use ($pdo){

        $data = $request->getParsedBody();

        $stmt = $pdo->prepare('INSERT INTO tindakan ( no_rm, deskripsi) VALUE (:deskripsi)');

        $data = [
            ":no_rm" => $data["no_rm"],
            ":deskripsi" => $data["deskripsi"]
        ];

        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

  //END rawat jalan
        

    };
