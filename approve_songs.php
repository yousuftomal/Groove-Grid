<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || $_SESSION['user_type'] != 'Admin') {
    redirect('login.php');
}

// Fetch songs pending approval
$songs_pending_approval = [];
$sql = "SELECT s.id, s.title, s.URL, s.Release_date, s.language, s.details, u.F_name, u.L_name 
        FROM Song s 
        JOIN User u ON s.artist_id = u.id 
        WHERE s.approval = 0"; // Only select songs that are not approved
if ($stmt = mysqli_prepare($conn, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $songs_pending_approval[] = $row;
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
    <title>Approve Songs - Music Streaming Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">Musicfy</div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="approve_songs.php">Approve Songs</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <h1>Approve Songs</h1>
            <form action="approve_songs_action.php" method="post">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>Details</th>
                <th>Release Date</th>
                <th>Language</th>
                <th>Audio</th>
                <th>Approve</th>
                <th>Reject</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($songs_pending_approval)): ?>
                <?php foreach ($songs_pending_approval as $song): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($song['title']); ?></td>
                        <td><?php echo htmlspecialchars($song['F_name'] . ' ' . $song['L_name']); ?></td>
                        <td><?php echo htmlspecialchars($song['details']); ?></td>
                        <td><?php echo htmlspecialchars($song['Release_date']); ?></td>
                        <td><?php echo htmlspecialchars($song['language']); ?></td>
                        <td>
                            <audio controls>
                                <source src="<?php echo htmlspecialchars($song['URL']); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </td>
                        <td>
                            <button type="submit" name="approve" value="<?php echo htmlspecialchars($song['id']); ?>">Approve</button>
                        </td>
                        <td>
                            <button type="submit" name="reject" value="<?php echo htmlspecialchars($song['id']); ?>">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No songs pending approval.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>

        </div>
    </div>
</body>
</html>
