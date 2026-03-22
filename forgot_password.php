<?php
session_start();
if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = "";
}

$error = array();
$success = "";

if (isset($_POST['submit'])) {
    require_once("function/security.php");
    $email = sanitize_input($_POST['email']);

    if (empty($email)) {
        $error[0] = "<p class=\"error-message\">Please enter your email address</p>";
    } else {
        $pattern_mail = '/^[a-z0-9_.+-]+@[a-z0-9-]+\.[a-z]{2,6}$/i';
        if (!preg_match($pattern_mail, $email)) {
            $error[1] = "<p class=\"error-message\">Please enter a valid email address</p>";
        }
    }

    if (count($error) == 0) {
        require("settings.php");
        $conn = new mysqli($host, $user, $pswd);
        if ($conn->connect_error) {
            $error[2] = "<p class=\"error-message\">Connection failed: " . $conn->connect_error . "</p>";
        } else {
            @$conn->select_db($dbnm);

            // Check if email exists
            $stmt = $conn->prepare("SELECT friend_id, profile_name FROM $table WHERE friend_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_id = $row['friend_id'];
                $user_name = $row['profile_name'];

                // Generate reset token
                $reset_token = substr(md5(uniqid(rand(), true)), 0, 32);
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Create reset_tokens table if not exists
                $create_table = "CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    expires DATETIME NOT NULL,
                    used TINYINT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                $conn->query($create_table);

                // Delete old tokens for this user
                $delete_old = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $delete_old->bind_param("s", $email);
                $delete_old->execute();

                // Insert new reset token
                $insert_token = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expires) VALUES (?, ?, ?, ?)");
                $insert_token->bind_param("isss", $user_id, $email, $reset_token, $expires);

                if ($insert_token->execute()) {
                    // Send email
                    $reset_link = "https://mercury.swin.edu.au/cos30020/s104176358/assign2/reset_password.php?token=" . $reset_token;

                    $to = $email;
                    $subject = "Reset Your Password - My Friend System";
                    $message = "
                    <html>
                    <head>
                        <title>Reset Your Password</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
                            .header { color: #1877f2; text-align: center; }
                            .button { 
                                background: #1877f2 !important; 
                                color: white !important; 
                                padding: 15px 30px; 
                                text-decoration: none; 
                                border-radius: 8px; 
                                display: inline-block; 
                                font-weight: bold;
                                border: none;
                            }
                            .footer { font-size: 12px; color: #666; text-align: center; margin-top: 30px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2 class='header'>Password Reset Request</h2>
                            <p>Hello <strong>$user_name</strong>,</p>
                            <p>We received a request to reset your password for your My Friend System account.</p>
                            <p>Click the button below to reset your password:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='$reset_link' class='button'> Reset Password</a>
                            </div>
                            <p>Or copy and paste this link into your browser:</p>
                            <p style='word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 5px; color: #333;'>$reset_link</p>
                            <p><strong>This link will expire in 1 hour.</strong></p>
                            <p>If you did not request this password reset, please ignore this email.</p>
                            <hr style='margin: 30px 0;'>
                            <div class='footer'>
                                This email was sent from My Friend System<br>
                                From: khaipqsws00431@fpt.edu.vn<br>
                                © 2025 My Friend System. All rights reserved.
                            </div>
                        </div>
                    </body>
                    </html>
                    ";

                    // Email headers
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: My Friend System <khaipqsws00431@fpt.edu.vn>" . "\r\n";
                    $headers .= "Reply-To: khaipqsws00431@fpt.edu.vn" . "\r\n";

                    if (mail($to, $subject, $message, $headers)) {
                        $success = "<p class=\"success\">Password reset link has been sent to your email!</p>";
                    } else {
                        $error[3] = "<p class=\"error-message\">Failed to send email. Please try again later.</p>";
                    }
                } else {
                    $error[4] = "<p class=\"error-message\">Failed to generate reset token. Please try again.</p>";
                }
            } else {
                // Don't reveal if email exists or not for security
                $success = "<p class=\"success\">If this email exists in our system, a password reset link has been sent!</p>";
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
    <meta name="description" content="Forgot Password - Reset your My Friend System password" />
    <link rel="stylesheet" href="styles.css" />
    <title>Forgot Password - My Friend System</title>
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
            <h1>Forgot Your Password?</h1>
        </header>

        <div class="form-container">
            <form action="forgot_password.php" method="post">
                <h2 style="color: #1877f2; text-align: center; margin-bottom: 30px;">🔐 Reset Your Password</h2>

                <p style="text-align: center; color: #65676b; margin-bottom: 25px;">
                    Enter your email address and we'll send you a link to reset your password.
                </p>

                <?php
                if (!empty($success)) {
                    echo '<div class="message success">' . $success . '</div>';
                }
                foreach ($error as $err) {
                    echo '<div class="message error-message">' . $err . '</div>';
                }
                ?>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your registered email address"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        required />
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-full">
                    📧 Send Reset Link
                </button>

                <div style="text-align: center; margin-top: 25px;">
                    <p style="color: #65676b;">
                        Remember your password?
                        <a href="login.php" style="color: #1877f2; text-decoration: none; font-weight: 600;">
                            Back to Login
                        </a>
                    </p>
                    <p style="color: #65676b; margin-top: 10px;">
                        Don't have an account?
                        <a href="signup.php" style="color: #1877f2; text-decoration: none; font-weight: 600;">
                            Create Account
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>