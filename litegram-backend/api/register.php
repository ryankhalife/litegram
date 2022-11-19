<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

require dirname(__DIR__) . '/vendor/autoload.php';
use Firebase\JWT\JWT;

$f_name = $_POST['f_name'];
$l_name = $_POST['l_name'];
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

//check all fields are filled
if (empty($f_name) || empty($l_name) || empty($username) || empty($password) || empty($email)) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Please fill all fields";

    die(json_encode($response));
}

//check password length
if (strlen($password) < 8) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Password must be at least 8 characters";

    die(json_encode($response));
}

//check username length
if (strlen($username) < 3) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Username must be at least 3 characters";

    die(json_encode($response));
}

//check email structure
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = [];
    $response['success'] = false;
    $response['message'] = "Invalid email";

    die(json_encode($response));
}


// check if username already exists
$check = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();

$result = $check->get_result();
if ($result->num_rows > 0) { // username already exists
    $response = [];
    $response['success'] = false;
    $response['message'] = "Username already exists";

    die(json_encode($response));
}

// check if email already exists
$check = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();

$result = $check->get_result();
if ($result->num_rows > 0) { // email already exists
    $response = [];
    $response['success'] = false;
    $response['message'] = "Email already exists";

    die(json_encode($response));
}

//hash password
$password = password_hash($password, PASSWORD_DEFAULT);

$profile_picture = "default.jpeg";

// insert user
$query = $mysqli->prepare("INSERT INTO users (f_name, l_name, username, password, email, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
$query->bind_param("ssssss", $f_name, $l_name, $username, $password, $email, $profile_picture);
$query->execute();

$response = [];
$response['success'] = true;
$response['message'] = "User registered successfully";

$id = $mysqli->insert_id;

$payload = [
    "id" => $id,
    "username" => $username,
    "email" => $email,
    "f_name" => $f_name,
    "l_name" => $l_name,
];

$jwt = JWT::encode($payload, $key, "HS256");
$response['token'] = $jwt;

echo json_encode($response);