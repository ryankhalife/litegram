<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

include('connection.php');

$user_id = $_POST['user_id'];
$img_id = $_POST['img_id'];

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



