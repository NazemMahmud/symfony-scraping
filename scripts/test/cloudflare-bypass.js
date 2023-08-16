require('dotenv').config();
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const randomUseragent = require('random-useragent');
// const proxyChain = require('proxy-chain');
// const getFreeProxies = require('get-free-https-proxy');

const headersToRemove = [
    "host", "user-agent", "accept", "accept-encoding", "content-length",
    "forwarded", "x-forwarded-proto", "x-forwarded-for", "x-cloud-trace-context"
];

const responseHeadersToRemove = ["Accept-Ranges", "Content-Length", "Keep-Alive", "Connection", "content-encoding", "set-cookie"];

const maxTries = process.env.MAX_TRIES;
const URL = "https://nowsecure.nl/";
const PROXY = '';
// const URL = "https://rekvizitai.vz.lt/en/company-search";

const scrapePage = async () => {
    // let [proxy1] = await getFreeProxies();
    // console.log('proxy : ', proxy1);
    let options = {
        headless: 'new',
        ignoreHTTPSErrors: true,
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
        args: ['--no-sandbox', '--disable-setuid-sandbox'
        ],
        dumpio: false,
        deviceScaleFactor: 1,
        hasTouch: false,
        isLandscape: false,
        isMobile: false,
    };

    let browser = await puppeteer.launch(options);
    const page = await browser.newPage();
    const userAgent = await randomUseragent.getRandom();
    const UA = userAgent; // || USER_AGENT;

    await page.setViewport({ width: 1920, height: 3000 });
    await page.setUserAgent(UA);

    await page.setDefaultNavigationTimeout(0);
    await page.goto(URL, { waitUntil: 'networkidle2' });
    // await page.screenshot({ path: 'example.png'});

    const res = await page.content();
    const desiredText = 'OH YEAH, you passed!';
    console.log(res);

    const desiredElementExists = await page.evaluate((desiredText) => {
        const h1Elements = Array.from(document.querySelectorAll('h1'));
        return h1Elements.some(element => element.textContent.includes(desiredText));
    }, desiredText);

    await browser.close();

    return desiredElementExists ? res : null;
}

const callScrap = async () => {
    const res = await scrapePage();

        if (res !== null) {
            console.log('SUCCESS');

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
