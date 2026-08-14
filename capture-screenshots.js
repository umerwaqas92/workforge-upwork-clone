import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/screenshots');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

async function run() {
    console.log('Launching browser to capture screenshots...');
    const chromePath = fs.existsSync('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome')
        ? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
        : undefined;

    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: chromePath,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900, deviceScaleFactor: 2 });

    const baseUrl = 'http://127.0.0.1:8008';

    // 1. Homepage
    console.log('Capturing Homepage...');
    await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '01_homepage.png'), fullPage: false });

    // 2. Browse Jobs
    console.log('Capturing Browse Jobs...');
    await page.goto(`${baseUrl}/jobs`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '02_browse_jobs.png'), fullPage: false });

    // 3. Freelancers Directory & Profile
    console.log('Capturing Freelancer Profile...');
    await page.goto(`${baseUrl}/freelancers/5`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '03_freelancer_profile.png'), fullPage: false });

    // 4. Quick login as Freelancer & Profile Builder
    console.log('Capturing Profile Builder...');
    await page.goto(`${baseUrl}/quick-login/freelancer`, { waitUntil: 'networkidle2' });
    await page.goto(`${baseUrl}/settings/profile`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '04_profile_builder.png'), fullPage: false });

    // 5. Quick login as Client & Contract Workroom
    console.log('Capturing Contract Workroom...');
    await page.goto(`${baseUrl}/quick-login/client`, { waitUntil: 'networkidle2' });
    await page.goto(`${baseUrl}/contracts/1`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '05_contract_workroom.png'), fullPage: false });

    // 6. Wallet Ledger
    console.log('Capturing Wallet & Ledger...');
    await page.goto(`${baseUrl}/wallet`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '06_wallet_ledger.png'), fullPage: false });

    // 7. Dodo Payments Checkout Simulator
    console.log('Capturing Dodo Payments Checkout...');
    await page.goto(`${baseUrl}/payments/dodo/simulator?amount=250&purpose=wallet_deposit`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '07_dodo_checkout.png'), fullPage: false });

    // 8. Quick login as Admin & Dashboard
    console.log('Capturing Admin Super-Panel...');
    await page.goto(`${baseUrl}/quick-login/admin`, { waitUntil: 'networkidle2' });
    await page.goto(`${baseUrl}/admin/dashboard`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outDir, '08_admin_panel.png'), fullPage: false });

    await browser.close();
    console.log('All screenshots captured successfully in docs/screenshots/');
}

run().catch(err => {
    console.error('Error capturing screenshots:', err);
    process.exit(1);
});
