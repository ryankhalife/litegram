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

//get user by id 
$query = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();

$result = $query->get_result();
$user = $result->fetch_assoc();

$f_name = $_POST['f_name'] ?? $user['f_name'];
$l_name = $_POST['l_name'] ?? $user['l_name'];
$username = $_POST['username'] ?? $user['username'];
$email = $_POST['email'] ?? $user['email'];
$bio = $_POST['bio'] ?? $user['bio'];

if (isset($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
} else {
    $password = $user['password'];
}

//update user
$query = $mysqli->prepare("UPDATE users SET f_name = ?, l_name = ?, username = ?, password = ?, bio = ?, email = ? WHERE id = ?");
$query->bind_param("ssssssi", $f_name, $l_name, $username, $password, $bio, $email, $user_id);
$query->execute();

$response = [];
$response['success'] = true;
$response['message'] = "User updated successfully";

$payload = [
    "id" => $user['id'],
    "username" => $username,
    "email" => $email,
    "f_name" => $f_name,
    "l_name" => $l_name,
    "bio" => $bio,
];

$jwt = JWT::encode($payload, $key, "HS256");
$response['token'] = $jwt;

echo json_encode($response);