<?php
// sync_updates.php

// 1. Bring in our database connection
require 'db_connect.php';

echo "Starting Manhwa background sync...\n";
echo "--------------------------------------\n";

// 2. Get all ongoing series from the database
$stmt = $pdo->query("SELECT id, title, scrape_url FROM series WHERE status = 'Ongoing'");
$seriesList = $stmt->fetchAll();

foreach ($seriesList as $series) {
    echo "Checking: " . $series['title'] . "...\n";

    // 3. Build the terminal command (just like the one you typed manually)
    // We point it to the manhwa_scraper folder inside our project
    $nodeCommand = 'node manhwa_scraper/scraper.js "' . $series['scrape_url'] . '"';

    // 4. Run the Node.js script and catch the JSON output using shell_exec()
    $output = shell_exec($nodeCommand);

    if ($output) {
        // Decode the JSON string back into an array PHP can understand
        $result = json_decode($output, true);

        if ($result && $result['success']) {
            $latestChapter = $result['data']['chapter'];
            echo "--> Scraped Chapter: " . $latestChapter . "\n";

            // 5. Attempt to save the new chapter to the database
            try {
                $insertStmt = $pdo->prepare("INSERT INTO chapters (series_id, chapter_number) VALUES (?, ?)");
                $insertStmt->execute([$series['id'], $latestChapter]);
                
                echo "--> SUCCESS: Saved Chapter " . $latestChapter . " to database!\n";
                
            } catch (PDOException $e) {
                // If it hits our UNIQUE constraint, it means we already have this chapter saved
                if ($e->getCode() == 23000) {
                    echo "--> Chapter " . $latestChapter . " is already up to date in the DB.\n";
                } else {
                    echo "--> Database Error: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "--> Scraper failed: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    } else {
         echo "--> Fatal Error: Could not trigger Node script.\n";
    }
    echo "--------------------------------------\n";
}

echo "Sync complete!\n";
?>