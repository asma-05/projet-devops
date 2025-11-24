<?php
require_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
   
    $user_id = NULL;
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
       
        $user_check_sql = "SELECT id FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
        $user_check_result = mysqli_query($conn, $user_check_sql);
        
        if (mysqli_num_rows($user_check_result) > 0) {
            $user_id = $_SESSION['user_id'];
        }
    }
    
   
    if ($user_id) {
        $sql = "INSERT INTO feedbacks (user_id, subject, message) VALUES ('$user_id', '$subject', '$message')";
    } else {
        $sql = "INSERT INTO feedbacks (subject, message) VALUES ('$subject', '$message')";
    }
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Thank you for your feedback! We will get back to you soon.'); window.location.href ='contact.html';</script>";
    } else {
        echo "<script>alert('Error submitting feedback. Please try again.'); window.location.href = 'contact.html';</script>";
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
   
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    if (!$user_id) {
        echo "<script>alert('Please log in first to subscribe.'); window.location='contact.html';</script>";
        exit;
    }
    
    
    $user_check_sql = "SELECT id FROM users WHERE id = '$user_id'";
    $user_check_result = mysqli_query($conn, $user_check_sql);
    
    if (mysqli_num_rows($user_check_result) == 0) {
        echo "<script>alert('User not found. Please log in again.'); window.location='main.html';</script>";
        exit;
    }
    
    $plan = mysqli_real_escape_string($conn, $_POST['subscription']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $sub_email = mysqli_real_escape_string($conn, $_POST['sub_email']);
    
    $start_date = date("Y-m-d");
    $renewal_date = date("Y-m-d", strtotime("+30 days"));
    
   
    $check_sql = "SELECT * FROM premium_users WHERE user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        
        $sql = "UPDATE premium_users SET plan_type = '$plan', start_date = '$start_date', renewal_date = '$renewal_date', status = 'active' WHERE user_id = '$user_id'";
    } else {
        
        $sql = "INSERT INTO premium_users (user_id, plan_type, start_date, renewal_date, status) 
                VALUES ('$user_id', '$plan', '$start_date', '$renewal_date', 'active')";
    }
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Premium subscription activated successfully!'); window.location='contact.html';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='contact.html';</script>";
    }
    exit;
}
?>
