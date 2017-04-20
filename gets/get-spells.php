<?php
    include '../connection.php';
    include '../jwt-helper.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    $partyMember = $_GET['party_member'];

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

        $spellsSql = "SELECT * FROM spells_learned WHERE party_member = '$partyMember' AND username = '$username'";
        $spellsResult = $mysqli->query($spellsSql);

        if ($spellsResult->num_rows) {
            while($row = $spellsResult->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        echo json_encode($rows);
    }

    $mysqli->close();
?>