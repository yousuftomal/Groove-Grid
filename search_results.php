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

// Get the search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Search for songs and artists
$search_results = [];
if (!empty($query)) {
    // Split the query into words
    $words = explode(' ', $query);
    $like_conditions = [];
    $params = [];
    
    foreach ($words as $word) {
        $like_conditions[] = "s.title LIKE ?";
        $like_conditions[] = "u.F_name LIKE ?";
        $like_conditions[] = "u.L_name LIKE ?";
        $params[] = "%$word%";
        $params[] = "%$word%";
        $params[] = "%$word%";
    }
    
    $like_clause = implode(' OR ', $like_conditions);
    
    $sql = "SELECT s.id, s.title, s.URL, u.F_name, u.L_name 
            FROM Song s 
            JOIN User u ON s.artist_id = u.id 
            WHERE s.approval = 1 AND ($like_clause)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Music Streaming Platform</title>
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
    </style>
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
            <h1>Search Results</h1>

            <div class="search-container">
                <form action="search_results.php" method="GET">
                    <input type="text" name="query" class="search-box" placeholder="Search songs or artists" value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>

            <div class="song-grid">
                <?php if (empty($search_results)): ?>
                    <p>No results found for "<?php echo htmlspecialchars($query); ?>".</p>
                <?php else: ?>
                    <?php foreach ($search_results as $song): ?>
                        <div class="song-card">
                            <img src="placeholder-album-art.jpg" alt="Album Art">
                            <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                            <p>Artist: <?php echo htmlspecialchars($song['F_name'] . ' ' . $song['L_name']); ?></p>
                            <audio controls>
                                <source src="<?php echo htmlspecialchars($song['URL']); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>