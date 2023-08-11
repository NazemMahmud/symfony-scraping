const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const randomUseragent = require('random-useragent');
const proxyChain = require('proxy-chain');
const getFreeProxies = require('get-free-https-proxy');

const headersToRemove = [
    "host", "user-agent", "accept", "accept-encoding", "content-length",
    "forwarded", "x-forwarded-proto", "x-forwarded-for", "x-cloud-trace-context"
];

const responseHeadersToRemove = ["Accept-Ranges", "Content-Length", "Keep-Alive", "Connection", "content-encoding", "set-cookie"];

(async () => {
    // TODO: for dockerfile ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
    const userAgents = [
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36"
    ];

    const [proxy1] = await getFreeProxies();

    // const oldProxyUrl = process.env.PROXY_SERVER;
    const oldProxyUrl = 'http://202.69.38.82:8080';
    // console.log('proxy: ', oldProxyUrl);
    console.log('proxy 2: ', proxy1);
    const newProxyUrl = await proxyChain.anonymizeProxy(oldProxyUrl);

    let options = {
        headless: 'new',
        ignoreHTTPSErrors: true,
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
        args: ['--no-sandbox', '--disable-setuid-sandbox',
            `--proxy-server=${proxy1.host}:${proxy1.port}`
            // `--proxy-server=${newProxyUrl}`
        ],
        dumpio: false,
        deviceScaleFactor: 1,
        hasTouch: false,
        isLandscape: false,
        isMobile: false,
    };
    // if (process.env.PUPPETEER_USERDATADIR)
    //     options.userDataDir = process.env.PUPPETEER_USERDATADIR;
    // if (process.env.PUPPETEER_PROXY)
    //     options.args.push(`--proxy-server=${process.env.PUPPETEER_PROXY}`);



    let browser = await puppeteer.launch(options);
    const page = await browser.newPage();
    const userAgent = randomUseragent.getRandom();
    const UA = userAgent || USER_AGENT;

    await page.setViewport({ width: 1920, height: 3000 });
    await page.setUserAgent(UA);

    // const URL = "https://nowsecure.nl/";
    const URL = "https://rekvizitai.vz.lt/en/company-search";

    await page.setDefaultNavigationTimeout(0);
    await page.goto(URL, { waitUntil: 'networkidle2' });
    // await page.screenshot({ path: 'example.png'});

    const res = await page.content();
    console.log(res);

    await browser.close();
})();
