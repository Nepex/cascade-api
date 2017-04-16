<?php
    include '../connection.php';
    include '../jwt-helper.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: DELETE');    

    $partyId = $_GET['id'];

    $headers = apache_request_headers();
    $sentToken = $headers['Authorization'];

    if ($sentToken) {
        $decoded = JWT::decode($sentToken, '8725309');

        // create inventory
            if ($stmt = $mysqli->prepare("DELETE FROM `party` WHERE id = ?")) {
                $stmt->bind_param('i', $partyId);
                $stmt->execute();
                $stmt->close();
            }

        echo 'success';     
    }    
   
    $mysqli->close();
?>