<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

include('connection.php');

require dirname(__DIR__) . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$image = $_FILES['image'];
$caption = $_POST['caption'];

if (empty($image)) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Image not provided";

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

//store image
$filename = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
$target = 'uploads/posts/' . $filename;

if (move_uploaded_file($image['tmp_name'], $target)) {
    $date = time();

    $query = $mysqli->prepare("INSERT INTO images (user_id, image, caption, date) VALUES (?, ?, ?, ?)");
    $query->bind_param("isss", $user_id, $filename, $caption, $date);
    $query->execute();

    $response = [];
    $response['success'] = true;
    $response['message'] = "Image uploaded";

    echo json_encode($response);
} else {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Image upload failed";

    echo json_encode($response);
}