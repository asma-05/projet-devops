<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    

    if ($email === 'admin@gmail.com' && $password === 'password') {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['username'] = 'Admin';
        $_SESSION['is_admin'] = true;
        echo "success:admin";
    } else {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = false;
                echo "success:" . $user['username'];
            } else {
                echo "error: Invalid email or password";
            }
        } else {
            echo "error: Invalid email or password";
        }
    }
}
?>
