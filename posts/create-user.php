<?php 
    include '../connection.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

    $username = $_GET['username'];
    $email = $_GET['email'];
    $password = $_GET['password'];

    if ($stmt = $mysqli->prepare("INSERT INTO `users` (username, password, email) VALUES (?, ?, ?)")) {
        $stmt->bind_param('sss', $username, $password, $email);
        $stmt->execute();
        
        echo 'success!';

        $stmt->close();
    }

    $mysqli->close();
?>