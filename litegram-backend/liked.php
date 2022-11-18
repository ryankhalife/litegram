<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$img_id = $_GET['id'];

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

$query = $mysqli->prepare("SELECT * FROM likes WHERE user_id = ? AND img_id = ?");
$query->bind_param("ii", $user_id, $img_id);
$query->execute();

$result = $query->get_result();
$response = [];

if ($result->num_rows > 0) { // already liked
    $response['liked'] = true;
} else { // not liked
    $response['liked'] = false;
}

$response['success'] = true;

echo json_encode($response);