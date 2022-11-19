<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

$id = $_GET['id'];

$query = $mysqli->prepare("SELECT username, bio, profile_picture  FROM users WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();

$result = $query->get_result();
$user = $result->fetch_assoc();

$response = [];
$response['success'] = true;
$response['user'] = $user;

echo json_encode($response);