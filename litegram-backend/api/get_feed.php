<?php

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

// get posts not from user
$query = $mysqli->prepare("SELECT * FROM images WHERE user_id != ? ORDER BY date DESC");
$query->bind_param("i", $user_id);
$query->execute();

$result = $query->get_result();

$response = [];
$response['success'] = true;
$response['posts'] = [];

while ($post = $result->fetch_assoc()) {
    $response['posts'][] = $post;
}

echo json_encode($response);