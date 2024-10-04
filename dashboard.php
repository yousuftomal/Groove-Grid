<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_type = $_SESSION['user_type'];

// Function to check if the listener has an active subscription
function isSubscribed($user_id, $conn) {
    $sql = "SELECT * FROM Subscription WHERE id = ? AND CURDATE() BETWEEN start_date AND end_date";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            return true; // User has an active subscription
        } else {
            return false; // No active subscription
        }
    }
    return false; // Default to no subscription
}

// Enforce subscription check for listeners
if ($user_type == 'Listener' && !isSubscribed($user_id, $conn)) {
    // Display message and link to subscription page
    echo "<div class='container'><h1>You need an active subscription to listen to songs. Please <a href='subscribe.php'>subscribe</a> to a plan.</h1></div>";
    exit; // Stop further execution if no subscription
}

// Fetch user's liked songs for listeners only
$liked_songs = [];
if ($user_type == 'Listener') {
    $sql = "SELECT s.id, s.title, s.URL,s.cover_photo_url, u.F_name, u.L_name 
            FROM Song s 
            JOIN Likes l ON s.id = l.song_id 
            JOIN User u ON s.artist_id = u.id
            WHERE l.user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $liked_songs[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all songs for listeners, user's songs for artists, or all approved songs for admins
$songs = [];
if ($user_type == 'Listener') {
    $sql = "SELECT s.id, s.title, s.URL,s.cover_photo_url, u.F_name, u.L_name,
            CASE WHEN l.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_liked
            FROM Song s 
            JOIN User u ON s.artist_id = u.id 
            LEFT JOIN Likes l ON s.id = l.song_id AND l.user_id = ?
            WHERE s.approval = 1";
} elseif ($user_type == 'Artist') {
    $sql = "SELECT id, title, URL, approval, cover_photo_url
            FROM Song 
            WHERE artist_id = ?";
} elseif ($user_type == 'Admin') {
    // Fetch all approved songs for admin
    $sql = "SELECT s.id, s.title, s.URL,s.cover_photo_url, u.F_name, u.L_name 
            FROM Song s 
            JOIN User u ON s.artist_id = u.id
            WHERE s.approval = 1";
}

if ($stmt = mysqli_prepare($conn, $sql)) {
    if ($user_type == 'Listener' || $user_type == 'Artist') {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $songs[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Music Streaming Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-container {
    margin: 20px 0;
    text-align: left;
}

.search-box {
    padding: 10px;
    width: 250px;
    border: 2px solid #555;
    border-radius: 25px;
    background-color: #222;
    color: #fff;
    font-size: 16px;
    outline: none;
    transition: all 0.3s ease-in-out;
}

.search-box:focus {
    border-color: #1DB954;
    background-color: #333;
}

.search-button {
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
    background-color: #222;
    color: white;
    font-size: 16px;
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s ease-in-out;
}

.search-button:hover {
    background-color: #1DB954;
}

.search-button:active {
    background-color: #1DB954;
}

        .song-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .song-card {
            background-color: #121212;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(210, 215, 211, 0.2);
            transition: transform 0.3s ease;
        }
        .song-card:hover {
            transform: translateY(-5px);
        }
        .song-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .song-card h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }
        .song-card p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        /* Change the background of the audio player */
.song-card audio {
    width: 100%;
    margin-bottom: 10px;
    
    border-radius: 10px;
}

/* Webkit specific controls - Chrome, Safari */
.song-card audio::-webkit-media-controls-panel {
    background-color: #222; /* Dark background */
    border-radius: 0px;
}

.song-card audio::-webkit-media-controls-play-button,
.song-card audio::-webkit-media-controls-pause-button {
    background-color: #1DB954; /* Custom play/pause button color */
    border-radius: 50%;
}





.song-card audio::-webkit-media-controls-current-time-display,
.song-card audio::-webkit-media-controls-time-remaining-display {
    color: #fff; /* Custom time display text color */
}

        .like-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.2s ease;
        }
        .like-button:hover {
            transform: scale(1.2);
        }
        .liked {
            color: red;
        }
        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
            .main-content {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .song-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 10px;
            }
        }
    </style>
    <script>
        function likeSong(songId, button) {
            fetch('like_song.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'song_id=' + songId
            })
            .then(response => response.text())
            .then(data => {
                button.classList.toggle('liked');
                button.textContent = button.classList.contains('liked') ? '❤️' : '🤍';
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="Phonic Hub Logo" style="width: 150px; height: auto;">
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <?php if ($user_type == 'Artist'): ?>
                        <li><a href="upload.php">Upload Song</a></li>
                    <?php endif; ?>
                    <?php if ($user_type == 'Admin'): ?>
                        <li><a href="approve_songs.php">Approve Songs</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>

            <div class="search-container">
                <form action="search_results.php" method="GET">
                    <input type="text" name="query" class="search-box" placeholder="Search songs or artists">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>

            <!-- Show liked songs only for listeners -->
            <?php if ($user_type == 'Listener'): ?>
                <h2>Your Liked Songs</h2>
                <div class="song-grid">
                    <?php if (empty($liked_songs)): ?>
                        <p>No liked songs found.</p>
                    <?php else: ?>
                        <?php foreach ($liked_songs as $song): ?>
                            <div class="song-card">
                                <img src="<?php echo htmlspecialchars($song['cover_photo_url']); ?>" alt="Album Art">
                                <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                                <p><?php echo htmlspecialchars($song['F_name'] . ' ' . $song['L_name']); ?></p>
                                <audio controls>
                                    <source src="<?php echo htmlspecialchars($song['URL']); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <button class="like-button liked" onclick="likeSong(<?php echo $song['id']; ?>, this)">❤️</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <h2><?php echo $user_type == 'Artist' ? 'Your Songs' : 'All Songs'; ?></h2>
            <div class="song-grid">
                <?php if (empty($songs)): ?>
                    <p>No songs found.</p>
                <?php else: ?>
                    <?php foreach ($songs as $song): ?>
                        <div class="song-card">
                            <img src="<?php echo htmlspecialchars($song['cover_photo_url']); ?>" alt="Album Art">
                            <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                            <?php if ($user_type == 'Listener' || $user_type == 'Admin'): ?>
                                <p>Artist: <?php echo htmlspecialchars($song['F_name'] . ' ' . $song['L_name']); ?></p>
                            <?php endif; ?>
                            <audio controls>
                                <source src="<?php echo htmlspecialchars($song['URL']); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <?php if ($user_type == 'Artist'): ?>
                                <p>Status: <?php echo $song['approval'] ? 'Approved' : 'Pending'; ?></p>
                            <?php elseif ($user_type == 'Listener'): ?>
                                <button class="like-button <?php echo isset($song['is_liked']) && $song['is_liked'] ? 'liked' : ''; ?>" onclick="likeSong(<?php echo $song['id']; ?>, this)">
                                    <?php echo isset($song['is_liked']) && $song['is_liked'] ? '❤️' : '🤍'; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
