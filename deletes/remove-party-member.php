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

        $userId = $decoded->id;

        $userSql = "SELECT * FROM users WHERE id = $userId";
        $userResult = $mysqli->query($userSql);
        if ($userResult->num_rows) {
            while($row = $userResult->fetch_assoc()) {
                $username = $row["username"];
            }
        }

        $partySql = "SELECT * FROM party WHERE id = $partyId";
        $partyResult = $mysqli->query($partySql);
        if ($partyResult->num_rows) {
            while($row = $partyResult->fetch_assoc()) {
                $partyMember = $row["name"];
            }
        }

        // remove party member
        if ($stmt = $mysqli->prepare("DELETE FROM party WHERE id = ?")) {
            $stmt->bind_param('i', $partyId);
            $stmt->execute();
            $stmt->close();
        }

        // remove spells
        if ($stmt = $mysqli->prepare("DELETE FROM spells_learned WHERE username = ? AND party_member = ?")) {
            $stmt->bind_param('ss', $username, $partyMember);
            $stmt->execute();
            $stmt->close();
        }

        echo 'success';     
    }    
   
    $mysqli->close();
?>