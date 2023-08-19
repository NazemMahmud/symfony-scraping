require('dotenv').config();
// const fs = require('fs');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
const fs = require("fs");
const path = require("path");
// const path = require("path");
puppeteer.use(StealthPlugin());

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


(async () => {
    const customHeadersPost = {
        "authority": "rekvizitai.vz.lt",
    //     "method": 'POST',
    //     "path": "/en/company-search/1/",
    //     "scheme": "https",
    //     "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
    //     "Origin": "https://rekvizitai.vz.lt",
    //     "Referer": "https://rekvizitai.vz.lt/en/company-search/1/"
    }

    const username = process.env.SCRAPE_TOKEN;
    const password = "render=false";
    const proxyUrl = "proxy.scrape.do:8080";
    const URL = "https://rekvizitai.vz.lt/en/company-search";
    console.log('token: ', username);
    console.log('pass: ', password);
    console.log('url: ', URL);
    // const google ='https://google.com';
    const browser = await puppeteer.launch({
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
        ignoreHTTPSErrors: true,
        headless: 'new', // Set to false if you want to see the browser window,
        args: ['--no-sandbox',
            `--proxy-server=http://${proxyUrl}`
        ],
    });

    const page = await browser.newPage();

    // Navigate to the website you want to scrape
    await page.authenticate({username, password});
    await page.setExtraHTTPHeaders(customHeadersPost);
    await page.goto(URL, {waitUntil: 'networkidle2'});
    const res = await page.content();

    console.log(res);

    const templateDir = path.join(__dirname, '../template');
    const responseFilePath = path.join(templateDir, 'response.html');
    await writeFile(templateDir, responseFilePath, res);

    // Perform scraping operations here
    // For example, you can extract data using page.evaluate() or other Puppeteer functions

    // Close the browser when done
    await browser.close();
})();
