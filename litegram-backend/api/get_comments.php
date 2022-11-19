<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

include('connection.php');

$img_id = $_GET['id'];

$query = $mysqli->prepare("SELECT user_id, text FROM comments WHERE img_id = ?");
$query->bind_param("i", $img_id);
$query->execute();

$result = $query->get_result();

$response = [];
$response['success'] = true;
$response['comments'] = [];
while ($comment = $result->fetch_assoc()) {
    $response['comments'][] = $comment;
}

echo json_encode($response);