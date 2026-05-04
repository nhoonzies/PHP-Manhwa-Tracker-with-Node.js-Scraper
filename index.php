<?php
// index.php
require 'db_connect.php';
$user_id = 1; 

$current_list = $_GET['list'] ?? 'reading'; 

if ($current_list == 'all') {
    $header_title = "Public Catalog";
    $stmt = $pdo->prepare("
        SELECT s.id, s.title, s.cover_image_url, s.description, s.status as series_status, 
               (SELECT MAX(chapter_number) FROM chapters WHERE series_id = s.id) as latest_chapter,
               ul.current_chapter, ul.rating, ul.status as user_status
        FROM series s
        LEFT JOIN user_library ul ON s.id = ul.series_id AND ul.user_id = ?
    ");
    $stmt->execute([$user_id]);

} else {
    $status_map = [
        'reading' => 'Reading',
        'plan_to_read' => 'Plan to Read',
        'favorites' => 'Favorites',
        'dropped' => 'Dropped'
    ];
    $db_status = $status_map[$current_list] ?? 'Reading';
    $header_title = $db_status; 
    
    $stmt = $pdo->prepare("
        SELECT s.id, s.title, s.cover_image_url, s.description, s.status as series_status, 
               (SELECT MAX(chapter_number) FROM chapters WHERE series_id = s.id) as latest_chapter,
               ul.current_chapter, ul.rating, ul.status as user_status
        FROM series s
        INNER JOIN user_library ul ON s.id = ul.series_id 
        WHERE ul.user_id = ? AND ul.status = ?
    ");
    $stmt->execute([$user_id, $db_status]);
}

$catalog = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manhwa Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #000000;
            --bg-panel: #121212;
            --bg-hover: #2a2a2a;
            --maroon: #720000; 
            --maroon-light: #a30000;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-base); color: var(--text-main); margin: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden; padding: 8px; box-sizing: border-box; gap: 8px; }

        /* NEW: TOP BAR STYLES */
        .top-bar { display: flex; align-items: center; justify-content: space-between; height: 48px; flex-shrink: 0; padding: 0 8px; }
        .top-bar-left { display: flex; align-items: center; gap: 16px; width: 300px;} 
        .app-logo { width: 36px; height: 36px; text-decoration: none; transition: transform 0.2s; }
        .app-logo:hover { transform: scale(1.05); }
        .home-btn { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: var(--bg-panel); border-radius: 50%; text-decoration: none; color: var(--text-sub); transition: all 0.2s; }
        .home-btn:hover { transform: scale(1.05); color: white; }

        .top-bar-center { display: flex; justify-content: center; flex-grow: 1; }
        .search-container { display: flex; align-items: center; background-color: var(--bg-panel); border-radius: 24px; padding: 0 16px; width: 450px; height: 48px; border: 1px solid transparent; transition: all 0.2s;}
        .search-container:hover { background-color: #2a2a2a; }
        .search-container:focus-within { background-color: #2a2a2a; border-color: white; }
        .search-container input { background: transparent; border: none; color: white; width: 100%; font-size: 1rem; margin-left: 10px; outline: none; }
        .search-container input::placeholder { color: var(--text-sub); font-weight: bold;}

        .top-bar-right { width: 300px; } /* Spacer to keep search perfectly centered */

        /* NEW: APP BODY WRAPPER */
        .app-body { display: flex; flex-grow: 1; gap: 8px; overflow: hidden; height: calc(100vh - 64px); }

        /* PANELS */
        .sidebar { background-color: var(--bg-panel); border-radius: 8px; display: flex; flex-direction: column; overflow-y: auto;}
        .left-panel { width: 300px; padding: 20px; gap: 20px; flex-shrink: 0;}
        .main-view { flex-grow: 1; background-color: var(--bg-panel); border-radius: 8px; overflow-y: auto; position: relative; }
        
        a.nav-link { color: var(--text-sub); text-decoration: none; display: flex; align-items: center; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; padding: 8px; border-radius: 6px; }
        a.nav-link:hover { color: var(--text-main); background-color: var(--bg-hover); }
        .nav-link.active { color: white; background-color: var(--bg-hover); }
        
        .list-cover { width: 48px; height: 48px; border-radius: 4px; object-fit: cover; background: #2a2a2a; flex-shrink: 0; }
        .list-info { display: flex; flex-direction: column; margin-left: 12px; flex-grow: 1; }
        .list-title { font-size: 0.95rem; font-weight: bold; }
        .list-subtext { font-size: 0.75rem; color: var(--text-sub); font-weight: normal; margin-top: 4px; }
        
        .btn-edit-list { background: transparent; border: none; color: var(--text-sub); cursor: pointer; display: none; font-size: 1rem; padding: 5px; }
        .nav-link:hover .btn-edit-list { display: block; }
        .btn-edit-list:hover { color: white; }

        .playlist-section { margin-top: 10px; border-top: 1px solid #2a2a2a; padding-top: 15px;}
        
        .import-box { background: var(--bg-hover); padding: 15px; border-radius: 8px; margin-top: auto;}
        .import-box input { width: 100%; padding: 10px; background: #333; border: none; border-radius: 4px; color: white; margin-bottom: 10px; box-sizing: border-box;}
        .btn-import { width: 100%; background: var(--text-main); color: var(--bg-base); padding: 10px; border: none; border-radius: 20px; font-weight: bold; cursor: pointer; }

        .hero { background: linear-gradient(to bottom, var(--maroon), var(--bg-panel)); padding: 60px 30px 30px 30px; }
        .hero h1 { font-size: 3.5rem; margin: 0; letter-spacing: -1px; }

        .list-container { padding: 0 30px 30px 30px; }
        .row { display: grid; grid-template-columns: 3fr 1fr 2.2fr; padding: 8px 16px; border-radius: 4px; align-items: center; transition: background 0.2s; }
        .row:hover { background-color: var(--bg-hover); }
        .row-header { color: var(--text-sub); border-bottom: 1px solid #2a2a2a; padding-bottom: 8px; margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;}
        
        .title-wrapper { display: flex; align-items: center; cursor: pointer; }
        .cover-art { width: 40px; height: 40px; border-radius: 4px; object-fit: cover; margin-right: 15px; background: #333; }
        .title { font-weight: bold; margin: 0; font-size: 1rem;}
        
        .controls { display: flex; gap: 8px; align-items: center; justify-content: flex-end;}
        .controls input { width: 45px; padding: 6px; background: #333; border: none; border-radius: 4px; color: white; text-align: center;}
        .controls select { padding: 6px; background: #333; border: none; border-radius: 4px; color: white; cursor: pointer;}
        .btn-save { background: var(--maroon); color: white; border: none; padding: 6px 15px; border-radius: 20px; font-weight: bold; cursor: pointer; }
        
        .btn-delete { background: transparent; border: none; color: #555; cursor: pointer; font-size: 1.1rem; padding: 5px; transition: color 0.2s; }
        .btn-delete:hover { color: var(--maroon-light); }

        .right-panel { width: 0; padding: 0; opacity: 0; transition: all 0.3s ease; flex-shrink: 0; overflow: hidden; pointer-events: none;}
        .right-panel.active { width: 320px; padding: 20px; opacity: 1; pointer-events: auto;}
        .rp-cover { width: 100%; height: 300px; border-radius: 8px; margin-bottom: 15px; object-fit: cover; background: #2a2a2a; display: flex; align-items: center; justify-content: center; color: #555; }
        .rp-title { font-size: 1.5rem; font-weight: bold; margin: 0 0 5px 0; }
        .rp-status { color: var(--text-sub); font-size: 0.9rem; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .rp-desc { font-size: 0.9rem; line-height: 1.5; color: var(--text-sub); }
        .close-btn { background: transparent; border: none; color: var(--text-sub); font-size: 1.2rem; cursor: pointer; align-self: flex-end; margin-bottom: 10px; transition: color 0.2s;}
        .close-btn:hover { color: white; }
    </style>
</head>
<body>

<!-- NEW: TOP NAVIGATION BAR -->
<header class="top-bar">
    <div class="top-bar-left">
        <!-- Logo Placeholder (SVG Books) -->
        <a href="?list=all" class="app-logo" title="Home">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='36' height='36'%3E%3Cdefs%3E%3ClinearGradient id='logoGrad' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23ff4b2b'/%3E%3Cstop offset='100%25' stop-color='%23ff416c'/%3E%3C/linearGradient%3E%3C/defs%3E%3Ccircle cx='12' cy='12' r='12' fill='url(%23logoGrad)'/%3E%3Cpath d='M6 16c0-1.1.9-2 2-2h8c1.1 0 2 .9 2 2v2H6v-2zm2-4h8v2H8v-2zm0-4h8v2H8V8z' fill='white'/%3E%3C/svg%3E" alt="Logo">
        </a>
        
        <!-- Home Button -->
        <a href="?list=all" class="home-btn" title="Home">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12.5 3.247a1 1 0 0 0-1 0L4 7.577V20h4.5v-6a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v6H20V7.577l-7.5-4.33z"></path></svg>
        </a>
    </div>

    <div class="top-bar-center">
        <!-- Search Bar -->
        <div class="search-container">
            <svg viewBox="0 0 24 24" fill="var(--text-sub)" width="20" height="20"><path d="M10.533 1.27893C5.35215 1.27893 1.12598 5.41887 1.12598 10.5579C1.12598 15.697 5.35215 19.8369 10.533 19.8369C12.7655 19.8369 14.8194 19.0671 16.4402 17.7794L20.7929 22.132C21.1834 22.5226 21.8166 22.5226 22.2071 22.132C22.5976 21.7415 22.5976 21.1083 22.2071 20.7178L17.8634 16.3741C19.1835 14.7415 19.9399 12.7231 19.9399 10.5579C19.9399 5.41887 15.7138 1.27893 10.533 1.27893ZM3.12598 10.5579C3.12598 6.53225 6.4259 3.27893 10.533 3.27893C14.6401 3.27893 17.9399 6.53225 17.9399 10.5579C17.9399 14.5836 14.6401 17.8369 10.533 17.8369C6.4259 17.8369 3.12598 14.5836 3.12598 10.5579Z"></path></svg>
            <input type="text" id="searchInput" placeholder="What do you want to read?">
        </div>
    </div>
    
    <div class="top-bar-right"></div>
</header>

<!-- NEW: APP BODY WRAPPER -->
<div class="app-body">
    <aside class="sidebar left-panel">
        <div>
            <a href="?list=all" class="nav-link <?= ($current_list == 'all') ? 'active' : '' ?>" style="margin-bottom: 20px;">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='pub' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%2300c6ff'/%3E%3Cstop offset='100%25' stop-color='%230072ff'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23pub)'/%3E%3Ccircle cx='24' cy='24' r='10' fill='none' stroke='white' stroke-width='2'/%3E%3Cellipse cx='24' cy='24' rx='4' ry='10' fill='none' stroke='white' stroke-width='2'/%3E%3Cline x1='14' y1='24' x2='34' y2='24' stroke='white' stroke-width='2'/%3E%3C/svg%3E" class="list-cover" alt="Catalog">
                <div class="list-info">
                    <span class="list-title" style="font-size: 1.15rem;">Public Catalog</span>
                    <span class="list-subtext">Discover • Master Database</span>
                </div>
            </a>
            
            <span style="color: var(--text-sub); font-size: 0.75rem; font-weight: bold; letter-spacing: 1px; margin-bottom: 10px; display: block; padding-left: 8px;">YOUR LIBRARY</span>
            
            <div class="playlist-section" style="border-top: none; padding-top: 0;">
                <a href="?list=reading" class="nav-link <?= ($current_list == 'reading') ? 'active' : '' ?>" data-list="reading">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='r' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23ff4b2b'/%3E%3Cstop offset='100%25' stop-color='%23ff416c'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23r)'/%3E%3Cpath d='M18 14v20l16-10z' fill='white'/%3E%3C/svg%3E" 
                         data-default-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='r' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23ff4b2b'/%3E%3Cstop offset='100%25' stop-color='%23ff416c'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23r)'/%3E%3Cpath d='M18 14v20l16-10z' fill='white'/%3E%3C/svg%3E" 
                         class="list-cover" alt="Cover">
                    <div class="list-info">
                        <span class="list-title">Currently Reading</span>
                        <span class="list-subtext">Playlist • Library</span>
                    </div>
                    <button class="btn-edit-list" title="Change Cover">✎</button>
                </a>

                <a href="?list=plan_to_read" class="nav-link <?= ($current_list == 'plan_to_read') ? 'active' : '' ?>" data-list="plan_to_read">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='p' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%2311998e'/%3E%3Cstop offset='100%25' stop-color='%2338ef7d'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23p)'/%3E%3Ccircle cx='24' cy='24' r='10' fill='none' stroke='white' stroke-width='2'/%3E%3Cpath d='M24 18v6l4 4' fill='none' stroke='white' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E" 
                         data-default-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='p' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%2311998e'/%3E%3Cstop offset='100%25' stop-color='%2338ef7d'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23p)'/%3E%3Ccircle cx='24' cy='24' r='10' fill='none' stroke='white' stroke-width='2'/%3E%3Cpath d='M24 18v6l4 4' fill='none' stroke='white' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E" 
                         class="list-cover" alt="Cover">
                    <div class="list-info">
                        <span class="list-title">Plan to Read</span>
                        <span class="list-subtext">Playlist • Library</span>
                    </div>
                    <button class="btn-edit-list" title="Change Cover">✎</button>
                </a>

                <a href="?list=favorites" class="nav-link <?= ($current_list == 'favorites') ? 'active' : '' ?>" data-list="favorites">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='f' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23450af5'/%3E%3Cstop offset='100%25' stop-color='%23c4efd9'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23f)'/%3E%3Cpath d='M24 33.5l-2.1-1.9c-7.5-6.8-12.4-11.2-12.4-16.6 0-4.4 3.5-7.9 7.9-7.9 2.5 0 4.9 1.2 6.6 3.1 1.7-1.9 4.1-3.1 6.6-3.1 4.4 0 7.9 3.5 7.9 7.9 0 5.4-4.9 9.8-12.4 16.6L24 33.5z' fill='white'/%3E%3C/svg%3E" 
                         data-default-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='f' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23450af5'/%3E%3Cstop offset='100%25' stop-color='%23c4efd9'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23f)'/%3E%3Cpath d='M24 33.5l-2.1-1.9c-7.5-6.8-12.4-11.2-12.4-16.6 0-4.4 3.5-7.9 7.9-7.9 2.5 0 4.9 1.2 6.6 3.1 1.7-1.9 4.1-3.1 6.6-3.1 4.4 0 7.9 3.5 7.9 7.9 0 5.4-4.9 9.8-12.4 16.6L24 33.5z' fill='white'/%3E%3C/svg%3E" 
                         class="list-cover" alt="Cover">
                    <div class="list-info">
                        <span class="list-title">Favorites</span>
                        <span class="list-subtext">Playlist • Liked Series</span>
                    </div>
                    <button class="btn-edit-list" title="Change Cover">✎</button>
                </a>

                <a href="?list=dropped" class="nav-link <?= ($current_list == 'dropped') ? 'active' : '' ?>" data-list="dropped">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='d' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23434343'/%3E%3Cstop offset='100%25' stop-color='%23000000'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23d)'/%3E%3Cpath d='M18 18l12 12M30 18L18 30' stroke='white' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E" 
                         data-default-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Cdefs%3E%3ClinearGradient id='d' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23434343'/%3E%3Cstop offset='100%25' stop-color='%23000000'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='48' height='48' fill='url(%23d)'/%3E%3Cpath d='M18 18l12 12M30 18L18 30' stroke='white' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E" 
                         class="list-cover" alt="Cover">
                    <div class="list-info">
                        <span class="list-title">Dropped</span>
                        <span class="list-subtext">Playlist • Library</span>
                    </div>
                    <button class="btn-edit-list" title="Change Cover">✎</button>
                </a>
            </div>
        </div>
        <div class="import-box">
            <form id="importForm" action="add_series.php" method="POST">
                <input type="url" id="scrapeUrl" name="scrape_url" placeholder="Paste Webtoon link..." required>
                <button type="submit" class="btn-import" id="importBtn">Import Series</button>
            </form>
        </div>
    </aside>

    <main class="main-view">
        <header class="hero">
            <p style="margin:0; font-weight:bold; font-size:0.9rem;">PUBLIC TRACKER</p>
            <h1><?= htmlspecialchars($header_title) ?></h1>
        </header>

        <div class="list-container">
            <div class="row row-header">
                <div>Title</div>
                <div>Latest Release</div>
                <div style="text-align: right;">My Progress</div>
            </div>

            <?php foreach ($catalog as $series): ?>
                <div class="row">
                    <div class="title-wrapper" 
                         data-title="<?= htmlspecialchars($series['title']) ?>"
                         data-status="<?= htmlspecialchars($series['series_status']) ?>"
                         data-cover="<?= htmlspecialchars($series['cover_image_url'] ?? '') ?>"
                         data-desc="<?= htmlspecialchars($series['description'] ?? 'No description available.') ?>">
                        <img src="<?= $series['cover_image_url'] ?: 'https://via.placeholder.com/40x40/2a2a2a/555555?text=?' ?>" class="cover-art" alt="Cover">
                        <p class="title"><?= htmlspecialchars($series['title']) ?></p>
                    </div>
                    
                    <div>
                        <span style="color: var(--text-sub); font-size: 0.9rem;">Ep. <?= $series['latest_chapter'] ?? '?' ?></span>
                    </div>
                    
                    <div class="controls">
                        <?php if ($series['user_status'] === null): ?>
                            <form action="update_library.php" method="POST" style="display: flex; gap: 8px; align-items: center;">
                                <input type="hidden" name="series_id" value="<?= $series['id'] ?>">
                                <input type="hidden" name="action" value="add">
                                
                                <select name="status" style="padding: 6px; background: #333; border: none; border-radius: 4px; color: white; cursor: pointer;">
                                    <option value="Reading">Reading</option>
                                    <option value="Plan to Read">Plan to Read</option>
                                    <option value="Favorites">Favorites</option>
                                    <option value="Dropped">Dropped</option>
                                </select>
                                
                                <button type="submit" class="btn-save" style="background: transparent; border: 1px solid #777;">Add</button>
                            </form>
                        <?php else: ?>
                            <form action="update_library.php" method="POST" style="display: flex; gap: 8px; align-items: center;">
                                <input type="hidden" name="series_id" value="<?= $series['id'] ?>">
                                <input type="hidden" name="action" value="update">
                                <select name="status">
                                    <option value="Reading" <?= $series['user_status'] == 'Reading' ? 'selected' : '' ?>>Reading</option>
                                    <option value="Plan to Read" <?= $series['user_status'] == 'Plan to Read' ? 'selected' : '' ?>>Plan to Read</option>
                                    <option value="Favorites" <?= $series['user_status'] == 'Favorites' ? 'selected' : '' ?>>Favorites</option>
                                    <option value="Dropped" <?= $series['user_status'] == 'Dropped' ? 'selected' : '' ?>>Dropped</option>
                                </select>
                                <span style="color: var(--text-sub); font-size: 0.8rem;">CH:</span>
                                <input type="number" step="0.1" name="current_chapter" value="<?= htmlspecialchars($series['current_chapter']) ?>" min="0">
                                <span style="color: var(--text-sub); font-size: 0.8rem;">★</span>
                                <input type="number" name="rating" value="<?= htmlspecialchars($series['rating'] ?? '') ?>" min="1" max="10" placeholder="-">
                                <button type="submit" class="btn-save">Save</button>
                            </form>

                            <form action="remove_entry.php" method="POST" class="delete-form">
                                <input type="hidden" name="series_id" value="<?= $series['id'] ?>">
                                <button type="submit" class="btn-delete" title="Remove from Library">✕</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <aside class="sidebar right-panel" id="detailPanel">
        <button class="close-btn" id="closePanel">✖</button>
        <img src="https://via.placeholder.com/300x400/2a2a2a/555555?text=No+Cover" id="rp-cover" class="rp-cover" alt="Cover Art">
        <h2 class="rp-title" id="rp-title">Title</h2>
        <p class="rp-status" id="rp-status">Ongoing</p>
        <div style="background: var(--bg-hover); padding: 15px; border-radius: 8px;">
            <h3 style="margin-top: 0; font-size: 1rem;">Synopsis</h3>
            <p class="rp-desc" id="rp-desc">Description will be fetched here.</p>
        </div>
    </aside>
</div> <!-- END APP BODY WRAPPER -->

<script src="app.js"></script>
</body>
</html>