<?php
session_set_cookie_params(1800);
session_start();
if ($_SESSION['status'] !== "logged") {
    header("Location: login.php");
}
$email = $_SESSION['email'];
$name = $_SESSION['name'];
require("settings.php");
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
    <meta name="description" content="Add Friends - Discover and connect with new people" />
    <meta name="keywords" content="add friends, discover, connect, social network" />
    <link rel="stylesheet" href="styles.css" />
    <title>Add Friends - My Friend System</title>
</head>

<body class="friendadd-main">
    <div class="container">
        <header>
            <nav>
                <a class="nav-logo" href="index.php">👥 My Friend System</a>
                <div class="nav-links">
                    <a href="profile.php">Profile</a>
                    <a href="friendlist.php">Friend List</a>
                    <a href="friendadd.php" class="active">Add Friends</a>
                    <a href="about.php">About</a>
                    <a href="logout.php">Logout</a>
                </div>
            </nav>
            <h1>Discover New Friends</h1>
            <h2 class="friendhead">👋 Welcome, <?php echo $name ?>!</h2>
            <h2 class="friendhead">🤝 You have <?php echo $num_of_friend ?> friends</h2>
        </header>

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
                <h2> People You May Know</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <?php
                    $conn = new mysqli($host, $user, $pswd);
                    @$conn->select_db($dbnm);
                    // Get total records first to validate page
                    $find_friend_count = "SELECT friend_id FROM $table WHERE friend_id NOT IN 
                     (SELECT friend_id1 FROM $table2 WHERE friend_id2 = $id UNION SELECT friend_id2 FROM $table2 WHERE friend_id1 = $id) 
                     AND friend_id != $id";
                    $count_result = $conn->query($find_friend_count);
                    $total_records = $count_result->num_rows;

                    $limit = 5;
                    $total_pages = ceil($total_records / $limit);

                    // Validate and sanitize page number
                    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    if ($page < 1) {
                        $page = 1;
                    } elseif ($page > $total_pages && $total_pages > 0) {
                        $page = $total_pages;
                    }

                    $offset = ($page - 1) * $limit;

                    $find_friend = "SELECT friend_id, profile_name FROM $table WHERE friend_id NOT IN 
                                (SELECT friend_id1 FROM $table2 WHERE friend_id2 = $id UNION SELECT friend_id2 FROM $table2 WHERE friend_id1 = $id) 
                                AND friend_id != $id LIMIT $limit OFFSET $offset";
                    $search_result = $conn->query($find_friend);

                    echo "<table>";
                    echo "<thead><tr><th>👤 Name</th><th>🤝 Mutual Friends</th><th>⚡ Action</th></tr></thead>";
                    echo "<tbody>";

                    if ($search_result->num_rows > 0) {
                        $row = $search_result->fetch_row();
                        $union_select = "SELECT friend_id1 FROM $table2 WHERE friend_id2 = $id
                                                UNION
                                                SELECT friend_id2 FROM $table2 WHERE friend_id1 = $id";

                        while ($row) {
                            echo "<tr>";
                            echo "<td><strong>{$row[1]}</strong></td>";

                            $mutual_friends_query = "SELECT friend_id, COUNT(*) AS mutual_friends_count
                                        FROM $table AS user_friends
                                        JOIN $table2 AS friend_links
                                            ON (user_friends.friend_id = friend_links.friend_id1 AND friend_links.friend_id2 = {$row[0]})
                                            OR (user_friends.friend_id = friend_links.friend_id2 AND friend_links.friend_id1 = {$row[0]})
                                        WHERE user_friends.friend_id != $id
                                            AND user_friends.friend_id IN ($union_select)";
                            $search_result_mutual_friends = $conn->query($mutual_friends_query);
                            $row_mutual_friends = $search_result_mutual_friends->fetch_assoc();
                            $mutual_friend_count = $row_mutual_friends['mutual_friends_count'];

                            echo "<td>👥 " . $mutual_friend_count . " mutual friends</td>";
                            echo "<td>";
                            echo "<form action=\"adddeletefriend.php\" method=\"post\" style=\"display: inline;\">";
                            echo "<input type=\"hidden\" name=\"friend_id\" value=\"" . $row[0] . "\"/>";
                            echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"addfriend\">➕ Add Friend</button></form>";
                            echo "</td>";
                            echo "</tr>";
                            $row = $search_result->fetch_row();
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='3'><div class='no-friends'><h3>🎉 All Connected!</h3><p>You've connected with everyone available!</p></div></td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";

                    $conn->close();
                    ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo ($page - 1); ?>">← Previous</a>
                        <?php endif; ?>

                        <span
                            style="padding: 12px 24px; background: rgba(248, 249, 250, 0.95); border-radius: 25px; font-weight: 600; backdrop-filter: blur(10px);">
                            📄 Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                        </span>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo ($page + 1); ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>