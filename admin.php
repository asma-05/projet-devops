<?php

require 'config.php';
if (!is_dir('uploads')) {
    mkdir('uploads');
}

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

function res($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function intValReq($v) {
    return isset($v) ? intval($v) : 0;
}

if ($method === 'GET' && $action === 'fetch') {
    $out = [];
    $tables = [
        'users' => 'users',
        'songs' => 'songs',
        'playlists' => 'playlists',
        'premium_users' => 'premium_users',
        'feedbacks' => 'feedbacks'
    ];
    foreach ($tables as $k => $table) {
        if ($table === 'premium_users') {

            $sql = "SELECT pu.*, u.username FROM premium_users pu LEFT JOIN users u ON u.id = pu.user_id ORDER BY pu.id DESC";
            $q = mysqli_query($conn, $sql);
        } else {
            $q = mysqli_query($conn, "SELECT * FROM $table ORDER BY id DESC");
        }

        if (!$q) {
            $out[$k] = [];
            continue;
        }
        $out[$k] = mysqli_fetch_all($q, MYSQLI_ASSOC);
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
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $artist = isset($_POST['artist']) ? $_POST['artist'] : '';
    $album = isset($_POST['album']) ? $_POST['album'] : '';
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $duration = isset($_POST['duration']) ? $_POST['duration'] : '';

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        res(['success' => false, 'error' => 'File upload missing or failed'], 400);
    }

    $filename = basename($_FILES['file']['name']);
    $destination = 'uploads/' . $filename;
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
        res(['success' => false, 'error' => 'Failed to move uploaded file'], 500);
    }


    $stmt = mysqli_prepare($conn, "INSERT INTO songs (title, artist, album, genre, duration, file_path) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) res(['success' => false, 'error' => 'Prepare failed: '.mysqli_error($conn)], 500);
    mysqli_stmt_bind_param($stmt, 'ssssss', $title, $artist, $album, $genre, $duration, $filename);
    if (!mysqli_stmt_execute($stmt)) {
        res(['success' => false, 'error' => 'Insert failed: '.mysqli_stmt_error($stmt)], 500);
    }
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
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $id = intValReq(isset($_POST['id']) ? $_POST['id'] : null);
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

<?php
function getVal($row, $candidates, $default = '') {
    foreach ($candidates as $c) {
        if (isset($row[$c]) && $row[$c] !== null && $row[$c] !== '') return $row[$c];
    }
    return $default;
}

$users_res = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$songs_res = mysqli_query($conn, "SELECT * FROM songs ORDER BY id DESC");
$playlists_res = mysqli_query($conn, "SELECT * FROM playlists ORDER BY id DESC");
$premium_res = mysqli_query($conn, "SELECT * FROM premium_users ORDER BY user_id DESC");
$feedbacks_res = mysqli_query($conn, "SELECT * FROM feedbacks ORDER BY id DESC");
?>
<?php

$u_res = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
 while ($u = mysqli_fetch_assoc($u_res)) {
$uid = getVal($u, ['id']);
$uname = getVal($u, ['username','name']);
$uemail = getVal($u, ['email']);
 $join = getVal($u, ['created_at','join_date','reg_date'], '-');
$status = getVal($u, ['status','active'], '-');
echo "<tr>\n";
echo "<td>".htmlspecialchars($uid)."</td>";
echo "<td>".htmlspecialchars($uname)."</td>";
echo "<td>".htmlspecialchars($uemail)."</td>";
echo "<td>".htmlspecialchars($join)."</td>";
echo "<td>".htmlspecialchars($status)."</td>";
echo "<td>\n";
echo "<form method='POST' style='display:inline'>\n";
echo "<input type='hidden' name='delete_user' value='".htmlspecialchars($uid)."'>\n";
echo "<button type='submit' class='action-btn btn-danger' onclick='return confirm(".'"Delete user?"'.")'><i class='fas fa-trash'></i></button>\n";
echo "</form>\n";
echo "</td>\n";
echo "</tr>\n";
}
?>
<?php
$p_res = mysqli_query($conn, "SELECT * FROM premium_users ORDER BY user_id DESC");
while ($p = mysqli_fetch_assoc($p_res)) {
    $pid = getVal($p, ['user_id','id']);
    $puser = getVal($p, ['username']);
    $plan = getVal($p, ['plan']);
    $start = getVal($p, ['start_date']);
    $renew = getVal($p, ['renewal_date']);
    $pstatus = getVal($p, ['status']);
    echo "<tr>";
  echo "<td>".htmlspecialchars($pid)."</td>";
  echo "<td>".htmlspecialchars($puser)."</td>";
 echo "<td>".htmlspecialchars($plan)."</td>";
 echo "<td>".htmlspecialchars($start)."</td>";
 echo "<td>".htmlspecialchars($renew)."</td>";
 echo "<td>".htmlspecialchars($pstatus)."</td>";
echo "<td>\n<form method='POST' style='display:inline'>\n";
echo "<input type='hidden' name='delete_premium' value='".htmlspecialchars($pid)."'>\n";
 echo "<button type='submit' class='action-btn btn-warning' onclick='return confirm(".'"Delete premium entry?"'.")'><i class='fas fa-ban'></i></button>\n";
echo "</form>\n</td>\n";
echo "</tr>";
 }
?>


<?php

$s_res = mysqli_query($conn, "SELECT * FROM songs ORDER BY id DESC");
while ($s = mysqli_fetch_assoc($s_res)) {
    $sid = getVal($s, ['id']);
    $title = getVal($s, ['title','name']);
    $artist = getVal($s, ['artist']);
    $genre = getVal($s, ['genre']);
    $duration = getVal($s, ['duration']);
    $plays = getVal($s, ['plays','play_count'], '-');
    ?>
    
    <tr>
        <td><?= htmlspecialchars($sid) ?></td>
        <td><?= htmlspecialchars($title) ?></td>
        <td><?= htmlspecialchars($artist) ?></td>
        <td><?= htmlspecialchars($genre) ?></td>
        <td><?= htmlspecialchars($duration) ?></td>
        <td><?= htmlspecialchars($plays) ?></td>
        <td>
            <form method="POST" style="display:inline">
                <input type="hidden" name="delete_song" value="<?= htmlspecialchars($sid) ?>">
                <button type="submit" class="action-btn btn-danger"
                    onclick="return confirm('Delete song?')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>

    <?php
}
?>

<?php
$pl_res = mysqli_query($conn, "SELECT * FROM playlists ORDER BY id DESC");
while ($pl = mysqli_fetch_assoc($pl_res)) {
    $plid = getVal($pl, ['id']);
    $plname = getVal($pl, ['name','title']);
    $creator = getVal($pl, ['creator']);
    $songs_count = getVal($pl, ['songs','song_count'], '-');
    $followers = getVal($pl, ['followers'], '-');
    $plstatus = getVal($pl, ['status'], '-');
    ?>

    <tr>
        <td><?= htmlspecialchars($plid) ?></td>
        <td><?= htmlspecialchars($plname) ?></td>
        <td><?= htmlspecialchars($creator) ?></td>
        <td><?= htmlspecialchars($songs_count) ?></td>
        <td><?= htmlspecialchars($followers) ?></td>
        <td><?= htmlspecialchars($plstatus) ?></td>
        <td>
            <form method="POST" style="display:inline">
                <input type="hidden" name="delete_playlist" value="<?= htmlspecialchars($plid) ?>">
                <button type="submit" class="action-btn"
                    onclick="return confirm('Delete playlist?')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>

    <?php
}
?>

<?php
$f_res = mysqli_query($conn, "SELECT * FROM feedbacks ORDER BY id DESC");
while ($f = mysqli_fetch_assoc($f_res)) {
    $fid = getVal($f, ['id']);
    $fuser = getVal($f, ['username','user','user_name']);
    $fdate = getVal($f, ['created_at','date']);
    $fcontent = getVal($f, ['content','message','feedback']);
    ?>

    <div class="feedback-item">
        <div class="feedback-header">
            <span class="feedback-user"><?= htmlspecialchars($fuser) ?></span>
            <span class="feedback-date"><?= htmlspecialchars($fdate) ?></span>
        </div>

        <div class="feedback-content">
            <?= nl2br(htmlspecialchars($fcontent)) ?>
        </div>

        <div class="feedback-actions">
            <form method="POST" style="display:inline">
                <input type="hidden" name="delete_feedback" value="<?= htmlspecialchars($fid) ?>">
                <button type="submit" class="action-btn btn-danger"
                    onclick="return confirm('Delete feedback?')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    <?php
}
?>
