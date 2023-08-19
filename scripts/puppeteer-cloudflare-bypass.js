require('dotenv').config();
const fs = require('fs');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
const path = require("path");
puppeteer.use(StealthPlugin());

// let formUrlPost = "https://rekvizitai.vz.lt/en/company-search/1"; // for POST waitSelector=. no need i guess
// const URL = "https://rekvizitai.vz.lt/en/company-search";

const URL = "https://google.com";

const scrapePage = async () => {

    // "Content-Type": "multipart/form-data; boundary=----WebKitFormBoundaryJWjHjqgnFEUVb3FS",
// "Sec-Ch-Ua":'"Chromium";v="116", "Not)A;Brand";v="24", "Google Chrome";v="116"',
// "Sec-Ch-Ua-Mobile": "?0",
// "Sec-Ch-Ua-Platform": "Windows",
// "Sec-Fetch-Dest": "document",
// "Sec-Fetch-Mode": "navigate",
// "Sec-Fetch-Site": "same-origin",
// "Sec-Fetch-User": "?1",
// "Upgrade-Insecure-Requests": 1,
// "Accept-Encoding": "gzip, deflate, br",
//     "Accept-Language": "en-US,en;q=0.9",
// "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36"
// "Cache-Control": "max-age=0",
//     "Content-Length": 2372,
    // const customHeadersPost = {
    //     "authority": "rekvizitai.vz.lt",
    //     "method": 'POST',
    //     "path": "/en/company-search/1/",
    //     "scheme": "https",
    //     "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
    //     "Origin": "https://rekvizitai.vz.lt",
    //     "Referer": "https://rekvizitai.vz.lt/en/company-search/1/"
    // }

    // const username = process.env.SCRAPE_TOKEN;
    // const password = "render=false";
    // const proxyUrl = "proxy.scrape.do:8080";

    console.log(process.env.PUPPETEER_EXECUTABLE_PATH);
    let options = {
        headless: 'new', // false
        ignoreHTTPSErrors: true,
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
        // args: ['--no-sandbox', `--proxy-server=http://${proxyUrl}` ],
        // args: ['--no-sandbox', `--proxy-server=http://${username}:${password}@${proxyUrl}` ],
        args: ['--no-sandbox' ],
        dumpio: false,
        deviceScaleFactor: 1,
        hasTouch: false,
        isLandscape: false,
        isMobile: false,
    };
    // console.log("tokne: ", username);
    console.log("here: ");
    try
    {
        let browser = await puppeteer.launch(options);
        const page = await browser.newPage();
        await page.setRequestInterception(true);
        const UA = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36"; // || USER_AGENT;
        await page.setUserAgent(UA);
        await page.setDefaultNavigationTimeout(0);

        await page.setViewport({ width: 1920, height: 3000 });
        // await page.authenticate({username, password});
        // await page.setExtraHTTPHeaders(customHeadersPost)
        await page.goto(URL, { waitUntil: 'networkidle2' });
        // await page.waitForSelector("#code");
        // await page.goto(targetUrlPost, { waitUntil: 'networkidle2' });
        // await page.screenshot({ path: 'example.png'});
        // console.log("proxy: ", proxyUrl);
        // await page.evaluate(() => {
        //     const searchForm = document.querySelector('form.searchBar--form')
        //     searchForm.submit() // this takes me to a new page which I need to wait for and then ideally return something.
        //
        //     // I've tried adding code here, but it doesn't run...
        // }, term, location)
        // await page.type('#code', '302801462');
        // await page.type('#order', '1');
        // await page.type('#resetFilter', '0');
        // console.log("now cick: ");
        // await page.click('#ok');
        // await page.waitForNavigation({ waitUntil: 'networkidle2'});
        // console.log("Navigating...: ");

        const res = await page.content();
        console.log(`content:::: ${res}`);

        // const desiredElementExists = await page.evaluate((desiredText) => {
        //     const h1Elements = Array.from(document.querySelectorAll('h1'));
        //     return h1Elements.some(element => element.textContent.includes(desiredText));
        // }, desiredText);

        await browser.close();

        return res;
    } catch (error) {
        console.error('Error: ', error);
    }
    // return desiredElementExists ? res : null;
}


const writeFile = async (templateDir, filePath, text) => {
    // Make sure the template directory exists before writing the file
    if (!fs.existsSync(templateDir)) {
        fs.mkdirSync(templateDir, { recursive: true });
    }

    fs.writeFile(filePath, text, function (writeError) {
        if (writeError) {
            console.error("Error writing to file:", writeError);
        } else {
            console.log("Response body saved to response.html");
        }
    });
}

const callScrap = async () => {
    const res = await scrapePage();

        if (res) {
            console.log('SUCCESS');
            console.log(res);
            const templateDir = path.join(__dirname, '../template');
            const responseFilePath = path.join(templateDir, 'response.html');
            await writeFile(templateDir, responseFilePath, res);
        }
}



(async () => {
    // TODO: for dockerfile ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
    // const userAgents = [
    //     "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36",
    //     "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36"
    // ];

    // const oldProxyUrl = process.env.PROXY_SERVER;
    // const oldProxyUrl = 'http://202.69.38.82:8080';

    // const newProxyUrl = await proxyChain.anonymizeProxy(oldProxyUrl);

    // if (process.env.PUPPETEER_USERDATADIR)
    //     options.userDataDir = process.env.PUPPETEER_USERDATADIR;
    // if (process.env.PUPPETEER_PROXY)
    //     options.args.push(`--proxy-server=${process.env.PUPPETEER_PROXY}`);
    try {
        await callScrap();
    } catch (error) {
        if (error.message.includes("net::ERR_TIMED_OUT")) {
            console.error("Timed out error. Retrying...");
            // await callScrap();
        } else {
            console.error(error);
        }
    }

})();
