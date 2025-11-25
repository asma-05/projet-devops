<?php
require_once 'config.php';
function fetchAll($conn, $sql, $params = [], $types = '')
{
    $stmt = mysqli_prepare($conn, $sql);

    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    return $rows;
}

function incrementPlayCount($conn, $song_id)
{
    $stmt = mysqli_prepare($conn, "UPDATE songs SET plays = plays + 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $song_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$top_songs    = fetchAll($conn, "SELECT * FROM songs ORDER BY plays DESC LIMIT 100");
$loved_songs  = fetchAll($conn, "SELECT * FROM songs ORDER BY likes DESC LIMIT 100");

$top_artists  = fetchAll($conn,
    "SELECT artist, COUNT(*) AS song_count, SUM(plays) AS total_plays 
     FROM songs 
     GROUP BY artist 
     ORDER BY total_plays DESC 
     LIMIT 100"
);


if (isset($_GET['play_song']) && is_numeric($_GET['play_song'])) {

    $song_id = (int) $_GET['play_song'];

    
    $song = fetchAll($conn, "SELECT * FROM songs WHERE id = ?", [$song_id], 'i')[0] ?? null;

    if (!$song) {
        http_response_code(404);
        exit("Song not found");
    }
    incrementPlayCount($conn, $song_id);
    $filename = basename($song['file_path']);
    $possible_paths = [
        __DIR__ . "/uploads/$filename",
        __DIR__ . "/../uploads/$filename",
        __DIR__ . "/musics/$filename",
        __DIR__ . "/../musics/$filename",
        __DIR__ . "/$filename"
    ];

    $final_path = null;

    foreach ($possible_paths as $p) {
        if (file_exists($p)) {
            $final_path = $p;
            break;
        }
    }

    if (!$final_path) {
        http_response_code(500);
        exit("Audio file not found on server");
    }

    
    header("Content-Type: " . mime_content_type($final_path));
    header("Content-Length: " . filesize($final_path));
    header("Accept-Ranges: bytes");

    readfile($final_path);
    exit;
}


if (isset($_GET['get_songs'])) {

    $merged = array_merge($top_songs, $loved_songs);
    $unique = [];

    foreach ($merged as $s) {
        $unique[$s['id']] = $s;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success'     => true,
        'songs'       => array_values($unique),
        'top_artists' => $top_artists
    ]);
    exit;
}
?>
