// puppeteer-scripts/cloudflare-bypass-html.js
const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    const URL = "https://www.ionos.com/tools/website-checker"; // https://rekvizitai.vz.lt/en/company-search/

    // Navigate to the site URL
    await page.goto(URL);
/*
    // Wait for Cloudflare iframe to appear
    // const iframeSelector = 'iframe[id^="cf-chl-widget-"]';
    // await page.waitForSelector(iframeSelector);

    // Switch to the Cloudflare iframe
    // const iframeHandle = await page.$(iframeSelector);
    // const frame = await iframeHandle.contentFrame();

    // Wait for the checkbox to become visible
    // const checkboxSelector = '.ctp-checkbox-label input[type="checkbox"]';
    // await frame.waitForSelector(checkboxSelector);

    // Click the checkbox
    // await frame.click(checkboxSelector);

    // Wait for the page to reload (this may vary based on actual behavior)
    // await page.waitForNavigation();
*/
    // Get the HTML content of the page
    const htmlContent = await page.content();

    // Print the HTML content
    console.log(htmlContent);

    // Keep the browser open for further actions
})();
