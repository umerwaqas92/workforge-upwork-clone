import puppeteer from 'puppeteer';
import fs from 'fs';

async function run() {
    const chromePath = fs.existsSync('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome')
        ? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
        : undefined;

    console.log('Connecting to InfinityFree via headless background agent...');
    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: chromePath,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

    const url = 'http://workforgemarketplace.gt.tc/installer.php?secret=workforge2026';
    console.log(`Navigating to ${url}...`);

    try {
        await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
        // Wait 3 seconds for challenge and migration output
        await new Promise(r => setTimeout(r, 4000));

        const content = await page.evaluate(() => document.body.innerText);
        console.log('====================================================');
        console.log('SERVER RESPONSE OUTPUT:');
        console.log('====================================================');
        console.log(content);
        console.log('====================================================');
    } catch (e) {
        console.error('Navigation error:', e.message);
    } finally {
        await browser.close();
    }
}

run();
