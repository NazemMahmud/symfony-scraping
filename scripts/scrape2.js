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

/**
 * Set the browser value initially to use it all places
 * @returns {Promise<void>}
 */
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

/**
 * Scrape 1: 1st page scrap to load the form
 * @returns {Promise<void>}
 */
const setPageWithProxy = async () => {
    page = await browser.newPage();

    await page.authenticate({username, password});
    await page.setExtraHTTPHeaders(customHeadersPost);
    await page.goto(mainURL, {waitUntil: 'networkidle2'});
}

/**
 * Scrape 2:  Fill and submit the form to get list
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
 * Scrape 3: get details info of that company to store
 * as getting list using code, there will always be one company link
 */
const getDetailsPage = async (html) => {
    const linkRegex = /<a class="company-title d-block" href="([^"]+)"/;
    const match = html.match(linkRegex);

    console.log('Match values: ', match);
    if (match && match[1]) {
        const link = match[1];
        const newURL = link.startsWith('https://') ? link : `${mainURL}${link}`;

        // Open a new page and navigate to the extracted link
        const newPage = await browser.newPage();
        await newPage.goto(newURL, { waitUntil: 'networkidle2' });

        // Get the content of the new page
        const newPageContent = await newPage.content();

        // Close the new page
        await newPage.close();

        return newPageContent;
    }

    return null;
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

    // Scrape 3: Extract link and scrape
    const linkedPageContent = await getDetailsPage(loadContent);

    // Close the browser when done
    await browser.close();

    const templateDir = path.join(__dirname, '../template');
    const responseFilePath = path.join(templateDir, 'response.html');
    await writeFile(templateDir, responseFilePath, linkedPageContent);

    return linkedPageContent;
}

/**
 * Get Company name, VAT, Address & Mobile Phone
 * @returns {Promise<Awaited<ReturnType<function(): {companyName: string, vat: string, address: string, mobilePhone: string}>>>}
 */
const getData = async () => {
    try {
        /**
         * TODO: remove browser & page, these are already found from scrapped data
         * remove file actions, I am going to read data from scrape content
         *
         */
        browser = await puppeteer.launch({ headless: 'new',  args: ['--no-sandbox'] });
        page = await browser.newPage();

        const templateDir = path.join(__dirname, '../template');
        const responseFilePath = path.join(templateDir, 'response.html');
        const linkedPageContent = fs.readFileSync(responseFilePath, 'utf8');
        await page.setContent(linkedPageContent);

        // from here everything will be copied
        await page.waitForSelector('h1.title');

        const companyDetails = await page.evaluate(() => {
            const details = {};
            const companyName = document.querySelector('h1.title')?.textContent.trim() || '';

            const regex = /vat/i;
            const nameCells = document.querySelectorAll('.details-block__1 tr td.name');

            for (const nameCell of nameCells) {
                if (regex.test(nameCell.textContent)) {
                    const valueCell = nameCell.nextElementSibling;
                    if (valueCell && valueCell.classList.contains('value')) {
                        details.vat = valueCell.textContent.trim();
                        break;
                    }
                }
            }

            let rows = document.querySelectorAll('.details-block__2 tr');
            let count = 0;
            for (const row of rows) {
                const nameCell = row.querySelector('td.name');
                if (nameCell) {
                    const name = nameCell.textContent.trim();
                    if (name === 'Address') {
                        const valueCell = nameCell.nextElementSibling;
                        if (valueCell && valueCell.classList.contains('value')) {
                            details.address = valueCell.textContent.trim();
                            count++;
                        }
                    } else if (name === 'Mobile phone') {
                        const valueCell = nameCell.nextElementSibling;
                        if (valueCell && valueCell.classList.contains('value')) {
                            const img = valueCell.querySelector('img');
                            if (img) {
                                const mobilePhoneSrc = img.getAttribute('src');
                                details.mobilePhone = mobilePhoneSrc.startsWith('http') ? mobilePhoneSrc : `https://xyz.com/${mobilePhoneSrc}`;
                                count++;
                            }
                        }
                    }

                    if (count > 1) break;
                }
            }

            return {
                companyName,
                ...details
            };
        });

        return companyDetails;
    } catch (err) {
        console.error('Error:', err);
    }
}

(async () => {

    // const res = await scrapePage('302801462');
    const res = await getData();
    console.log(res);

    if (res) {
        console.log("Success.............. ")
    } else {
        console.error("Not Right.............. ")
    }

})();
