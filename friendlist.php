<?php
session_set_cookie_params(1800);
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
$sql = "SELECT friend_id, num_of_friends FROM $table WHERE friend_email = '$email'";
$result = $conn->query($sql);
$row = $result->fetch_row();
$id = $row[0];
$num_of_friend = $row[1];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Your Friend List - Manage and view your connections" />
    <meta name="keywords" content="friends, connections, social network, manage friends" />
    <link rel="stylesheet" href="styles.css" />
    <title>Friend List - My Friend System</title>
</head>

<body class="friendlist-main">
    <header>
        <nav>
            <a class="nav-logo" href="index.php">👥 My Friend System</a>
            <div class="nav-links">
                <a href="profile.php">Profile</a>
                <a href="friendlist.php" class="active">Friend List</a>
                <a href="friendadd.php">Add Friends</a>
                <a href="about.php">About</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        <h1>Your Friend Network</h1>
        <h2 class="friendhead">👋 Welcome back, <?php echo $name ?>!</h2>
        <h2 class="friendhead">🤝 Total friends: <?php echo $num_of_friend ?></h2>
    </header>

    <div class="container">
        <?php
        if (isset($_SESSION['message'])) {
            if (!empty($_SESSION['message'])) {
                echo '<div class="message success">' . $_SESSION['message'] . '</div>';
            }
            $_SESSION['message'] = "";
        }
        ?>

        <div class="card">
            <div class="card-header">
                <h2>Your Friends</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <?php
                    $conn = new mysqli($host, $user, $pswd);
                    @$conn->select_db($dbnm);

                    echo "<table>";
                    echo "<thead><tr><th>Name</th><th>Friends</th><th>Action</th></tr></thead>";
                    echo "<tbody>";

                    $find_friend = "SELECT * FROM $table2 WHERE friend_id1 = $id OR friend_id2 = $id GROUP BY friend_id1, friend_id2";
                    $search_result = $conn->query($find_friend);

                    // Replace the counting logic
                    if ($search_result->num_rows > 0) {
                        $friends_displayed = 0; // Counter for actually displayed friends
                    
                        while ($row = $search_result->fetch_row()) {
                            $get_friend = "SELECT friend_id, profile_name FROM $table WHERE NOT friend_email = '$email'";
                            $search_result_get = $conn->query($get_friend);

                            while ($row_get = $search_result_get->fetch_row()) {
                                if (($row_get[0] == $row[0] && $row[1] == $id) || ($row_get[0] == $row[1] && $row[0] == $id)) {
                                    $friends_displayed++; // Increment only when actually displaying
                    
                                    echo "<tr>";
                                    echo "<td><strong>{$row_get[1]}</strong></td>";
                                    echo "<td>";

                                    // ADD THIS MISSING QUERY FOR FRIEND'S FRIENDS
                                    $get_of_friend = "SELECT * FROM $table2 WHERE friend_id1 = {$row_get[0]} OR friend_id2 = {$row_get[0]}";
                                    $search_result_get_of_friend = $conn->query($get_of_friend);

                                    if ($search_result_get_of_friend->num_rows > 0) {
                                        $checkbox_id = "toggle-friends-" . $row_get[0];

                                        echo "<input type=\"checkbox\" id=\"$checkbox_id\" class=\"friends-toggle\">";
                                        echo "<label for=\"$checkbox_id\" class=\"btn btn-secondary view-friends-label\" style=\"font-size: 12px; padding: 4px 8px; margin-top: 8px; cursor: pointer;\"></label>";

                                        echo "<div class=\"friend-details friends-content\">";

                                        // Reset result pointer and display friends list
                                        $search_result_get_of_friend->data_seek(0);
                                        $displayed_count = 0;
                                        while ($row_get_friend = $search_result_get_of_friend->fetch_row()) {
                                            $friend_id = ($row_get_friend[0] == $row_get[0]) ? $row_get_friend[1] : $row_get_friend[0];

                                            // Get friend name regardless of ID
                                            $friend_name_query = "SELECT profile_name FROM $table WHERE friend_id = $friend_id";
                                            $friend_name_result = $conn->query($friend_name_query);

                                            if ($friend_name_result && $friend_name_result->num_rows > 0) {
                                                $friend_name_row = $friend_name_result->fetch_row();
                                                // Only exclude if it's the current logged-in user
                                                if ($friend_id != $id) {
                                                    echo "<p>👤 {$friend_name_row[0]}</p>";
                                                    $displayed_count++;
                                                }
                                            } else {
                                                // Handle case where friend data doesn't exist
                                                echo "<p style='color: #999;'>👤 [Deleted User - ID: $friend_id]</p>";
                                                $displayed_count++;
                                            }
                                        }



                                        echo "</div>";
                                    } else {
                                        echo "<span style='color: #999; font-style: italic;'>No friends to display</span>";
                                    }

                                    echo "</td>";
                                    echo "<td>";
                                    echo "<form action=\"adddeletefriend.php\" method=\"post\" style=\"display: inline;\">";
                                    echo "<input type=\"hidden\" name=\"friend_id\" value=\"" . $row_get[0] . "\"/>";
                                    echo "<button type=\"submit\" class=\"btn btn-danger\" name=\"unfriend\">❌ Unfriend</button></form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='3'><div class='no-friends'><h3>🔍 No friends yet</h3><p><a href=\"friendadd.php\" class=\"btn btn-primary\">➕ Add Friends</a></p></div></td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>