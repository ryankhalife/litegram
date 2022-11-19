<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

require dirname(__DIR__) . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$img_id = $_GET['id'];

if (empty($img_id)) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Image id not provided";

    die(json_encode($response));
}

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

// check if already liked
$check = $mysqli->prepare("SELECT * FROM likes WHERE user_id = ? AND img_id = ?");
$check->bind_param("ii", $user_id, $img_id);
$check->execute();

$result = $check->get_result();
$response = [];

if ($result->num_rows > 0) { // already liked
    $delete = $mysqli->prepare("DELETE FROM likes WHERE user_id = ? AND img_id = ?");
    $delete->bind_param("ii", $user_id, $img_id);
    $delete->execute();
    $response['message'] = "Unliked";
} else { // not liked
    $insert = $mysqli->prepare("INSERT INTO likes (user_id, img_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $img_id);
    $insert->execute();
    $response['message'] = "Liked";
}

$response['success'] = true;

echo json_encode($response);