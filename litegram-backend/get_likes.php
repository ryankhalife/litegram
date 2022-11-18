<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

$id = $_GET['id'];

$query = $mysqli->prepare("SELECT COUNT(*) FROM likes WHERE img_id = ?");
$query->bind_param("i", $id);
$query->execute();

$result = $query->get_result();
$likes = $result->fetch_assoc();

$response = [];
$response['success'] = true;
$response['likes'] = $likes['COUNT(*)'] ?? 0;

echo json_encode($response);