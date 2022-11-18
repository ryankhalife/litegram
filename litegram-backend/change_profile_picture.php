<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

include('connection.php');

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$image = $_FILES['image'];
$delete = $_POST['delete'];

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

if ($delete) {
    $query = $mysqli->prepare("UPDATE users SET profile_picture = 'default.png' WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();

    $response = [];
    $response['success'] = true;
    $response['message'] = "Profile picture deleted";
    die(json_encode($response));
}

if (empty($image)) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Image not provided";

    die(json_encode($response));
}

$filename = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
$target = 'uploads/profile-pictures/' . $filename;

if (move_uploaded_file($image['tmp_name'], $target)) {
    $query = $mysqli->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $query->bind_param("si", $filename, $user_id);
    $query->execute();

    $response = [];
    $response['success'] = true;
    $response['message'] = "Profile picture uploaded";

    echo json_encode($response);
} else {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Profile picture upload failed";

    echo json_encode($response);
}