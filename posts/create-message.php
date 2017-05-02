<?php
include '../connection.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$entityBody = file_get_contents('php://input');
$bodyArray = json_decode($entityBody, true);

$message = $bodyArray['message'];
$sender = $bodyArray['sender'];
$receiver = $bodyArray['receiver'];
$dateOf = $bodyArray['date'];
$timeOf = $bodyArray['time'];
$seen = 'false';

$searchTakenNames = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$receiver'");
$nameMatches = mysqli_num_rows($searchTakenNames);

if ($nameMatches == 0) {
    echo 'username doesnt exist';
    return;
} 

// create message
if ($stmt = $mysqli->prepare("INSERT INTO `messages` (sender, receiver, message, dateOf, timeOf, seen) VALUES (?, ?, ?, ?, ?, ?)")) {
    $stmt->bind_param('ssssss', $sender, $receiver, $message, $dateOf, $timeOf, $seen);
    $stmt->execute();
    
    echo 'success';
    
    $stmt->close();
}

$mysqli->close();
?>