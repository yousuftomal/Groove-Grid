<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Function to check if the user already has an active subscription
function isSubscribed($user_id, $conn) {
    $sql = "SELECT * FROM subscription WHERE id = ? AND CURDATE() BETWEEN start_date AND end_date";
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

// Handle subscription submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscription_type'])) {
    $subscription_type = $_POST['subscription_type'];

    // Check if user already has an active subscription
    if (isSubscribed($user_id, $conn)) {
        $subscription_message = "You already have an active subscription.";
    } else {
        // Calculate start and end dates based on subscription type
        $start_date = date('Y-m-d');
        if ($subscription_type == 'Monthly') {
            $end_date = date('Y-m-d', strtotime('+1 month'));
        } elseif ($subscription_type == '6-Months') {
            $end_date = date('Y-m-d', strtotime('+6 months'));
        } elseif ($subscription_type == 'Yearly') {
            $end_date = date('Y-m-d', strtotime('+1 year'));
        } else {
            $subscription_message = "Invalid subscription type.";
        }

        if (!isset($subscription_message)) {
            // Insert subscription into the database
            $sql = "INSERT INTO Subscription (id, type, start_date, end_date) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "isss", $user_id, $subscription_type, $start_date, $end_date);
                if (mysqli_stmt_execute($stmt)) {
                    $subscription_message = "Subscription successful!";
                } else {
                    $subscription_message = "Error: Could not subscribe.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe - Music Streaming Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Subscribe to a Plan</h1>

        <?php if (isset($subscription_message)): ?>
            <p><?php echo $subscription_message; ?></p>
        <?php endif; ?>

        <?php if (!isSubscribed($user_id, $conn)): ?>
            <form action="subscribe.php" method="post">
                <div class="form-group">
                    <label for="subscription_type">Choose a subscription plan:</label>
                    <select name="subscription_type" id="subscription_type" class="form-control" required>
                        <option value="Monthly">Monthly - $9.99</option>
                        <option value="6-Months">6-Months - $49.99</option>
                        <option value="Yearly">Yearly - $89.99</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary">Go Back to Dashboard</a>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>