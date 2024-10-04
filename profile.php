<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_type = $_SESSION['user_type'];

$email = "";
$email_err = "";
$bio = "";
$genre = "";

// Fetch user email from the database
$sql = "SELECT ue.Email FROM UserEmail ue WHERE ue.id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $email);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Fetch artist profile if user is an artist
if ($user_type == 'Artist') {
    $sql = "SELECT bio, genre FROM ArtistProfile WHERE artist_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $bio, $genre);
            mysqli_stmt_fetch($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_email'])) {
        // Update email
        $email = sanitizeInput($_POST["email"]);
        
        if (empty($email)) {
            $email_err = "Please enter an email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        } else {
            $sql = "SELECT id FROM UserEmail WHERE Email = ? AND id != ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $param_email, $user_id);
                $param_email = $email;
                
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        $email_err = "This email is already taken.";
                    } else {
                        $sql = "UPDATE UserEmail SET Email = ? WHERE id = ?";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "si", $param_email, $user_id);
                            $param_email = $email;
                            
                            if (mysqli_stmt_execute($stmt)) {
                                echo "Email updated successfully.";
                            } else {
                                echo "Something went wrong. Please try again later.";
                            }
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    if (isset($_POST['update_profile'])) {
        // Update artist profile (if applicable)
        if ($user_type == 'Artist') {
            $bio = sanitizeInput($_POST["bio"]);
            $genre = sanitizeInput($_POST["genre"]);
            
            // Update artist profile in the database
            $sql = "UPDATE user SET bio = ?, genre = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssi", $param_bio, $param_genre, $user_id);
                $param_bio = $bio;
                $param_genre = $genre;
                
                if (mysqli_stmt_execute($stmt)) {
                    echo "Profile updated successfully.";
                } else {
                    echo "Something went wrong. Please try again later.";
                }
            }
        }
    }
}

// Fetch user's liked songs (for Listeners) or uploaded songs (for Artists)
$songs = [];
if ($user_type == 'Listener') {
    $sql = "SELECT s.id, s.title, s.URL, s.cover_photo_url, u.F_name, u.L_name 
            FROM Song s 
            JOIN Likes l ON s.id = l.song_id 
            JOIN User u ON s.artist_id = u.id
            WHERE l.user_id = ?";
} else if ($user_type == 'Artist') {
    $sql = "SELECT id, title, URL, approval, cover_photo_url
            FROM Song 
            WHERE artist_id = ?";
}

if (isset($sql) && $stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
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
    <title>Profile - Music Streaming Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General styles */
        .container {
            display: flex;
            flex-wrap: wrap;
        }
        .sidebar {
            width: 100%;
            max-width: 200px;
            padding: 20px;
            background-color: #1a1a1a;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .logo img {
            width: 100%;
            height: auto;
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
                <img src="logo.png" alt="Phonic Hub Logo">
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
            <h1>Profile</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" name="update_email" class="btn btn-primary" value="Update Email">
                </div>

                <?php if ($user_type == 'Artist'): ?>
                    <h2>User Profile</h2>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" class="form-control"><?php echo htmlspecialchars($bio); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" class="form-control" value="<?php echo htmlspecialchars($genre); ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="update_profile" class="btn btn-primary" value="Update Profile">
                </div>
                <?php endif; ?>
            </form>

            <?php if ($user_type == 'Listener'): ?>
                <h2>Your Liked Songs</h2>
                <div class="song-grid">
                    <?php if (empty($songs)): ?>
                        <p>No liked songs found.</p>
                    <?php else: ?>
                        <?php foreach ($songs as $song): ?>
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
            <?php elseif ($user_type == 'Artist'): ?>
                <h2>Your Uploaded Songs</h2>
                <div class="song-grid">
                    <?php if (empty($songs)): ?>
                        <p>No uploaded songs found.</p>
                    <?php else: ?>
                        <?php foreach ($songs as $song): ?>
                            <div class="song-card">
                            <img src="<?php echo htmlspecialchars($song['cover_photo_url']); ?>" alt="Album Art">
                                <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                                <audio controls>
                                    <source src="<?php echo htmlspecialchars($song['URL']); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <p>Status: <?php echo $song['approval'] ? 'Approved' : 'Pending'; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>