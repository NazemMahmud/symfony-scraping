// puppeteer-scripts/cloudflare-bypass-html.js
// const puppeteer = require('puppeteer');

const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());

const headersToRemove = [
    "host", "user-agent", "accept", "accept-encoding", "content-length",
    "forwarded", "x-forwarded-proto", "x-forwarded-for", "x-cloud-trace-context"
];

const responseHeadersToRemove = ["Accept-Ranges", "Content-Length", "Keep-Alive", "Connection", "content-encoding", "set-cookie"];

(async () => {
    // TODO: for dockerfile ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

    let options = {
        headless: 'new',
        ignoreHTTPSErrors: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    };
    if (process.env.PUPPETEER_SKIP_CHROMIUM_DOWNLOAD)
        options.executablePath = process.env.PUPPETEER_EXECUTABLE_PATH; // '/usr/bin/chromium-browser';
    if (process.env.PUPPETEER_HEADFUL)
        options.headless = false;
    if (process.env.PUPPETEER_USERDATADIR)
        options.userDataDir = process.env.PUPPETEER_USERDATADIR;
    if (process.env.PUPPETEER_PROXY)
        options.args.push(`--proxy-server=${process.env.PUPPETEER_PROXY}`);


    const browser = await puppeteer.launch(options);
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });
    // await page.setUserAgent('Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0')

    const URL = "https://rekvizitai.vz.lt/en/company-search/1"; // https://rekvizitai.vz.lt/en/company-search/1/?__cf_chl_tk=ZDhfxGWy1zSl5KGQUqnq05VnUmYyg7LNTcjdDEueoBc-1691659411-0-gaNycGzNC3s
    const formUrl = 'https://rekvizitai.vz.lt/en/company-search/1/';

    // const formData = new URLSearchParams({
    //     code: '302801462',
    //     order: '1',
    //     resetFilter: '0'
    // }).toString();

    // const headers = {
    //     'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    //     'Content-Type': 'application/x-www-form-urlencoded',
    //     ':authority': 'rekvizitai.vz.lt',
    //     ':method': 'POST',
    //     ':path': '/en/company-search/1/',
    //     ':scheme': 'https',
    //     'accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    //     'accept-encoding': 'gzip, deflate, br',
    //     'accept-language': 'en-US,en;q=0.9',
    //     'cache-control': 'max-age=0',
    //     'content-length': '2372',
    //     'content-type': 'multipart/form-data; boundary=----WebKitFormBoundaryG5JQKaJqIVRPNTgf',
    //     'cookie': 'CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%7D; PHPSESSID=eb1t58qdadklnbn10u3btjf7du; VzLtLoginHash=ioKLhWLIWHxwzyTvTV; _gid=GA1.2.200725408.1691659401; cf_clearance=5dxBalCgPVa5lxVtWgYPa_JeTQu.Ndejp5SQEfQdk2s-1691684163-0-1-1ce970ee.15c4389f.4be82f0e-250.2.1691684163; _gat_UA-724652-3=1; _ga_D931ERQW91=GS1.1.1691684149.6.1.1691684720.0.0.0; _ga=GA1.1.396925687.1690686830',
    //     'origin': 'https://rekvizitai.vz.lt',
    //     'referer': 'https://rekvizitai.vz.lt/en/company-search/',
    //     'sec-ch-ua': '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
    //     'sec-ch-ua-mobile': '?0',
    //     'sec-fetch-dest': 'document',
    //     'sec-fetch-mode': 'navigate',
    //     'sec-fetch-site': 'same-origin',
    //     'sec-fetch-user': '?1',
    //     'upgrade-insecure-requests': '1',
    //     'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
    // };

    const headers = {
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Content-Type': 'application/x-www-form-urlencoded',
        'Host': 'rekvizitai.vz.lt',
        'method': 'POST',
        'path': '/en/company-search/1/',
        'scheme': 'https',
        'accept-encoding': 'gzip, deflate, br',
        'accept-language': 'en-US,en;q=0.9',
        'cache-control': 'max-age=0',
        'content-length': '2372',
        'cookie': 'CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%7D; PHPSESSID=eb1t58qdadklnbn10u3btjf7du; VzLtLoginHash=ioKLhWLIWHxwzyTvTV; _gid=GA1.2.200725408.1691659401; cf_clearance=5dxBalCgPVa5lxVtWgYPa_JeTQu.Ndejp5SQEfQdk2s-1691684163-0-1-1ce970ee.15c4389f.4be82f0e-250.2.1691684163; _gat_UA-724652-3=1; _ga_D931ERQW91=GS1.1.1691684149.6.1.1691684720.0.0.0; _ga=GA1.1.396925687.1690686830',
        'origin': 'https://rekvizitai.vz.lt',
        'referer': 'https://rekvizitai.vz.lt/en/company-search/',
        'sec-ch-ua': '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
        'sec-ch-ua-mobile': '?0',
        'sec-fetch-dest': 'document',
        'sec-fetch-mode': 'navigate',
        'sec-fetch-site': 'same-origin',
        'sec-fetch-user': '?1',
        'upgrade-insecure-requests': '1',
        'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
    };

    // await page.setExtraHTTPHeaders(headers);
    const formData = new FormData();
    formData.append('code', '302801462');
    formData.append('order', '1');
    formData.append('resetFilter', '0');
/*
    const cookies = [
        {
            name: '_ga_D931ERQW91',
            domain: URL,
            value: 'GS1.1.1691659401.4.1.1691659808.0.0.0',
        },
        {
            name: 'VzLtLoginHash',
            domain: URL,
            value: 'ioKLhWLIWHxwzyTvTV',
        },
        {
            name: '_gid',
            domain: URL,
            value: 'GA1.2.200725408.1691659401',
        },
        {
            name: '_ga',
            domain: URL,
            value: 'GA1.1.396925687.1690686830',
        },
        {
            name: 'cf_clearance',
            domain: URL,
            value: 'jL2DIhVD_ZNgFzmOdGGpg54PjTZoGoxZMkrvxqSRU8o-1691659401-0-1-1ce970ee.80afecb3.4be82f0e-250.2.1691659401',
        },
        {
            name: '1P_JAR',
            domain: URL,
            value: '2023-08-03-22',
        },
        {
            name: 'PHPSESSID',
            domain: URL,
            value: 'eb1t58qdadklnbn10u3btjf7du',
        },
        {
            name: 'CookieScriptConsent',
            domain: URL,
            value: '%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%7D',
        }
    ];

    // Set local storage data
    // await page.evaluate(data => {
    //     for (const [key, value] of Object.entries(data)) {
    //         localStorage.setItem(key, value);
    //     }
    // }, localStorageData);

    const localStorageEntries = Object.entries(localStorageData);

    const setLocalStorage = `
        Object.entries(JSON.parse('${JSON.stringify(localStorageData)}')).forEach(([key, value]) => {
            localStorage.setItem(key, value);
        });
    `;

    await page.evaluateOnNewDocument(setLocalStorage);

    // Set session storage data
    // await page.evaluate(data => {
    //     for (const [key, value] of Object.entries(data)) {
    //         sessionStorage.setItem(key, value);
    //     }
    // }, sessionStorageData);


    // Set cookies
    await page.setCookie(...cookies);
    // await page.deleteCookie({
    //     domains: ["https://www.google.com"]
    // });
*/
    // Navigate to the site URL
    // await page.goto(formUrl, { waitUntil: 'domcontentloaded' });
    // await page.goto(formUrl);
    await page.goto(formUrl, { waitUntil: 'networkidle2' });
    // await page.evaluate(() => {
    //     // Fill the form inputs
    //     document.querySelector('input[name="code"]').value = '302801462';
    //     document.querySelector('input[name="order"]').value = '1';
    //     document.querySelector('input[name="resetFilter"]').value = '0';
    //
    //     // Submit the form
    //     document.querySelector('form').submit();
    // });

    // const response = await page.evaluate((headers, formUrl) => {
    //     const formData = new FormData();
    //     formData.append('code', '302801462');
    //     formData.append('order', '1');
    //     formData.append('resetFilter', '0');
    //
    //     // Submit the form
    //     return fetch(formUrl, {
    //         method: 'POST',
    //         headers: headers,
    //         body: formData,
    //     });
    // }, headers);

    // const response = await fetch(formUrl, {
    //     method: 'POST',
    //     headers: headers,
    //     body: formData,
    // });

    // await page.waitForNavigation(); // Wait for the navigation to complete

    // await page.goto(formUrl, {
    //     method: 'POST',
    //     formData,
    // });

    // await page.goto(URL);
    // await page.setJavaScriptEnabled(true);

/*    // Wait for Cloudflare iframe to appear
    const iframeSelector = 'iframe[id^="cf-chl-widget-"]';
    await page.waitForSelector(iframeSelector, { timeout: 60000 });
    //
    // // Switch to the Cloudflare iframe
    const iframeHandle = await page.$(iframeSelector);
    const frame = await iframeHandle.contentFrame();
    //
    // // Wait for the checkbox to become visible
    // const checkboxSelector = '.ctp-checkbox-label input[type="checkbox"]';
    // const parentSelector = '.ctp-checkbox-label';
    await frame.waitForSelector('#challenge-stage', { display: 'block', timeout: 60000});
    // await frame.waitForSelector(checkboxSelector, { timeout: 60000 });
/*
    // Click the checkbox
    // await frame.click(checkboxSelector);

    // Wait for the page to reload (this may vary based on actual behavior)
    // await page.waitForNavigation();
*/
// console.log(location.href);
//     const response = await page.evaluate(async (formData, headers, formUrl) => {
//         const response = await fetch(formUrl, {
//             method: 'POST',
//             headers: headers,
//             body: formData,
//             credentials: 'include'
//         });
//
//         const text = await response.text();
//         return text;
//     }, formData, headers);

    // const response = await page.fe(formUrl, formData);
    // const content = await response.text();

    const response = await page.evaluate(async (headers, formUrl) => {
        const formData = new FormData();
        formData.append('code', '302801462');
        formData.append('order', '1');
        formData.append('resetFilter', '0');

        // Submit the form
        const response = await fetch(formUrl, {
            method: 'POST',
            headers: headers,
            body: formData,
        });

        // Convert the response to an object with relevant information
        const responseData = {
            status: response.status,
            headers: {},
            body: await response.text(),
        };

        // Convert response headers to a plain object
        response.headers.forEach((value, name) => {
            responseData.headers[name] = value;
        });

        return responseData;
    }, headers, formUrl);
    console.log('Response Status:', response);
    // console.log('Response Headers:', response.headers);
    // console.log('Response Body:', response.body);
    // const status = JSON.parse(response.toString());
    // console.log(status);
    // console.log(response);
    // Get the HTML content of the page
    // const htmlContent = await page.content();

    // Print the HTML content
    // console.log(htmlContent);

    // Keep the browser open for further actions
    await browser.close();
})();
