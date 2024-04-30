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

 //FARMASIGROUP
 //OBAT
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

    // Get data by SKU untuk menampilkan data yang akan diupdate
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

    // Get data by SKU untuk menampilkan data yang akan diupdate
    $app->get("/farmasi/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('SELECT * FROM farmasi WHERE sku = :sku');
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
    $app->put("/farmasi/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];
        $requestData = $request->getParsedBody();

        $stmt = $pdo->prepare('UPDATE farmasi SET label = :label, dosis = :dosis WHERE sku = :sku');

        $data = [
            ":sku" => $sku,
            ":label" => $requestData["label"],
            ":dosis" => $requestData["dosis"]
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
    $app->delete("/farmasi/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('DELETE FROM farmasi WHERE sku = :sku');

        if($stmt->execute([':sku' => $sku]))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });


//END OBAT
//DATA FARMASI
    
    $app->get('/farmasi', function (Request $request, Response $response) use ($pdo) {
        $stmt = $pdo->query('SELECT * FROM farmasi');
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($items));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    //insert
    $app->post("/farmasi", function (Request $request, Response $response) use ($pdo){
        $data = $request->getParsedBody();
        
        $stmt = $pdo->prepare('INSERT INTO farmasi (nama_obat, sku, dosis, label) VALUES (:nama_obat, :sku, :dosis, :label)');
        
        $data = [
            ":nama_obat" => $data["nama_obat"],
            ":sku" => $data["sku"],
            ":dosis" => $data["dosis"],
            ":label" => $data["label"]
        ];
        
        if($stmt->execute($data)) {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });
    


//END DATA FARMASI
//END FARMASIGROUP

//Rawat Jalan
        
    $app->get('/rawatjalan1', function(Request $request, Response $response) use ($pdo) {
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

    $app->post("/pasien", function (Request $request, Response $response) use ($pdo){

        $data = $request->getParsedBody(); 

        $stmt = $pdo->prepare('INSERT INTO pasien (alamat,berat,gol_darah,jk,kontak_keluarga,kontak_keluarga_alamat,
                                kontak_keluarga_hp,nama,nik,no_hp, no_rm,tempat_lahir,tgl_lahir,tinggi) 
                                VALUE (:alamat,:berat,:gol_darah,:jk,:kontak_keluarga,:kontak_keluarga_alamat,
                                :kontak_keluarga_hp,:nama,:nik,:no_hp,: no_rm,:tempat_lahir,:tgl_lahir,:tinggi)');

        $data = [
            ":alamat"  => $data["alamat"],
            ":berat"  => $data["berat"],
            ":gol_darah"  => $data["gol_darah"],
            ":jk"  => $data["jk"],
            ":kontak_keluarga"  => $data["kontak_keluarga"],
            ":kontak_keluarga_alamat"  => $data["kontak_keluarga_alamat"],
            ":kontak_keluarga_hp"  => $data["kontak_keluarga_hp"],
            ":nama"  => $data["nama"],
            ":nik " => $data["nik"],
            ":no_hp"  => $data["no_hp"],
            ":no_rm"  => $data["no_rm "],
            ":tempat_lahir"  => $data["tempat_lahir"],
            ":tgl_lahir"  => $data["tgl_lahir "],
            ":tinggi"  => $data["tinggi "]
        ];
    
        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Get data by nama untuk menampilkan data 
    $app->get("/pasien/{nama}", function (Request $request, Response $response, $args) use ($pdo){
        $nama = $args['nama'];

        $stmt = $pdo->prepare('SELECT * FROM pasien WHERE nama = :nama');
        $stmt->execute([':nama' => $nama]);
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
    $app->put("/pasien/{nama}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['nama'];
        $requestData = $request->getParsedBody();

        $stmt = $pdo->prepare('UPDATE pasien SET nik = :nik, berat = :berat WHERE nama = :nama');

        $data = [
            ":alamat"  => $requestData["alamat"],
            ":berat"  => $requestData["berat"],
            ":gol_darah"  => $requestData["gol_darah"],
            ":jk"  => $requestData["jk"],
            ":kontak_keluarga"  => $requestData["kontak_keluarga"],
            ":kontak_keluarga_alamat"  => $requestData["kontak_keluarga_alamat"],
            ":kontak_keluarga_hp"  => $requestData["kontak_keluarga_hp"],
            ":nama"  => $requestData["nama"],
            ":nik " => $requestData["nik"],
            ":no_hp"  => $requestData["no_hp"],
            ":no_rm"  => $requestData["no_rm "],
            ":tempat_lahir"  => $requestData["tempat_lahir"],
            ":tgl_lahir"  => $requestData["tgl_lahir "],
            ":tinggi"  => $requestData["tinggi "]
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
    $app->delete("/pasien/{nama}", function (Request $request, Response $response, $args) use ($pdo){
        $nama = $args['nama'];

        $stmt = $pdo->prepare('DELETE FROM pasien WHERE nama = :nama');

        if($stmt->execute([':nama' => $nama]))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });
        
    //  END Pasien
    // Mulai Rekam Medis
    $app->group('/rekam-medis', function (Group $group) use ($pdo) {

        // Mendapatkan semua data rekam medis
        $group->get('', function (Request $request, Response $response) use ($pdo) {
            $stmt = $pdo->query('SELECT rekam_medis.no_rm AS no_rm,  pasien.nama AS nama_pasien, tindakan.deskripsi AS deskripsi_tindakan, farmasi.nama_obat AS nama_obat
                                 FROM rekam_medis
                                 INNER JOIN pasien ON rekam_medis.no_rm = pasien.no_rm
                                 INNER JOIN tindakan ON rekam_medis.no_rm = tindakan.no_rm
                                 INNER JOIN obat ON rekam_medis.sku = obat.sku
                                 INNER JOIN farmasi ON obat.sku = farmasi.sku');
            $rekam_medis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response->getBody()->write(json_encode($rekam_medis));
            return $response->withHeader('Content-Type', 'application/json');
        });
        // Mendapatkan data lengkap pasien berdasarkan nomor rekam medis
    });

    $app->group('/detail-pasien', function (Group $group) use ($pdo) {
        // Mendapatkan data lengkap pasien berdasarkan nomor rekam medis
        $group->get('/{no_rm}', function (Request $request, Response $response, array $args) use ($pdo) {
            $no_rm = $args['no_rm'];
            $stmt = $pdo->prepare('SELECT * FROM pasien WHERE no_rm = :no_rm');
            $stmt->execute([':no_rm' => $no_rm]);
            $pasien = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$pasien) {
                $response->getBody()->write(json_encode(['error' => 'Data pasien tidak ditemukan']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            $response->getBody()->write(json_encode($pasien));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/riwayat', function (Group $group) use ($pdo) {
    // Mendapatkan riwayat pasien berdasarkan nomor rekam medis
    $group->get('/{no_rm}', function (Request $request, Response $response, array $args) use ($pdo) {
        $no_rm = $args['no_rm'];
        // Mendapatkan riwayat pasien berdasarkan nomor rekam medis dari tabel rekam_medis
        $stmt_riwayat = $pdo->prepare('SELECT id_rm, tanggal, keluhan, tinggi, berat, tensi, dokter FROM rekam_medis WHERE no_rm = :no_rm');
        $stmt_riwayat->execute([':no_rm' => $no_rm]);
        $riwayat_pasien = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);

        if (empty($riwayat_pasien)) {
            $response->getBody()->write(json_encode(['error' => 'Riwayat pasien tidak ditemukan']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Ambil data obat berdasarkan id_rm
        foreach ($riwayat_pasien as &$riwayat) {
            $id_rm = $riwayat['id_rm'];
            $stmt_obat = $pdo->prepare('SELECT sku FROM obat WHERE id_rm = :id_rm');
            $stmt_obat->execute([':id_rm' => $id_rm]);
            $sku_results = $stmt_obat->fetchAll(PDO::FETCH_ASSOC);

            $nama_obat = [];
            foreach ($sku_results as $sku_row) {
                $sku = $sku_row['sku'];
                // Ambil nama obat dari tabel farmasi berdasarkan sku
                $stmt_nama_obat = $pdo->prepare('SELECT nama_obat FROM farmasi WHERE sku = :sku');
                $stmt_nama_obat->execute([':sku' => $sku]);
                $nama_obat_result = $stmt_nama_obat->fetch(PDO::FETCH_ASSOC);
                if ($nama_obat_result) {
                    $nama_obat[] = $nama_obat_result['nama_obat'];
                }
            }
            $riwayat['obat'] = $nama_obat;
        }

        $response->getBody()->write(json_encode($riwayat_pasien));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

$app->group('/cari_pasien', function (Group $group) use ($pdo) {
    // Mendapatkan data lengkap pasien berdasarkan nomor rekam medis
    $group->get('/{no_rm}', function (Request $request, Response $response, array $args) use ($pdo) {
        $no_rm = $args['no_rm'];

        // Prepare statement
        $stmt = $pdo->prepare('SELECT no_rm, nama, jk, gol_darah, tinggi, berat FROM pasien WHERE no_rm = :no_rm');
        $stmt->execute([':no_rm' => $no_rm]);

        // Fetch data
        $pasien = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika data tidak ditemukan, kirim response error
        if (!$pasien) {
            $response->getBody()->write(json_encode(['error' => 'Data pasien tidak ditemukan']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Kirim data pasien dalam format JSON
        $response->getBody()->write(json_encode($pasien));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

//Rawat Jalan
    // API membuat tampilan untuk memanggil api setelah dientri ke rawat jalan
    // misale ndak muncul bnyk baris 146 di hapus 
    $app->get('/rawatjalan', function(Request $request, Response $response) use ($pdo) {
        // $response-> getBody()->write(json_encode(['foo'=>'bar']));
        $stmt = $pdo->query('
        SELECT
            *
        FROM tindakan 
        INNER JOIN pasien ON tindakan.no_rm = pasien.no_rm 
        INNER JOIN obat ON tindakan.no_rm = obat.id_rm 
        ;      
            ');
        $obats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($obats));

        return $response->withHeader('Content-Type','application/json')->withStatus(201);
    });

    $app->get('/daftarpasien', function (Request $request, Response $response) use($pdo){
        // $response->getBody()->write(json_encode(['foo'=> 'bar']));
        $stmt = $pdo->query('SELECT * FROM pasien');
        $pasien = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($pasien));
        return $response->withHeader('Content-Type','application/json')->withStatus(201);
        //return $response->withJson(["status"=>"success"],200);
    });

    // insert pada table OBAT & TINDAKAN 
    $app->post("/tindakanObat", function (Request $request, Response $response) use ($pdo){

        $data = $request->getParsedBody();
        // QUERY 1 INSERT OBAT
        $stmt1 = $pdo->prepare('INSERT INTO obat (id_rm, sku, label_catatan, jumlah) VALUE (:id_rm,:sku, :label_catatan, :jumlah)');
        $data1 = [
            ":id_rm" => $data["id_rm"],
            ":sku" => $data["sku"],
            ":label_catatan" => $data["label_catatan"],
            ":jumlah" => $data["jumlah"]
        ];
    
        if($stmt1->execute($data1))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }

        // QUERY 2 INSERT TINDAKAN --> BISA NDAA? APAKAH PERINTAH QUERY 2 HARUS DI DALAM IF QUERY 1 (?)
        $stmt2 = $pdo->prepare('INSERT INTO tindakan (no_rm, deskripsi) VALUE (:no_rm,:deskripsi)');

        $data2 = [
            ":no_rm" => $data["no_rm"],
            ":deskripsi" => $data["deskripsi"] // Assuming "deskripsi" is present in the request body
        ];

        if (!$stmt2->execute($data2)) {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Get data by nama untuk menampilkan data sesuai nama pasien ---> PINJAM DR FUNCTION TIM PASIEN --> /pasien/{nama}
  //END rawat jalan


  // API OBAT
  $app->group('/cari_obat', function (Group $group) use ($pdo) {
    // Mendapatkan data lengkap obat berdasarkan ID
    $group->get('/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
        $id = $args['id'];

        // Prepare statement
        $stmt = $pdo->prepare('SELECT * FROM farmasi WHERE id = :id');
        $stmt->execute([':id' => $id]);

        // Fetch data
        $obat = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika data tidak ditemukan, kirim response error
        if (!$obat) {
            $response->getBody()->write(json_encode(['error' => 'Data obat tidak ditemukan']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Kirim data obat dalam format JSON
        $response->getBody()->write(json_encode($obat));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

};