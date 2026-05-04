<?php
// update_library.php
require 'db_connect.php';

$user_id = 1; // Dummy user

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['series_id'])) {
    $series_id = (int)$_POST['series_id'];
    $action = $_POST['action'];

    try {
        if ($action == 'add') {
            // NEW: Grab the target list from the dropdown (default to Reading if missing)
            $status = $_POST['status'] ?? 'Reading';
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_library (user_id, series_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $series_id, $status]);
            
        } elseif ($action == 'update') {
            $current_chapter = (float)$_POST['current_chapter'];
            $status = $_POST['status']; 
            
            $rating = (!empty($_POST['rating'])) ? (int)$_POST['rating'] : null;
            
            $stmt = $pdo->prepare("UPDATE user_library SET current_chapter = ?, rating = ?, status = ? WHERE user_id = ? AND series_id = ?");
            $stmt->execute([$current_chapter, $rating, $status, $user_id, $series_id]);
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>