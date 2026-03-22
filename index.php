<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_set_cookie_params(1800);
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
    <meta name="description" content="My Friend System - Connect and manage your friendships online" />
    <meta name="keywords" content="friends, social network, connections, PHP, web application" />
    <link rel="stylesheet" href="styles.css" />
    <title>My Friend System - Home</title>
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
                        <a href="about.php">About</a>
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
            <h1>
                <?php if ($_SESSION['status'] == "logged"): ?>
                    Welcome back, <?php echo $_SESSION['name']; ?>!
                <?php else: ?>
                    Welcome to My Friend System
                <?php endif; ?>
            </h1>
        </header>

        <!-- Hero Section -->
        <div class="hero-section">
            <?php if ($_SESSION['status'] == "logged"): ?>
                <h2>👋 Welcome back, <?php echo $_SESSION['name']; ?>!</h2>
                <p>Ready to explore your network and discover new connections?</p>
                <div class="cta-buttons" style="margin-top: 20px;">
                    <a href="friendlist.php" class="btn btn-primary">View Friend List</a>
                    <a href="friendadd.php" class="btn btn-success">Add New Friends</a>
                </div>
            <?php else: ?>
                <h2>🌟 Connect, Share, and Build Lasting Friendships</h2>
                <p>My Friend System is a comprehensive social platform designed to help you manage and expand your social
                    network with ease.</p>
                <p>Discover new connections, maintain existing friendships, and explore mutual connections in a secure and
                    user-friendly environment.</p>
            <?php endif; ?>
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <div class="feature-card">
                <h3>🔍 Smart Friend Discovery</h3>
                <p>Find new friends through our intelligent recommendation system that shows mutual connections and
                    suggests people you might know.</p>
            </div>
            <div class="feature-card">
                <h3>👥 Mutual Friends</h3>
                <p>See how you're connected to others through mutual friends, making it easier to build trust and expand
                    your network naturally.</p>
            </div>
            <div class="feature-card">
                <h3>📊 Friend Management</h3>
                <p>Easily manage your friend list, view friend details, and explore your friends' connections with our
                    intuitive interface.</p>
            </div>
            <div class="feature-card">
                <h3>🔒 Secure & Private</h3>
                <p>Your privacy matters. All connections are secure, and you have full control over your profile and
                    friend interactions.</p>
            </div>
            <div class="feature-card">
                <h3>📱 Responsive Design</h3>
                <p>Access your friend network from any device. Our responsive design ensures a seamless experience on
                    desktop, tablet, and mobile.</p>
            </div>
            <div class="feature-card">
                <h3>⚡ Real-time Updates</h3>
                <p>Stay updated with real-time notifications about friend requests, new connections, and network
                    changes.</p>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta-section">
            <h2 style="color: white; margin-bottom: 15px;">Ready to Start Building Your Network?</h2>
            <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 20px;">Join thousands of users who are already
                connecting and building meaningful relationships.</p>
            <div class="cta-buttons">
                <a href="signup.php" class="btn btn-primary">Get Started - It's Free!</a>
                <a href="login.php" class="btn btn-success">Already a Member? Login</a>
            </div>
        </div>

        <div class="index-container">
            <div class="index-info">
                <h2 style="color: #1877f2; margin-bottom: 20px;"> Student Information</h2>
                <p><strong>Name:</strong> Pham Quang Khai</p>
                <p><strong>Student ID:</strong> 104176358</p>
                <p><strong>Email:</strong> 104176358@student.swin.edu.au</p>
                <p>
                    <em>I declare that this assignment is my individual work.
                        I have not worked collaboratively nor have I copied
                        from any other student's work or from any other source.</em>
                </p>
            </div>

            <div class="index-message">
                <h2 style="color: #1877f2; margin-bottom: 20px;">System Status</h2>
                <?php
                require_once("settings.php");
                $conn = new mysqli($host, $user, $pswd);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $system_message = "";
                @$conn->select_db($dbnm);

                $query = "CREATE TABLE IF NOT EXISTS $table (
                        friend_id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        friend_email VARCHAR(50) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        profile_name VARCHAR(30) NOT NULL,
                        date_started DATE NOT NULL,
                        num_of_friends INT(6) UNSIGNED
                        )";
                if ($conn->query($query) === true) {
                    $system_message .= "<p class=\"success\">Table <strong>$table</strong> created successfully</p>";
                    $select_all_tb1 = "SELECT * FROM $table";
                    $result1 = $conn->query($select_all_tb1);
                    if ($result1->num_rows > 0) {
                        $system_message .= "<p class=\"success\"> <strong>$table</strong> already has data</p>";
                    } else {
                        $populate_tb1 =
                            "INSERT INTO $table (friend_email, password, profile_name, date_started, num_of_friends)
                    VALUES ('emilydavis@gmail.com', 'emily123', 'Emily Davis', '2025-07-02', 4),
                            ('sarahmiller@gmail.com', 'sarah123', 'Sarah Miller', '2025-07-02', 6),
                            ('jessicawilson@gmail.com', 'jessica123', 'Jessica Wilson', '2025-07-02', 8),
                            ('ashleybrown@gmail.com', 'ashley123', 'Ashley Brown', '2025-07-02', 3),
                            ('amandajones@gmail.com', 'amanda123', 'Amanda Jones', '2025-07-02', 7),
                            ('melissagarcia@gmail.com', 'melissa123', 'Melissa Garcia', '2025-07-02', 5),
                            ('stephanielee@gmail.com', 'stephanie1', 'Stephanie Lee', '2025-07-02', 9),
                            ('laurawilliams@gmail.com', 'laura123', 'Laura Williams', '2025-07-02', 2),
                            ('heathersmith@gmail.com', 'heather123', 'Heather Smith', '2025-07-02', 6),
                            ('rachelmartin@gmail.com', 'rachel123', 'Rachel Martin', '2025-07-02', 4)";
                        if ($conn->query($populate_tb1) === true) {
                            $system_message .= "<p class=\"success\">📝 Table <strong>$table</strong> populated successfully</p>";
                        } else {
                            $system_message .= "Error populating table: " . $conn->error;
                        }
                    }
                } else {
                    $system_message .= "Error creating table: " . $conn->error;
                }

                $query_tb = "CREATE TABLE IF NOT EXISTS $table2 (
                            friend_id1 INT(6) NOT NULL,
                            friend_id2 INT(6) NOT NULL
                            CHECK (friend_id1 <> friend_id2)
                            )";
                if ($conn->query($query_tb) !== false) {
                    $system_message .= "<p class=\"success\">Table <strong>$table2</strong> created successfully</p>";
                    $select_all_tb2 = "SELECT * FROM $table2";
                    $result2 = $conn->query($select_all_tb2);
                    if ($result2->num_rows > 0) {
                        $system_message .= "<p class=\"success\"><strong>$table2</strong> already has data</p>";
                    } else {
                        $populate_tb2 = "INSERT INTO $table2 (friend_id1, friend_id2)
                                VALUES  (1, 2), (1, 3), (1, 4), (1, 5),
                                       (2, 3), (2, 4), (2, 5), (2, 6), (2, 7),
                                      (3, 4), (3, 5), (3, 6), (3, 7), (3, 8), (3, 9), (3, 10),
                                       (4, 6), (4, 7),
                                        (5, 6), (5, 7), (5, 8), (5, 9), (5, 10),
                                        (6, 7), (6, 8), (6, 9), (6, 10),
                                       (7, 8), (7, 9), (7, 10), (7, 1),
                                        (8, 9), (8, 10),
                                        (9, 10), (9, 1),
                                        (10, 1), (10, 2)";


                        if ($conn->query($populate_tb2) !== false) {
                            $system_message .= "<p class=\"success\">📝 Table <strong>$table2</strong> populated successfully</p>";
                        } else {
                            $system_message .= "Error populating table: " . $conn->error;
                        }
                    }
                } else {
                    $system_message .= "Error creating table: " . $conn->error;
                }
                $conn->close();

                echo $system_message;

                require_once("function/update.php");
                updatefriend($host, $user, $pswd, $dbnm, $table, $table2);
                ?>
            </div>
        </div>
    </div>
</body>

</html>