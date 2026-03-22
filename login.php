<?php
session_set_cookie_params(1800);
session_start();
if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = "";
}
if ($_SESSION['status'] == "logged") {
    $_SESSION['message'] = "<p class=\"remind-message\">You need to logout first</p>";
    header("Location: friendlist.php");
}
$error = array();

if (isset($_POST['submit'])) {
    require("function/security.php");
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    if (empty($email)) {
        $error[0] = "<p class=\"error-message\">Please fill email</p>";
    }
    if (empty($password)) {
        $error[1] = "<p class=\"error-message\">Please fill in password</p>";
    }
    if (count($error) == 0) {
        require("settings.php");
        $conn = new mysqli($host, $user, $pswd);
        if ($conn->connect_error) {
            $error[3] = "Connection failed: " . $conn->connect_error;
        }
        @$conn->select_db($dbnm);
        $stmt = $conn->prepare("SELECT password, profile_name FROM $table WHERE friend_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_row();
            if (verify_password($password, $row[0])) {
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $row[1];
                $_SESSION['status'] = "logged";
                $_SESSION['message'] = "<p class=\"success\">Login Successfully</p>";
                header("Location: friendlist.php");
            } else {
                $error[5] = "<p class=\"error-message\">Incorrect password</p>";
            }
        } else {
            $error[4] = "<p class=\"error-message\">Invalid email</p>";
        }
        $conn->close();
    }
    $_SESSION['email'] = $email;
}

if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = "";
}
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Login to My Friend System - Access your social network" />
    <meta name="keywords" content="login, sign in, friend system, social network" />
    <link rel="stylesheet" href="styles.css" />
    <title>Login - My Friend System</title>
</head>

<body class="login-main">
    <div class="container">
        <header>
            <nav>
                <a class="nav-logo" href="index.php">👥 My Friend System</a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="login.php" class="active">Login</a>
                    <a href="signup.php">Register</a>
                    <a href="about.php">About</a>
                </div>
            </nav>
            <h1>Welcome Back!</h1>
        </header>

        <div class="form-container">
            <form action="login.php" method="post">
                <h2 style="color: #1877f2; text-align: center; margin-bottom: 30px;">🔐 Login to Your Account</h2>

                <?php
                if (isset($error[3]))
                    echo '<div class="message error-message">' . $error[3] . '</div>';
                if (isset($error[4]))
                    echo '<div class="message error-message">' . $error[4] . '</div>';
                if (isset($error[5]))
                    echo '<div class="message error-message">' . $error[5] . '</div>';
                if (isset($error[1]))
                    echo '<div class="message error-message">' . $error[1] . '</div>';
                ?>

                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input type="text" name="email" id="email" value="<?php echo $email ?>"
                        placeholder="Enter your email address" required />
                </div>
                <?php if (isset($error[0]))
                    echo '<div class="message error-message">' . $error[0] . '</div>'; ?>

                <div class="form-group">
                    <label for="password">🔒 Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required />
                    <div style="text-align: right; margin-top: 8px;">
                        <a href="forgot_password.php" style="color: #1877f2; text-decoration: none; font-size: 14px;">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-full">🚀 Login Now</button>

                <div style="text-align: center; margin-top: 25px;">
                    <p style="color: #65676b;">
                        Don't have an account yet?
                        <a href="signup.php" style="color: #1877f2; text-decoration: none; font-weight: 600;">Create
                            Account</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>