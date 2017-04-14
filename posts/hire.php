<?php 
    include '../connection.php';
    include '../jwt-helper.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    $name = $_GET['name'];
    $job = $_GET['job'];
    $sprite = $_GET['sprite'];
    $level = 1;
    $experience = 0;
    $currentHp = 100;
    $hp = 100;
    $currentMp = 50;
    $mp = 50;

    $headers = apache_request_headers();
    $sentToken = $headers['Authorization'];
    
    if ($sentToken) {
        $decoded = JWT::decode($sentToken, '8725309');

        $userId = $decoded->id;

        if (!ctype_alnum($name) || $name == '' || strlen($name) > 15 || $job == '' || strlen($job) > 15 ||
        $sprite == '' || strlen($sprite) > 15) {
            echo 'validation error';
        } else {
            $sql = "SELECT * FROM users WHERE id = $userId";
            $result = $mysqli->query($sql);

            if ($result->num_rows) {
                while($row = $result->fetch_assoc()) {
                    $username = $row['username'];
                    $partySlotsAvailable = $row['party_slots'] - 1;
                    $partySlotsUnlocked = $row['party_slots_unlocked'];
                }
            }

            // remove available party slot
            if ($stmt = $mysqli->prepare("UPDATE `users` SET party_slots = ? WHERE id = $userId")) {
                $stmt->bind_param('i', $partySlotsAvailable);
                $stmt->execute();
                
                // create party member
                if ($stmt = $mysqli->prepare("INSERT INTO `party` (owner, name, job, sprite, level, experience, current_hp, hp, current_mp, mp, party_position) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $stmt->bind_param('ssssiiiiiii', $username, $name, $job, $sprite, $level, $experience, $currentHp, $hp, $currentMp, $mp, $partySlotsUnlocked);
                    $stmt->execute();
                    $stmt->close();
                }

                echo 'success';

                $stmt->close();
            } 
        }
    }

    $mysqli->close();
?>