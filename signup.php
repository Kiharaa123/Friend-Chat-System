<?php
session_set_cookie_params(1800);
session_start();
if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = "";
}
if ($_SESSION['status'] == "logged") {
    $_SESSION['message'] = "<p class=\"remind-message\">You need to logout first</p>";
    header("Location: friendadd.php");
}
$error = array();

if (isset($_POST['submit'])) {
    require_once("function/security.php");
    $email = sanitize_input($_POST['email']);
    $name = sanitize_input($_POST['name']);
    $password = sanitize_input($_POST['password']);
    $re_password = sanitize_input($_POST['re_password']);
    if (!empty($email) && !empty($name) && !empty($password) && !empty($re_password)) {
        $pattern_mail = '/^[a-z0-9_.+-]+@[a-z0-9-]+\.[a-z]{2,6}$/i';
        if (!preg_match($pattern_mail, $email)) {
            $error[1] = "<p class=\"error-message\">Please enter a valid email address</p>";
        }
        $pattern_name = '/^[a-zA-Z ]+$/';
        if (!preg_match($pattern_name, $name)) {
            $error[2] = "<p class=\"error-message\">Name can only contain letters</p>";
        }
        if ($password !== $re_password) {
            $error[3] = "<p class=\"error-message\">Passwords do not match</p>";
        }
        $pattern_pass = '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9]{1,8}$/';
        if (!preg_match($pattern_pass, $password)) {
            $error[4] = "<p class=\"error-message\">Password must contain letters and characters</p>";
        }
        if (count($error) == 0) {
            require_once("settings.php");
            $conn = new mysqli($host, $user, $pswd);
            if ($conn->connect_error) {
                $error[5] = "Connection failed: " . $conn->connect_error;
            }
            @$conn->select_db($dbnm);
            $stmt = $conn->prepare("SELECT friend_id FROM $table WHERE friend_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error[6] = "<p class=\"error-message\">Email already exists</p>";
            } else {
                $row = $result->fetch_row();
            }
            if (count($error) == 0) {
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $date = date("Y-m-d");

                // Hash the password before storing it
                $password = hash_password($password);

                $stmt = $conn->prepare("INSERT INTO $table (friend_email, password, profile_name, date_started, num_of_friends) VALUES (?, ?, ?, ?, 0)");
                $stmt->bind_param("ssss", $email, $password, $name, $date);
                if ($stmt->execute()) {
                    $conn->close();
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $name;
                    $_SESSION['status'] = "logged";
                    $_SESSION['message'] = "<p class=\"success\">You have registered successfully</p>";
                    header("Location: friendadd.php");
                } else {
                    $error[7] = "Error: " . $stmt->error;
                }
            }
            $conn->close();
        } else {
            $error[4] = "<p class=\"error-message\">Password must contain letters and characters</p>";
        }
    } else {
        $error[0] = "<p class=\"error-message\">Please fill in all the fields</p>";
    }
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
}

if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = "";
}
$register_mail = $_SESSION['email'];
if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = "";
}
$register_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Join My Friend System - Create your account and start connecting" />
    <meta name="keywords" content="register, sign up, join, friend system, social network" />
    <link rel="stylesheet" href="styles.css" />
    <title>Register - My Friend System</title>
</head>

<body class="signup-main">
    <div class="container">
        <header>
            <nav>
                <a class="nav-logo" href="index.php">👥 My Friend System</a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="login.php">Login</a>
                    <a href="signup.php" class="active">Register</a>
                    <a href="about.php">About</a>
                </div>
            </nav>
            <h1>Join Our Community!</h1>
        </header>

        <div class="login-form-container">
            <div class="login-image">
                <h2>🌟 Start Your Journey<br>Connect with Amazing People</h2>
            </div>
            <div class="login-form">
                <form action="signup.php" method="post">
                    <h2 style="color: #1877f2; text-align: center; margin-bottom: 30px;">✨ Create Your Account</h2>

                    <?php
                    if (isset($error[0]))
                        echo '<div class="message error-message">' . $error[0] . '</div>';
                    if (isset($error[5]))
                        echo '<div class="message error-message">' . $error[5] . '</div>';
                    if (isset($error[7]))
                        echo '<div class="message error-message">' . $error[7] . '</div>';
                    ?>

                    <div class="form-group">
                        <label for="email">📧 Email Address</label>
                        <input type="text" id="email" name="email" value="<?php echo $register_mail; ?>"
                            placeholder="Enter your email address" required />
                    </div>
                    <?php
                    if (isset($error[1]))
                        echo '<div class="message error-message">' . $error[1] . '</div>';
                    if (isset($error[6]))
                        echo '<div class="message error-message">' . $error[6] . '</div>';
                    ?>

                    <div class="form-group">
                        <label for="name">👤 Profile Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $register_name; ?>"
                            placeholder="Enter your profile name" required />
                    </div>
                    <?php if (isset($error[2]))
                        echo '<div class="message error-message">' . $error[2] . '</div>'; ?>

                    <div class="form-group">
                        <label for="password">🔒 Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            required />
                    </div>
                    <?php
                    if (isset($error[3]))
                        echo '<div class="message error-message">' . $error[3] . '</div>';
                    if (isset($error[4]))
                        echo '<div class="message error-message">' . $error[4] . '</div>';
                    ?>

                    <div class="form-group">
                        <label for="re_password">🔐 Confirm Password</label>
                        <input type="password" id="re_password" name="re_password" placeholder="Confirm your password"
                            required />
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-full">🚀 Create Account</button>

                    <div style="text-align: center; margin-top: 25px;">
                        <p style="color: #65676b;">
                            Already have an account?
                            <a href="login.php" style="color: #1877f2; text-decoration: none; font-weight: 600;">Login
                                Here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>