<?php
session_start();
if ($_SESSION['status'] !== "logged") {
    header("Location: login.php");
}
if (isset($_POST['addfriend']) || isset($_POST['unfriend'])) {
    $email = $_SESSION['email'];
    require("settings.php");
    $conn = new mysqli($host, $user, $pswd);
    @$conn->select_db($dbnm);

    // Get the record of the current user from table friends
    $sql = "SELECT friend_id FROM $table WHERE friend_email = '$email'";
    $result = $conn->query($sql);
    $row = $result->fetch_row();
    $id = $row[0];
    $friend_id = $_POST['friend_id'];

    if (isset($_POST['addfriend'])) {
        // Check if friendship already exists
        $check_friendship = "SELECT * FROM $table2 WHERE 
                            (friend_id1 = $id AND friend_id2 = $friend_id) OR 
                            (friend_id1 = $friend_id AND friend_id2 = $id)";
        $check_result = $conn->query($check_friendship);

        if ($check_result->num_rows > 0) {
            // Friendship already exists - do nothing
            $_SESSION['message'] = "<p class=\"remind-message\">You are already friends with this person</p>";
        } else {
            // Friendship doesn't exist - add new friendship
            $insert_sql = "INSERT INTO $table2 (friend_id1, friend_id2) VALUES ($id, $friend_id)";
            if ($conn->query($insert_sql)) {
                // Update friend count for both users
                $update_sql = "UPDATE $table SET num_of_friends = num_of_friends + 1 WHERE friend_id = $id OR friend_id = $friend_id";
                $conn->query($update_sql);
                $_SESSION['message'] = "<p class=\"success\">You have added friend successfully</p>";
            } else {
                $_SESSION['message'] = "<p class=\"error-message\">Error adding friend: " . $conn->error . "</p>";
            }
        }
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);

    } else {
        // Unfriend logic
        $delete_sql = "DELETE FROM $table2 WHERE (friend_id1 = $id AND friend_id2 = $friend_id) OR (friend_id1 = $friend_id AND friend_id2 = $id)";
        if ($conn->query($delete_sql)) {
            // Only update if rows were actually deleted
            if ($conn->affected_rows > 0) {
                $update_sql = "UPDATE $table SET num_of_friends = num_of_friends - 1 WHERE friend_id = $id OR friend_id = $friend_id";
                $conn->query($update_sql);
                $_SESSION['message'] = "<p class=\"success\">You have unfriended successfully</p>";
            } else {
                $_SESSION['message'] = "<p class=\"remind-message\">You were not friends with this person</p>";
            }
        } else {
            $_SESSION['message'] = "<p class=\"error-message\">Error removing friend: " . $conn->error . "</p>";
        }
        $conn->close();
        header("Location: friendlist.php");
    }
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>