<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit();
}

$user_sql = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($conn, $user_sql);
$currentUser = mysqli_fetch_assoc($user_result);

if (!$currentUser || !$currentUser['is_admin']) {
    header("Location: main.php");
    exit();
}

// Get statistics
$total_users_sql = "SELECT COUNT(*) as count FROM users";
$total_users_result = mysqli_query($conn, $total_users_sql);
$total_users = mysqli_fetch_assoc($total_users_result)['count'];

$premium_users_sql = "SELECT COUNT(*) as count FROM premium_users WHERE status = 'active'";
$premium_users_result = mysqli_query($conn, $premium_users_sql);
$premium_users = mysqli_fetch_assoc($premium_users_result)['count'];

$total_songs_sql = "SELECT COUNT(*) as count FROM songs";
$total_songs_result = mysqli_query($conn, $total_songs_sql);
$total_songs = mysqli_fetch_assoc($total_songs_result)['count'];

$total_playlists_sql = "SELECT COUNT(*) as count FROM playlists";
$total_playlists_result = mysqli_query($conn, $total_playlists_sql);
$total_playlists = mysqli_fetch_assoc($total_playlists_result)['count'];

// Get all users
$users_sql = "SELECT * FROM users ORDER BY join_date DESC";
$users_result = mysqli_query($conn, $users_sql);
$all_users = array();
if (mysqli_num_rows($users_result) > 0) {
    while($row = mysqli_fetch_assoc($users_result)) {
        $all_users[] = $row;
    }
}

// Get premium users
$premium_details_sql = "SELECT pu.*, u.username, u.email FROM premium_users pu JOIN users u ON pu.user_id = u.id";
$premium_details_result = mysqli_query($conn, $premium_details_sql);
$premium_details = array();
if (mysqli_num_rows($premium_details_result) > 0) {
    while($row = mysqli_fetch_assoc($premium_details_result)) {
        $premium_details[] = $row;
    }
}

// Get all songs
$all_songs_sql = "SELECT * FROM songs ORDER BY plays DESC";
$all_songs_result = mysqli_query($conn, $all_songs_sql);
$all_songs = array();
if (mysqli_num_rows($all_songs_result) > 0) {
    while($row = mysqli_fetch_assoc($all_songs_result)) {
        $all_songs[] = $row;
    }
}

// Get all playlists
$all_playlists_sql = "SELECT p.*, u.username as creator_name FROM playlists p LEFT JOIN users u ON p.creator_id = u.id";
$all_playlists_result = mysqli_query($conn, $all_playlists_sql);
$all_playlists = array();
if (mysqli_num_rows($all_playlists_result) > 0) {
    while($row = mysqli_fetch_assoc($all_playlists_result)) {
        $all_playlists[] = $row;
    }
}

// Get feedback
$feedback_sql = "SELECT f.*, u.username FROM feedbacks f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_sql);
$all_feedback = array();
if (mysqli_num_rows($feedback_result) > 0) {
    while($row = mysqli_fetch_assoc($feedback_result)) {
        $all_feedback[] = $row;
    }
}

// Handle add song
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_song'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $artist = mysqli_real_escape_string($conn, $_POST['artist']);
    $album = mysqli_real_escape_string($conn, $_POST['album']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    
    $sql = "INSERT INTO songs (title, artist, album, genre, duration) VALUES ('$title', '$artist', '$album', '$genre', '$duration')";
    if (mysqli_query($conn, $sql)) {
        $song_success = "Song added successfully!";
    } else {
        $song_error = "Error: " . mysqli_error($conn);
    }
}

// Handle delete actions
if (isset($_GET['delete'])) {
    $type = $_GET['delete'];
    $id = $_GET['id'];
    
    switch($type) {
        case 'user':
            $sql = "DELETE FROM users WHERE id = $id";
            break;
        case 'song':
            $sql = "DELETE FROM songs WHERE id = $id";
            break;
        case 'feedback':
            $sql = "DELETE FROM feedbacks WHERE id = $id";
            break;
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - mymelody</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #9e7bff;
            --secondary: #c8b5ff;
            --accent: #ffd166;
            --text: #5a5a5a;
            --light: #f5f7fa;
            --card-bg: #ffffff;
            --shadow: rgba(158, 123, 255, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }