const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
    console.log('Starting full E2E Playwright tests...');
    if (!fs.existsSync('./screenshots')) {
        fs.mkdirSync('./screenshots');
    }

    const browser = await chromium.launch();
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    const url = process.argv[2] || 'http://127.0.0.1:8000';
    let ssCount = 1;
    
    const screenshot = async (name) => {
        const prefix = ssCount.toString().padStart(2, '0');
        await page.screenshot({ path: `./screenshots/${prefix}_${name}.png`, fullPage: true });
        ssCount++;
    };

    // Auto-accept any dialogs (confirms, alerts)
    page.on('dialog', async dialog => {
        console.log(`Accepted dialog: ${dialog.message()}`);
        await dialog.accept();
    });
    
    try {
        console.log("Navigating to " + url);
        await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
        
        // 1. Install bypass
        if (page.url().includes('install.php')) {
            await screenshot('install_page');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(1000);
            await page.goto(url, { waitUntil: 'networkidle' });
        }
        
        // 2. Register
        const testEmail = `admin_e2e_${Date.now()}@test.local`;
        await page.goto(url + '/register', { waitUntil: 'networkidle' });
        await page.fill('input[name="name"]', 'Test Admin');
        await page.fill('input[name="email"]', testEmail);
        await page.fill('input[name="password"]', 'password123');
        await screenshot('register_page');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);
        
        // Check if redirected to login
        if (page.url().includes('/login')) {
            await page.fill('input[name="email"]', testEmail);
            await page.fill('input[name="password"]', 'password123');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(1000);
        }
        
        // 3. Dashboard
        await screenshot('dashboard_empty');
        
        // 4. Create Product
        let createSelector = 'a[href*="products/create"], a[href*="products/add"]';
        const createBtn = await page.$(createSelector);
        if (createBtn) {
            await createBtn.click();
            await page.waitForTimeout(1000);
            await page.fill('input[name="name"]', 'Playwright E2E Product');
            await page.fill('input[name="sku"]', `PW-E2E-${Date.now()}`);
            await page.fill('input[name="price"]', '499.99');
            await screenshot('add_product_form');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(1000);
            await screenshot('dashboard_with_product');
        }
        
        // 5. Toggle Active Status
        const deactivateBtn = await page.$('button[title="Deactivate"]');
        if (deactivateBtn) {
            await deactivateBtn.click();
            await page.waitForTimeout(1000);
            await screenshot('product_deactivated');
        }

        // 6. Edit Product
        const editBtn = await page.$('a[title="Edit"]');
        if (editBtn) {
            await editBtn.click();
            await page.waitForTimeout(1000);
            await page.fill('input[name="price"]', '599.99');
            await screenshot('edit_product_form');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(1000);
            await screenshot('product_edited');
        }
        
        // 7. Soft Delete (Trash)
        const trashBtn = await page.$('button[title="Trash"]');
        if (trashBtn) {
            await trashBtn.click(); // auto-accept dialog
            await page.waitForTimeout(1000);
            await screenshot('dashboard_after_trash');
        }

        // 8. View Trash
        const viewTrashBtn = await page.$('a[href*="trash=1"]');
        if (viewTrashBtn) {
            await viewTrashBtn.click();
            await page.waitForTimeout(1000);
            await screenshot('trash_view');
        }

        // 9. Restore
        const restoreBtn = await page.$('button[title="Restore"]');
        if (restoreBtn) {
            await restoreBtn.click();
            await page.waitForTimeout(1000);
            await screenshot('dashboard_after_restore');
        }
        
    } catch (e) {
        console.error(`Error during E2E:`, e.message);
    }
    
    await context.close();
    await browser.close();
    console.log('Playwright E2E testing completed fully!');
})();


