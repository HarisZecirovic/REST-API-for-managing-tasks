<?php
//  ovo for every URL run the index.php script
// ne znam sto to radi sad ali mozda saznam kasnije
// u htaccess fileu
// i ovo nije php nego apache configuration directives
// Napravio je da svi URL-ovi idu na ovu jednu skriptu i to index.php 

declare(strict_types=1);
//ini_set("display_errors", "On");

require __DIR__ . "/bootstrap.php";



$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[4];

$id = $parts[5] ?? null; // if it is not set then we set it to null

// echo $resource, " -  ", $id;

// echo "\n", $_SERVER["REQUEST_METHOD"];

if($resource != "tasks"){
    // header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
    http_response_code(404);
    exit;
}





//require dirname(__DIR__) . "/src/TaskController.php";




$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);

//print($_SERVER["HTTP_AUTHORIZATION"]);
//$headers = apache_request_headers();
//echo $headers["Authorization"];
//exit;

$codec = new JWTCodec($_ENV["SECRET_KEY"]);

$auth = new Auth($user_gateway, $codec);

if( ! $auth->authenticateAccessToken()){
    exit;
}



$user_id = $auth->getUserID();


//$database->getConnection();
$task_gateway = new TaskGateway($database);

$controller = new TaskController($task_gateway, $user_id);

$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

