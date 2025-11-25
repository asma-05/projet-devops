<?php
require 'config.php';

if (!is_dir('uploads')) mkdir('uploads');

header('Content-Type: application/json; charset=utf-8');


function res($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getVal($row, $keys, $default = '') {
    foreach ($keys as $k) {
        if (!empty($row[$k])) return $row[$k];
    }
    return $default;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

if ($method === 'GET' && $action === 'fetch') {
    $tables = [
        'users' => 'users',
        'songs' => 'songs',
        'playlists' => 'playlists',
        'premium_users' => 'premium_users',
        'feedbacks' => 'feedbacks'
    ];
    $out = [];
    foreach ($tables as $key => $table) {
        if ($table === 'premium_users') {
            $q = mysqli_query($conn, "SELECT pu.*, u.username FROM premium_users pu LEFT JOIN users u ON u.id = pu.user_id ORDER BY pu.id DESC");
        } else {
            $q = mysqli_query($conn, "SELECT * FROM $table ORDER BY id DESC");
        }
        $out[$key] = $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }
    $out['counts'] = [
        'users' => count($out['users']),
        'premium' => count($out['premium_users']),
        'songs' => count($out['songs']),
        'playlists' => count($out['playlists'])
    ];
    res(['success' => true, 'data' => $out]);
}

if ($method === 'POST' && $action === 'add_song') {
    $title = $_POST['title'] ?? '';
    $artist = $_POST['artist'] ?? '';
    $album = $_POST['album'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $duration = $_POST['duration'] ?? '';

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        res(['success' => false, 'error' => 'File upload missing or failed'], 400);
    }

    $filename = basename($_FILES['file']['name']);
    $destination = 'uploads/' . $filename;
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
        res(['success' => false, 'error' => 'Failed to move uploaded file'], 500);
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO songs (title, artist, album, genre, duration, file_path) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssss', $title, $artist, $album, $genre, $duration, $filename);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    res(['success' => true, 'message' => 'Song added']);
}

$delete_map = [
    'delete_user' => "DELETE FROM users WHERE id=%d",
    'delete_song' => "DELETE FROM songs WHERE id=%d",
    'delete_playlist' => "DELETE FROM playlists WHERE id=%d",
    'delete_premium' => "DELETE FROM premium_users WHERE user_id=%d",
    'delete_feedback' => "DELETE FROM feedbacks WHERE id=%d",
];

if ($method === 'POST' && $action === 'delete') {
    $type = $_POST['type'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (!$type || !$id || !isset($delete_map[$type])) {
        res(['success' => false, 'error' => 'Invalid delete request'], 400);
    }

    $sql = sprintf($delete_map[$type], $id);
    if (!mysqli_query($conn, $sql)) {
        res(['success' => false, 'error' => mysqli_error($conn)], 500);
    }

    res(['success' => true, 'message' => 'Deleted']);
}

res(['success' => false, 'error' => 'No valid action supplied'], 400);
?>
