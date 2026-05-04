// importer.js
const puppeteer = require('puppeteer');

const targetUrl = process.argv[2]; 

if (!targetUrl) {
    console.error(JSON.stringify({ success: false, error: "No URL provided" }));
    process.exit(1);
}

(async () => {
    let browser;
    try {
        browser = await puppeteer.launch({ headless: true });
        const page = await browser.newPage();
        
        // Disguise the scraper to avoid basic anti-bot blocks
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');

        // Use networkidle2 so we wait for SPA sites (like Tappytoon) to finish loading their API requests
        await page.goto(targetUrl, { waitUntil: 'networkidle2', timeout: 30000 });

        // Extract Title, Cover Art, Synopsis, and Latest Chapter simultaneously
        const seriesData = await page.evaluate(() => {
            
            // 1. Universal Open Graph Extraction (Works on almost all sites)
            let title = document.querySelector('meta[property="og:title"]')?.content || document.querySelector('title')?.innerText || 'Unknown Title';
            let cover_url = document.querySelector('meta[property="og:image"]')?.content || '';
            let description = document.querySelector('meta[property="og:description"]')?.content || 'No description available.';

            // Clean up generic site branding from titles using Regex (Case Insensitive)
            title = title
                .replace(/\s*\|\s*WEBTOON/i, '')
                .replace(/\s*\|\s*Tapas/i, '')
                .replace(/\s*\|\s*Tappytoon/i, '')
                .replace(/\s*-\s*Tappytoon/i, '')
                .replace(/\s*-\s*Official Comic/i, '')
                .trim();

            // 2. Identify the Website
            const currentUrl = window.location.hostname;
            let chapterNumber = 0;

            // 3. The "Strategy" Logic: Apply custom rules based on the domain
            try {
                if (currentUrl.includes('webtoons.com')) {
                    // WEBTOON LOGIC
                    const chapterElement = document.querySelector('#_listUl .subj span'); 
                    if (chapterElement) chapterNumber = parseFloat(chapterElement.innerText.replace(/[^0-9.]/g, ''));
                    
                } else if (currentUrl.includes('tapas.io')) {
                    // TAPAS LOGIC 
                    const chapterElement = document.querySelector('.episode-list .js-episode .name') || document.querySelector('.episode-title');
                    if (chapterElement) chapterNumber = parseFloat(chapterElement.innerText.replace(/[^0-9.]/g, ''));
                    
                } else if (currentUrl.includes('tappytoon.com')) {
                    // TAPPYTOON LOGIC 
                    const chapterElement = document.querySelector('[class*="EpisodeRow"] [class*="Title"]');
                    if (chapterElement) chapterNumber = parseFloat(chapterElement.innerText.replace(/[^0-9.]/g, ''));
                    
                } else {
                    // FALLBACK: If it's a site we haven't mapped yet, leave chapter at 0
                    chapterNumber = 0; 
                }
            } catch (e) {
                // If chapter extraction fails, fail gracefully
                chapterNumber = 0;
            }

            return { title, cover_url, description, chapter: chapterNumber };
        });

        await browser.close();
        console.log(JSON.stringify({ success: true, data: seriesData }));

    } catch (error) {
        console.log(JSON.stringify({ success: false, error: error.message }));
        // Ensure browser closes even if we hit a timeout
        if (browser) {
            try { await browser.close(); } catch(e) {}
        }
    }
})();   