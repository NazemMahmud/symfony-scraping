require('dotenv').config();
// const fs = require('fs');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
const fs = require("fs");
const path = require("path");
// const path = require("path");
puppeteer.use(StealthPlugin());

const username = process.env.SCRAPE_TOKEN;
const password = "render=false";
const proxyUrl = "proxy.scrape.do:8080";
const mainURL = "https://rekvizitai.vz.lt/en/company-search";

let browser = null;
let page = null;

const customHeadersPost = {
    "authority": "rekvizitai.vz.lt",
    //     "method": 'POST',
    //     "path": "/en/company-search/1/",
    //     "scheme": "https",
    //     "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
    //     "Origin": "https://rekvizitai.vz.lt",
    //     "Referer": "https://rekvizitai.vz.lt/en/company-search/1/"
};



const writeFile = async (templateDir, filePath, text) => {
    // Make sure the template directory exists before writing the file
    if (!fs.existsSync(templateDir)) {
        fs.mkdirSync(templateDir, { recursive: true });
    }

    fs.writeFile(filePath, text, function (writeError) {
        if (writeError) {
            console.error("Error writing to file:", writeError);
        } else {
            console.log("*************-------------------*********************");
            console.log("Response body saved to response.html");
        }
    });
}

const setBrowser = async () => {
    const options = {
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
        ignoreHTTPSErrors: true,
        headless: 'new', // Set to false if you want to see the browser window,
        args: ['--no-sandbox',
            `--proxy-server=http://${proxyUrl}`
        ],
    };

    browser = await puppeteer.launch(options);
};

const setPageWithProxy = async () => {
    page = await browser.newPage();

    await page.authenticate({username, password});
    await page.setExtraHTTPHeaders(customHeadersPost);
    await page.goto(mainURL, {waitUntil: 'networkidle2'});
}

/**
 * Scrape 2:  Fill and submit the form
 */
const formSubmit = async (code) => {

    await page.type('#code', code);
    await page.type('#order', '1');
    await page.type('#resetFilter', '0');
    await page.click('#ok');

    // Wait for navigation to the page after form submission
    await page.waitForNavigation({ waitUntil: 'networkidle2' });

    // Get the content of the loaded page
    // const loadedPageContent = await
    return page.content();
};

/**
 * To-do
 * 1. Scrape 1: the main page content, need the form to submit
 * 2. Submit the form with proper data
 * 3. Scrape 2: That will load a new page with list item (only 1 item will be there, as we are sending specific registration code)
 * 4. get the URL from the list to redirect to "details" page.
 * 5. Scrape 3: Redirect to the detail page, Store the data into an array, return that in PHP Service
 */

const scrapePage = async (code) => {
    console.log('token: ', username);
    console.log('pass: ', password);
    console.log('url: ', mainURL);

    // Scrape 1: scrape the main page
    await setBrowser();
    await setPageWithProxy();
    const res = await page.content();

    console.log(res);
    console.log('-----------------------------------------------------------------------------------');

    const loadContent = await formSubmit(code);


    // Close the browser when done
    await browser.close();
    const templateDir = path.join(__dirname, '../template');
    const responseFilePath = path.join(templateDir, 'response.html');
    await writeFile(templateDir, responseFilePath, loadContent);

    return loadContent;
}

(async () => {

    const res = await scrapePage('302801462')
    console.log(res);

    // Scrape 3:
})();
