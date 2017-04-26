<?php
include '../connection.php';
include '../jwt-helper.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$headers = apache_request_headers();
$sentToken = $headers['Authorization'];

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $mysqli->query($sql);
    
    if ($result->num_rows) {
        while($row = $result->fetch_assoc()) {
            $row["password"] = '';
            $rows[] = $row;
        }
    }
    
    echo json_encode($rows);
}

$mysqli->close();
?>