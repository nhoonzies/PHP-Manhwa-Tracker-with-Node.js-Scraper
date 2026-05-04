<?php
// add_series.php
require 'db_connect.php';

// Check if a URL was submitted via the form
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['scrape_url'])) {
    
    // Clean the input to prevent security issues
    $url = filter_var($_POST['scrape_url'], FILTER_SANITIZE_URL);
    
    // 1. Trigger the Node.js Importer
    $command = 'node manhwa_scraper/importer.js ' . escapeshellarg($url);
    $output = shell_exec($command);
    
    if ($output) {
        $result = json_decode($output, true);
        
        if ($result && $result['success']) {
            $title = $result['data']['title'];
            $cover_url = $result['data']['cover_url']; // NEW: Catch the image
            $description = $result['data']['description']; // NEW: Catch the synopsis
            $chapter = $result['data']['chapter'];
            
            try {
                // 2. Insert the new series into the master catalog (Updated query)
                $stmt = $pdo->prepare("INSERT INTO series (title, cover_image_url, description, scrape_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $cover_url, $description, $url]);
                
                // Get the ID of the series we just inserted
                $newSeriesId = $pdo->lastInsertId();
                
                // 3. Insert the latest chapter for this new series
                $chapterStmt = $pdo->prepare("INSERT INTO chapters (series_id, chapter_number) VALUES (?, ?)");
                $chapterStmt->execute([$newSeriesId, $chapter]);
                
                // Redirect back to the dashboard with a success message
                header("Location: index.php?status=success");
                exit();
                
            } catch (PDOException $e) {
                // If it fails (like if the series is already in the database)
                header("Location: index.php?status=error&msg=" . urlencode($e->getMessage()));
                exit();
            }
        }
    }
    // If the scraper failed entirely
    header("Location: index.php?status=scraper_failed");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>