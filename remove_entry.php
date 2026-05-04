<?php
// remove_entry.php
require 'db_connect.php';

$user_id = 1; // Our dummy user

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['series_id'])) {
    $series_id = (int)$_POST['series_id'];

    try {
        // Delete only the user's link to this manhwa
        $stmt = $pdo->prepare("DELETE FROM user_library WHERE user_id = ? AND series_id = ?");
        $stmt->execute([$user_id, $series_id]);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Redirect back to the previous list
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>