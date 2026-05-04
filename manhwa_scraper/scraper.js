// scraper.js
const puppeteer = require('puppeteer');

// This catches the URL we will eventually pass from our PHP application
const targetUrl = process.argv[2]; 

if (!targetUrl) {
    console.error(JSON.stringify({ success: false, error: "No URL provided" }));
    process.exit(1);
}

(async () => {
    try {
        const browser = await puppeteer.launch({ headless: true });
        const page = await browser.newPage();

        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');

        // Go to the URL
        await page.goto(targetUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });

        // --- THE FIX ---
        // Force the browser to wait until the specific list item is drawn on the screen (Max wait: 10 seconds)
        await page.waitForSelector('#_listUl .subj span', { timeout: 10000 });

        // Search the page's HTML for the latest chapter
        const chapterData = await page.evaluate(() => {
            
            // Note the updated desktop selector!
            const chapterElement = document.querySelector('#_listUl .subj span'); 
            
            if (chapterElement) {
                let rawText = chapterElement.innerText;
                let chapterNumber = rawText.replace(/[^0-9.]/g, ''); 
                return { chapter: parseFloat(chapterNumber) };
            }
            return null;
        });

        await browser.close();

        if (chapterData) {
            console.log(JSON.stringify({ success: true, data: chapterData }));
        } else {
            console.log(JSON.stringify({ success: false, error: "Could not find chapter element on page" }));
        }

    } catch (error) {
        console.log(JSON.stringify({ success: false, error: error.message }));
    }
})();