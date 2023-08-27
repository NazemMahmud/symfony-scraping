require('dotenv').config();

const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());

const username = process.env.SCRAPE_TOKEN;
const password = "render=false";
const proxyUrl = "proxy.scrape.do:8080";
const mainURL = "https://rekvizitai.vz.lt/en/company-search";

let browser = null;
let page = null;

const customHeadersPost = {
    "authority": "rekvizitai.vz.lt",
};


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
    return page.content();
};

/**
 * Scrape 3: get details info of that company to store
 * as getting list using code, there will always be one company link
 */
const getDetailsPage = async (html) => {
    const linkRegex = /<a class="company-title d-block" href="([^"]+)"/;
    const match = html.match(linkRegex);

    if (match && match[1]) {
        const link = match[1];
        const newURL = link.startsWith('https://') ? link : `${mainURL}${link}`;

        // Open a new page and navigate to the extracted link
        const newPage = await browser.newPage();
        await newPage.goto(newURL, { waitUntil: 'networkidle2' });
        await newPage.waitForSelector('h1.title');
        await newPage.content();

        const data = await getData(newPage);

        await newPage.close();

        return data;
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

const scrapePage = async code => {
    // Scrape 1: scrape the main page
    await setBrowser();
    await setPageWithProxy();
    await page.content();

    const loadContent = await formSubmit(code);

    // Scrape 3: Extract link and scrape
    const linkedPageContent = await getDetailsPage(loadContent);

    await browser.close();

    return linkedPageContent;
}

/**
 * Get Company name, VAT, Address & Mobile Phone
 * @returns {Promise<Awaited<ReturnType<function(): {companyName: string, vat: string, address: string, mobilePhone: string}>>>}
 */
const getData = async detailsPage => {
    try {
        const companyDetails = await detailsPage.evaluate(() => {
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
            let foundPhone = false;
            let foundAddress = false;
            for (const row of rows) {
                const nameCell = row.querySelector('td.name');
                if (nameCell) {
                    const name = nameCell.textContent.trim();
                    if (name === 'Address') {
                        const valueCell = nameCell.nextElementSibling;
                        if (valueCell && valueCell.classList.contains('value')) {
                            details.address = valueCell.textContent.trim();
                            foundAddress = true;
                        }
                    } else if (foundAddress && name.toLowerCase().includes('phone')) {
                        const valueCell = nameCell.nextElementSibling;
                        if (valueCell && valueCell.classList.contains('value')) {
                            const img = valueCell.querySelector('img');
                            if (img) {
                                const mobilePhoneSrc = img.getAttribute('src');
                                details.mobilePhone = mobilePhoneSrc.startsWith('http') ? mobilePhoneSrc : `https://rekvizitai.vz.lt${mobilePhoneSrc}`;
                                foundPhone = true
                            }
                        }
                    }

                    if (foundPhone && foundAddress) break;
                }
            }

            return {
                companyName,
                ...details
            };
        });
        return companyDetails;
    } catch (err) {
       return null;
    }
}

(async () => {
    const registrationCode = process.argv[2];
    // const res = await scrapePage('302801462');
    // const res = await scrapePage('301108117');
    const res = await scrapePage(registrationCode);

    console.log(res);

})();
