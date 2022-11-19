<?php

//add comment
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

require dirname(__DIR__) . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//Validate JWT
$headers = apache_request_headers();
$auth = $headers['Authorization'];
$token = str_replace("Bearer ", "", $auth);

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $user_id = $decoded->id;
} catch (Exception $e) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Invalid token";

    die(json_encode($response));
}

$img_id = $_POST['img_id'];
$text = $_POST['text'];

$query = $mysqli->prepare("INSERT INTO comments (user_id, img_id, text) VALUES (?, ?, ?)");
$query->bind_param("iis", $user_id, $img_id, $text);
$query->execute();

$response = [];
$response['success'] = true;

echo json_encode($response);