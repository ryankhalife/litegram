<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

$username = $_POST['username'];
$password = $_POST['password'];

//check all fields are filled
if (empty($username) || empty($password)) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Please fill all fields";

    die(json_encode($response));
}

// get user from database
$query = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user == null || !password_verify($password, $user['password'])) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Incorrect username or password";

    die(json_encode($response));
}

$response = [];
$response['success'] = true;
$response['message'] = "Login successful";

$payload = [
    "id" => $user['id'],
    "username" => $user['username'],
    "email" => $user['email'],
    "f_name" => $user['f_name'],
    "l_name" => $user['l_name'],
];

$jwt = JWT::encode($payload, $key, "HS256");
$response['token'] = $jwt;

echo json_encode($response);