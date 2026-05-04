# Manhwa Tracker

A sleek, Spotify-inspired web dashboard for tracking, managing, and importing your reading progress for Webtoons, Manhwa, and Manga. 

## Overview

Manhwa Tracker is a full-stack application designed to replace messy spreadsheets. It features a dark-themed, responsive interface that allows users to organize their reading lists, rate series, and track chapter progress. It includes a custom Node.js web scraper that automatically fetches titles, cover art, and synopses from legal reading platforms.

## Key Features

*   **Spotify-Style User Interface:** A dark-mode aesthetic featuring a persistent side navigation bar, custom SVG gradient placeholders, and a real-time search filtering system.
*   **Universal Web Scraper:** Powered by Node.js and Puppeteer, the importer extracts Open Graph (OG) metadata and cleans branding tags from URLs (supports Webtoon, Tapas, and Tappytoon).
*   **Dynamic Library Management:** Organize series into custom lists (Currently Reading, Plan to Read, Favorites, Dropped) with a single click.
*   **Persistent Customization:** Users can click the edit icon on any playlist to assign a custom cover image URL, which is saved locally via browser LocalStorage.
*   **Quick Progress Updates:** Update chapter progress and ratings directly from the main dashboard without navigating to separate pages.

## Snapshots 
<img width="1877" height="922" alt="image" src="https://github.com/user-attachments/assets/61828794-e117-4c93-8ad9-b0f27b4a5572" />
<img width="1872" height="920" alt="image" src="https://github.com/user-attachments/assets/b3b65e20-278b-46d8-8ef6-6485ad4620b4" />



## Tech Stack

*   **Frontend:** HTML5, CSS3, TypeScript (DOM manipulation, LocalStorage, event handling)
*   **Backend:** PHP 8+
*   **Database:** MySQL (PDO wrapper for secure queries)
*   **Scraping Engine:** Node.js, Puppeteer

## Prerequisites

To run this project locally, you will need:
*   A local web server environment (e.g., XAMPP, MAMP, or Laragon)
*   PHP 8.0 or higher
*   MySQL/MariaDB
*   Node.js and npm (for the scraper)
*   TypeScript compiler globally installed (`npm install -g typescript`)

## Installation and Setup

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/YOUR-USERNAME/manhwa-tracker.git](https://github.com/YOUR-USERNAME/manhwa-tracker.git)
    cd manhwa-tracker
    ```

2.  **Database Configuration:**
    *   Open your MySQL manager (e.g., phpMyAdmin).
    *   Create a new database named `manhwa_tracker`.
    *   Import your SQL schema to create the `series`, `chapters`, `users`, and `user_library` tables.
    *   Ensure the `status` column in `user_library` is set to `VARCHAR(50)` (not ENUM) to support custom lists, and the `rating` column allows `DEFAULT NULL`.

3.  **Backend Configuration:**
    *   Locate or create a `db_connect.php` file in the root directory.
    *   Update it with your local MySQL credentials:
    ```php
    <?php
    $host = 'localhost';
    $db   = 'manhwa_tracker';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
    ?>
    ```

4.  **Install Node Dependencies:**
    Navigate to the directory containing your scraper (e.g., `manhwa_scraper`) and install Puppeteer:
    ```bash
    npm install puppeteer
    ```

5.  **Compile TypeScript:**
    Compile the `app.ts` file to generate the functional JavaScript file used by the browser:
    
```bash
    tsc app.ts
    ```

## Usage Instructions

*   **Importing a Series:** Paste a valid URL from Webtoon, Tapas, or Tappytoon into the import bar on the bottom left and click "Import Series". The system will scrape the metadata and add it to the Public Catalog.
*   **Adding to Library:** In the Public Catalog, use the dropdown next to the "Add" button to send a series directly to a specific list (e.g., Favorites).
*   **Customizing Playlist Covers:** Hover over any list in the sidebar, click the pencil icon, and paste an image URL. Leave the prompt blank to reset to the default SVG gradient.
*   **Searching:** Use the top search bar to instantly filter the active view by series title.

## License

This project is licensed under the MIT License.
