<?php
include '../connection.php';
include '../jwt-helper.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

$headers = apache_request_headers();
$sentToken = $headers['Authorization'];

$entityBody = file_get_contents('php://input');
$bodyArray = json_decode($entityBody, true);

$expGained = $bodyArray['exp'];
$partyIds = $bodyArray['partyIds'];

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    for ($i = 0; $i < count($partyIds); $i++) {
        $partySql = "SELECT * FROM party WHERE id = $partyIds[$i]";
        $partyResult = $mysqli->query($partySql);
        if ($partyResult->num_rows) {
            while($row = $partyResult->fetch_assoc()) {
                $exp = $row['experience'];
                $expNeeded = $row['experience_needed'];
                
                $newAmount = $exp + $expGained;
                
                if ($newAmount > $row['experience_needed']) {
                    continue;
                }
                
                if ($stmt = $mysqli->prepare("UPDATE party SET experience = ? WHERE id = ?")) {
                    $stmt->bind_param('ii', $newAmount, $partyIds[$i]);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            
        }
    }
    
    echo 'success';
}

$mysqli->close();
?>