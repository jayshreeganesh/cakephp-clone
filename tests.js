const { chromium, devices } = require('playwright');
const fs = require('fs');

(async () => {
    console.log('Starting full E2E Playwright tests...');
    if (!fs.existsSync('./screenshots')) {
        fs.mkdirSync('./screenshots');
    }

    const browser = await chromium.launch();
    
    // Test on Desktop only to test full functionality quickly
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    
    // Try to get URL from args, default to 8000
    const url = process.argv[2] || 'http://127.0.0.1:8000';
    
    try {
        console.log("Navigating to " + url);
        await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
        
        // Handle potential install page redirect
        if (page.url().includes('install.php')) {
            await page.screenshot({ path: `./screenshots/01_install_page.png` });
            await page.click('button[type="submit"]');
            await page.waitForTimeout(1000);
            await page.goto(url, { waitUntil: 'networkidle' });
        }
        
        await page.goto(url + '/login', { waitUntil: 'networkidle' });
        await page.screenshot({ path: `./screenshots/02_login_page.png` });
        
        // Login
        await page.fill('input[name="email"]', 'admin@test.local');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        
        await page.waitForTimeout(1000);
        
        // Dashboard
        await page.screenshot({ path: `./screenshots/03_dashboard.png`, fullPage: true });
        
        // Test Add Product
        await page.goto(url + '/products/create', { waitUntil: 'networkidle' }).catch(() => {});
        if (page.url().includes('create') || page.url().includes('add')) {
            await page.fill('input[name="name"]', 'Playwright Test Product');
            await page.fill('input[name="sku"]', 'PW-100');
            await page.fill('input[name="price"]', '99.99');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(1000);
            await page.screenshot({ path: `./screenshots/04_after_add.png`, fullPage: true });
        }
        
    } catch (e) {
        console.error(`Error during E2E:`, e.message);
    }
    
    await context.close();
    await browser.close();
    console.log('Playwright tests completed.');
})();
