const { chromium, devices } = require('playwright');
const fs = require('fs');

(async () => {
    console.log('Starting Playwright screenshot tests for CakePHP...');
    
    if (!fs.existsSync('./screenshots')) {
        fs.mkdirSync('./screenshots');
    }

    const browser = await chromium.launch();
    
    const viewports = [
        { name: 'Mobile_iPhone13', ...devices['iPhone 13'] },
        { name: 'Tablet_iPad', ...devices['iPad (gen 7)'] },
        { name: 'Desktop_1080p', viewport: { width: 1920, height: 1080 } }
    ];

    for (const vp of viewports) {
        console.log(`Testing viewport: ${vp.name}`);
        const context = await browser.newContext(vp);
        const page = await context.newPage();
        
        try {
            await page.goto('http://127.0.0.1:8003/login', { waitUntil: 'networkidle' });
            
            // Login
            await page.fill('input[name="email"]', 'admin@test.local');
            await page.fill('input[name="password"]', 'password'); // Assume standard test password
            await page.click('button[type="submit"]');
            
            await page.waitForTimeout(1000); // Wait for redirect
            
            // Check if we hit install.lock block or dashboard
            const url = page.url();
            if (url.includes('install.php')) {
                await page.goto('http://127.0.0.1:8003/products', { waitUntil: 'networkidle' });
            }
            
            await page.screenshot({ path: `./screenshots/CakePHP_${vp.name}_dashboard.png`, fullPage: true });
            
        } catch (e) {
            console.error(`Error on ${vp.name}:`, e.message);
        }
        await context.close();
    }
    
    await browser.close();
    console.log('Playwright tests completed.');
})();
