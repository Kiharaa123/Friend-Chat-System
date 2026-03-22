<?php
session_start();
if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = "";
}

$error = array();
$success = "";
$token_valid = false;
$token = "";
$user_email = "";

// Check if token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    require("settings.php");
    $conn = new mysqli($host, $user, $pswd);
    @$conn->select_db($dbnm);

    // Verify token
    $stmt = $conn->prepare("SELECT email, expires, used FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_email = $row['email'];
        $expires = $row['expires'];
        $used = $row['used'];

        // Check if token is expired or used
        if ($used == 1) {
            $error[0] = "<p class=\"error-message\">This reset link has already been used.</p>";
        } elseif (strtotime($expires) < time()) {
            $error[1] = "<p class=\"error-message\">This reset link has expired. Please request a new one.</p>";
        } else {
            $token_valid = true;
        }
    } else {
        $error[2] = "<p class=\"error-message\">Invalid reset link.</p>";
    }
    $conn->close();
} else {
    $error[3] = "<p class=\"error-message\">No reset token provided.</p>";
}

// Handle password reset form submission
if (isset($_POST['submit']) && $token_valid) {
    require_once("function/security.php");
    $new_password = sanitize_input($_POST['new_password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $error[4] = "<p class=\"error-message\">Please fill in all fields.</p>";
    } elseif ($new_password !== $confirm_password) {
        $error[5] = "<p class=\"error-message\">Passwords do not match.</p>";
    } else {
        $pattern_pass = '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9]{1,8}$/';
        if (!preg_match($pattern_pass, $new_password)) {
            $error[6] = "<p class=\"error-message\">Password must contain letters and numbers (max 8 characters).</p>";
        } else {
            require("settings.php");
            $conn = new mysqli($host, $user, $pswd);
            @$conn->select_db($dbnm);

            // Hash new password
            $hashed_password = hash_password($new_password);

            // Update password
            $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE friend_email = ?");
            $stmt->bind_param("ss", $hashed_password, $user_email);

            if ($stmt->execute()) {
                // Mark token as used
                $mark_used = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $mark_used->bind_param("s", $token);
                $mark_used->execute();

                $success = "<p class=\"success\">✅ Password reset successfully! You can now login with your new password.</p>";
                $token_valid = false; // Hide form after successful reset
            } else {
                $error[7] = "<p class=\"error-message\">Failed to update password. Please try again.</p>";
            }
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Reset Password - Set your new My Friend System password" />
    <link rel="stylesheet" href="styles.css" />
    <title>Reset Password - My Friend System</title>
</head>

<body class="login-main">
    <div class="container">
        <header>
            <nav>
                <a class="nav-logo" href="index.php">👥 My Friend System</a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Register</a>
                    <a href="about.php">About</a>
                </div>
            </nav>
            <h1>Reset Your Password</h1>
        </header>

        <div class="form-container">
            <?php if ($token_valid): ?>
                <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                    <h2 style="color: #1877f2; text-align: center; margin-bottom: 30px;">🔐 Create New Password</h2>

                    <p style="text-align: center; color: #65676b; margin-bottom: 25px;">
                        Enter your new password for <strong><?php echo htmlspecialchars($user_email); ?></strong>
                    </p>

                    <?php
                    foreach ($error as $err) {
                        echo '<div class="message error-message">' . $err . '</div>';
                    }
                    ?>

                    <div class="form-group">
                        <label for="new_password">🔒 New Password</label>
                        <input type="password" name="new_password" id="new_password" placeholder="Enter your new password"
                            required />
                        <small style="color: #65676b; font-size: 12px;">
                            Password must contain letters and numbers (max 8 characters)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">🔐 Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password"
                            placeholder="Confirm your new password" required />
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-full">
                        🔄 Reset Password
                    </button>
                </form>
            <?php else: ?>
                <div style="text-align: center;">
                    <h2 style="color: #FFFFFF; margin-bottom: 30px;">🔐 Reset Password</h2>

                    <?php
                    if (!empty($success)) {
                        echo '<div class="message success">' . $success . '</div>';
                        echo '<div style="margin-top: 25px;">
                                <a href="login.php" class="btn btn-primary">🚀 Login Now</a>
                              </div>';
                    } else {
                        foreach ($error as $err) {
                            echo '<div class="message error-message">' . $err . '</div>';
                        }
                        echo '<div style="margin-top: 25px;">
                                <a href="forgot_password.php" class="btn btn-primary">📧 Request New Reset Link</a>
                              </div>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 25px;">
                <p style="color: #65676b;">
                    <a href="login.php" style="color: #1877f2; text-decoration: none; font-weight: 600;">
                        ← Back to Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>