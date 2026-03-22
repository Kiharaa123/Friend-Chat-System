<?php
session_start();
if ($_SESSION['status'] !== "logged") {
    header("Location: login.php");
}
$email = $_SESSION['email'];
$name = $_SESSION['name'];
require("settings.php");
require_once("function/update.php");
updatefriend($host, $user, $pswd, $dbnm, $table, $table2);
$conn = new mysqli($host, $user, $pswd);
@$conn->select_db($dbnm);
$sql = "SELECT * FROM $table WHERE friend_email = '$email'";
$result = $conn->query($sql);
$row = $result->fetch_row();
$id = $row[0];
$num_of_friend = $row[5];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Your Profile - Manage your account and view your information" />
    <meta name="keywords" content="profile, account, user information, settings" />
    <link rel="stylesheet" href="styles.css" />
    <title>Profile - My Friend System</title>
</head>

<body class="profile-main">
    <div class="container">
        <header>
            <nav>
                <a class="nav-logo" href="index.php">👥 My Friend System</a>
                <div class="nav-links">
                    <a href="profile.php" class="active">Profile</a>
                    <a href="friendlist.php">Friend List</a>
                    <a href="friendadd.php">Add Friends</a>
                    <a href="about.php">About</a>
                    <a href="logout.php">Logout</a>
                </div>
            </nav>
            <h1>Your Profile</h1>
            <h2 class="friendhead">👋 Hello, <?php echo $name ?>!</h2>
            <h2 class="friendhead">🤝 Total friends: <?php echo $num_of_friend ?></h2>
        </header>

        <div class="card">
            <div class="card-header">
                <h2>👤 Profile Information</h2>
            </div>
            <div class="card-body">
                <div class="profile-info">
                    <?php
                    $conn = new mysqli($host, $user, $pswd);
                    @$conn->select_db($dbnm);
                    $sql = "SELECT * FROM $table WHERE friend_email = '$email'";
                    $result = $conn->query($sql);
                    $row = $result->fetch_row();

                    echo "<p><strong>Friend ID:</strong> " . $row[0] . "</p>";
                    echo "<p><strong> Email:</strong> " . $row[1] . "</p>";
                    //echo "<p><strong>🔒 Password:</strong> " . str_repeat('*', strlen($row[2])) . "</p>";
                    echo "<p><strong>Profile Name:</strong> " . $row[3] . "</p>";
                    echo "<p><strong>Date Started:</strong> " . $row[4] . "</p>";
                    echo "<p><strong>Total Friends:</strong> " . $row[5] . "</p>";
                    $conn->close();
                    ?>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="deleteaccount.php" class="btn btn-danger"
                        onclick="return confirm('⚠️ Are you sure you want to delete your account? This action cannot be undone and you will lose all your connections!')">
                        🗑️ Delete Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>