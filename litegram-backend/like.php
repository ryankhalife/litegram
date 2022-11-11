<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

include('connection.php');

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$img_id = $_POST['img_id'];

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
if ($result->num_rows > 0) { // already liked
    $delete = $mysqli->prepare("DELETE FROM likes WHERE user_id = ? AND img_id = ?");
    $delete->bind_param("ii", $user_id, $img_id);
    $delete->execute();
} else { // not liked
    $insert = $mysqli->prepare("INSERT INTO likes (user_id, img_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $img_id);
    $insert->execute();
}

$response = [];
$response['success'] = true;

echo json_encode($response);