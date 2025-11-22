<?php
require_once 'config.php';

// Get top songs
$top_songs_sql = "SELECT * FROM songs ORDER BY plays DESC LIMIT 10";
$top_songs_result = mysqli_query($conn, $top_songs_sql);
$top_songs = array();
if (mysqli_num_rows($top_songs_result) > 0) {
    while($row = mysqli_fetch_assoc($top_songs_result)) {
        $top_songs[] = $row;
    }
}

// Get most loved songs
$loved_songs_sql = "SELECT * FROM songs ORDER BY likes DESC LIMIT 10";
$loved_songs_result = mysqli_query($conn, $loved_songs_sql);
$loved_songs = array();
if (mysqli_num_rows($loved_songs_result) > 0) {
    while($row = mysqli_fetch_assoc($loved_songs_result)) {
        $loved_songs[] = $row;
    }
}

// Get top artists
$artists_sql = "SELECT artist, COUNT(*) as song_count, SUM(plays) as total_plays FROM songs GROUP BY artist ORDER BY total_plays DESC LIMIT 5";
$artists_result = mysqli_query($conn, $artists_sql);
$top_artists = array();
if (mysqli_num_rows($artists_result) > 0) {
    while($row = mysqli_fetch_assoc($artists_result)) {
        $top_artists[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Hits - mymelody</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add your CSS styles from hits.html here */
        :root {
            --primary: #ff9ec8;
            --secondary: #b5e8ff;
            --accent: #ffd166;
            --text: #5a5a5a;
            --light: #fff9fc;
            --card-bg: #ffffff;
            --shadow: rgba(255, 158, 200, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px var(--shadow);
            border-radius: 0 0 25px 25px;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
        }
        
        .search-form {
            display: flex;
            width: 100%;
            max-width: 500px;
            margin-top: 10px;
        }
        
        .search-form input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 30px 0 0 30px;
        }
        
        .search-form button {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
        }
        
        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .chart-section {
            background-color: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--shadow);
        }
        
        .song-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .song-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f8f8f8;
            border-radius: 15px;
        }
        
        .song-rank {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            width: 40px;
            text-align: center;
            margin-right: 15px;
        }
        
        .song-cover {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            margin-right: 15px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .song-info {
            flex: 1;
        }
        
        .song-stats {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #888;
            font-size: 0.9rem;
        }
        
        .artist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .artist-card {
            background: linear-gradient(135deg, var(--secondary), #d4f2ff);
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
        }
        
        .artist-img {
            width: 100%;
            height: 150px;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            border-radius: 25px 25px 0 0;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-music"></i>
                <h1>mymelody</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="main.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="hits.php" class="active"><i class="fas fa-chart-line"></i> Monthly Hits</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
            <form class="search-form" action="main.php" method="POST">
                <input type="text" name="query" placeholder="Search for songs, artists, or playlists...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </header>
    
    <main>
        <div class="page-title">
            <i class="fas fa-trophy"></i>
            <h2>Monthly Hits</h2>
        </div>
        
        <div class="charts-container">
            <section class="chart-section">
                <h3><i class="fas fa-globe-americas"></i> Global Top 10</h3>
                <div class="song-list">
                    <?php foreach($top_songs as $index => $song): ?>
                    <div class="song-item">
                        <div class="song-rank <?php echo $index < 3 ? 'top-3' : ''; ?>"><?php echo $index + 1; ?></div>
                        <div class="song-cover">
                            <i class="fas fa-music"></i>
                        </div>
                        <div class="song-info">
                            <h4><?php echo htmlspecialchars($song['title']); ?></h4>
                            <p><?php echo htmlspecialchars($song['artist']); ?></p>
                        </div>
                        <div class="song-stats">
                            <span><i class="fas fa-play"></i> <?php echo number_format($song['plays']); ?></span>
                            <span><i class="fas fa-heart"></i> <?php echo number_format($song['likes']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <section class="chart-section">
                <h3><i class="fas fa-heart"></i> Most Loved</h3>
                <div class="song-list">
                    <?php foreach($loved_songs as $index => $song): ?>
                    <div class="song-item">
                        <div class="song-rank <?php echo $index < 3 ? 'top-3' : ''; ?>"><?php echo $index + 1; ?></div>
                        <div class="song-cover">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="song-info">
                            <h4><?php echo htmlspecialchars($song['title']); ?></h4>
                            <p><?php echo htmlspecialchars($song['artist']); ?></p>
                        </div>
                        <div class="song-stats">
                            <span><i class="fas fa-play"></i> <?php echo number_format($song['plays']); ?></span>
                            <span><i class="fas fa-heart"></i> <?php echo number_format($song['likes']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
        
        <section class="chart-section">
            <h3><i class="fas fa-crown"></i> Top Artists</h3>
            <div class="artist-grid">
                <?php foreach($top_artists as $artist): ?>
                <div class="artist-card">
                    <div class="artist-img">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="artist-info">
                        <h4><?php echo htmlspecialchars($artist['artist']); ?></h4>
                        <p><?php echo $artist['song_count']; ?> songs • <?php echo number_format($artist['total_plays']); ?> plays</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="social-icons">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
            <p>&copy; 2024 mymelody. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>