<?php 
    include './connection.php';

    $username = $_GET['username'];
    $password = $_GET['password'];

    if ($stmt = $mysqli->prepare("SELECT username, password FROM users where username=?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($fetchedUser, $fetchedPass);
        $stmt->fetch();
        $stmt->close();
    }

    $hashed = $fetchedPass;
    $unhashed = password_verify($password, $hashed);

    if ($unhashed == 0) {
        echo 'incorrect credentials';
    } else {
        if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?")) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($fetchedId);
            $stmt->fetch();
            $stmt->close();
        }

        echo $fetchedId;
        $mysqli->close();
    }
?>
