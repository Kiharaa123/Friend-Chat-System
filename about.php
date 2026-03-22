<?php
session_start();
if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="About My Friend System - Learn more about this social networking platform" />
    <meta name="keywords" content="about, friend system, social network, features" />
    <link rel="stylesheet" href="styles.css" />
    <title>About - My Friend System</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <a class="nav-logo" href="index.php">👥 My Friend System</a>
                <div class="nav-links">
                    <?php if ($_SESSION['status'] == "logged"): ?>
                        <!-- Navigation for logged in user -->
                        <a href="profile.php">Profile</a>
                        <a href="friendlist.php">Friend List</a>
                        <a href="friendadd.php">Add Friends</a>
                        <a href="about.php" class="active">About</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <!-- Navigation for guest user -->
                        <a href="index.php">Home</a>
                        <a href="login.php">Login</a>
                        <a href="signup.php">Register</a>
                        <a href="about.php" class="active">About</a>
                    <?php endif; ?>
                </div>
            </nav>
            <h1>About My Friend System</h1>
        </header>

        <div class="about-content">
            <h2 style="text-align: center; margin-bottom: 30px; color: #1877f2;">📋 Project Information & Development
                Details</h2>

            <h3 style="margin-bottom: 20px;">Navigation Links</h3>
            <ul>
                <li><a href="index.php">Home page</a> - index.php</li>
                <li><a href="friendlist.php">Friend List</a> - friendlist.php</li>
                <li><a href="friendadd.php">Add Friends</a> - friendadd.php</li>
            </ul>

            <ul>
                <li>What tasks I have not attempted or not completed?</li>
                <ul>
                    <li>I have done all the required tasks and also implement some features that are not included.</li>
                </ul>

                <li>What special features have I done or attempted in creating site that you should know about?</li>
                <ul>
                    <li>I have done the mutual friends part where the add page will show number of mutual friends</li>
                    <li>I have done the part that view the current user profile and can delete the account (which is
                        quite the same with the nowadays implemented features on the social media like Facebook).</li>
                    <li>I have added a feature that allows users to see who others have become friends with.</li>
                </ul>

                <li>Which parts did I have trouble with?</li>
                <ul>
                    <li>I encountered difficulties in designing the user interface, including some issues related to
                        CSS.
                    <li>I also faced challenges in writing database queries, as I was unsure how to optimize them.
                        Therefore, I conducted extensive online research to learn and improve.</li>
                </ul>

                <li>What would I like to do better next time?</li>
                <ul>
                    <li>I would like to focus on enhancing the security features of the website, particularly by
                        implementing secure session management and appropriate authentication mechanisms to safeguard
                        user data and strengthen system integrity.</li>
                </ul>

                <li> Additional features did I add to the assignment?</li>
                <ul>
                    <li>I have implemented a "Forgot Password" feature that sends a password reset email to users in
                        case they forget their credentials.</li>
                    <li>Users are also able to delete their own accounts if they wish, similar to features found on
                        platforms like Facebook.</li>
                </ul>
            </ul>
        </div>
    </div>
</body>

</html>