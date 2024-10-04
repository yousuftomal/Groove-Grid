<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$f_name = $l_name = $email = $password = $confirm_password = $user_type = "";
$f_name_err = $l_name_err = $email_err = $password_err = $confirm_password_err = $user_type_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    $f_name = sanitizeInput($_POST["f_name"]);
    if (empty($f_name)) {
        $f_name_err = "Please enter your first name.";
    }

    // Validate last name
    $l_name = sanitizeInput($_POST["l_name"]);
    if (empty($l_name)) {
        $l_name_err = "Please enter your last name.";
    }

    // Validate email
    $email = sanitizeInput($_POST["email"]);
    if (empty($email)) {
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT id FROM UserEmail WHERE Email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate user type
    $user_type = sanitizeInput($_POST["user_type"]);
    if (empty($user_type) || !in_array($user_type, ['Listener', 'Artist', 'Admin'])) {
        $user_type_err = "Please select a valid user type.";
    }

    // Check input errors before inserting in database
    if (empty($f_name_err) && empty($l_name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($user_type_err)) {
        $sql = "INSERT INTO User (F_name, L_name, password, User_Type) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_f_name, $param_l_name, $param_password, $param_user_type);
            $param_f_name = $f_name;
            $param_l_name = $l_name;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_user_type = $user_type;
            
            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_insert_id($conn);
                $sql = "INSERT INTO UserEmail (id, Email) VALUES (?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "is", $param_id, $param_email);
                    $param_id = $user_id;
                    $param_email = $email;
                    if (mysqli_stmt_execute($stmt)) {
                        redirect('login.php');
                    } else {
                        echo "Something went wrong. Please try again later.";
                    }
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Music Streaming Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="f_name" class="form-control" value="<?php echo $f_name; ?>" autocomplete="off">
                <span class="error"><?php echo $f_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="l_name" class="form-control" value="<?php echo $l_name; ?>" autocomplete="off">
                <span class="error"><?php echo $l_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" autocomplete="off">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" autocomplete="new-password">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" autocomplete="new-password">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>User Type</label>
                <select name="user_type" class="form-control">
                    <option value="">Select User Type</option>
                    <option value="Listener" <?php if($user_type == "Listener") echo "selected"; ?>>Listener</option>
                    <option value="Artist" <?php if($user_type == "Artist") echo "selected"; ?>>Artist</option>
                    <option value="Admin" <?php if($user_type == "Admin") echo "selected"; ?>>Admin</option>
                </select>
                <span class="error"><?php echo $user_type_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
