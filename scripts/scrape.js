require('dotenv').config();
const puppeteer = require('puppeteer-extra');
const fs = require('fs');
const path = require('path');
const axios = require('axios');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const request = require('request');

let token = process.env.SCRAPE_TOKEN
// let targetUrl = "https://nowsecure.nl";
let targetUrl = "https://rekvizitai.vz.lt/en/company-search"; // for GET
let targetUrlPost = "https://rekvizitai.vz.lt/en/company-search/1"; // for POST waitSelector=. no need i guess
/**
// https://rekvizitai.vz.lt/en/company-search/1/ POST
// wait untill list-item class
// take href from company-title d-block class, something like this: href="https://rekvizitai.vz.lt/en/company/impress_teva/"
//
 * wait untill class: block p-0
 * scrape company profile:
 * Company name: h1 class: title, subtitle also may be
 * under div class details-block__1 > information > table,
 * Registration code: 1st tr, td class=value, check if name is Registration Code
 * VAT: 2nd tr, td class=value, check if name is VAT
 *
 * under div class details-block__2 > information > table,
 * Address:  2nd tr, td class=value, check if name is Address
 * Mobile phone: 4th tr > td, check is name is Mobile phone, but it is an image, under this
 */
const config = {
    waitSelector: '.p-0', // do not use for post
    waitUntil: 'domcontentloaded', // do not use for post
    super: 'true',
    render: 'true', // true for get
    blockResources: 'false',
    customHeaders: 'true',
}
// let proxyUrl = `http://${token}:super=true&waitUntil=domcontentloaded&render=true&&blockResources=false@proxy.scrape.do:8080`;
let proxyUrl = `http://${token}:@proxy.scrape.do:8080`;
let content = '';


const methodType = {
    GET: 'GET',
    POST: 'POST'
};
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

const customHeadersPost = {
    "authority": "rekvizitai.vz.lt",
    "method": methodType.POST,
    "path": "/en/company-search/1/",
    "scheme": "https",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
    "Origin": "https://rekvizitai.vz.lt",
    "Referer": "https://rekvizitai.vz.lt/en/company-search/1/"
}

/**
 *
 * @param string registrationCode
 * @returns {Promise<void>}
 */
const fetchCompanyInfo = async (registrationCode) => {
    const data = JSON.stringify({
        "code": registrationCode,
        "order": "1"
    });
    console.log(`data:::: ${data}`);
    console.log(`headers:::: `, customHeadersPost);

    await request({
        'url': targetUrlPost,
        'method': methodType.POST,
        'proxy': proxyUrl,
        'rejectUnauthorized': false, // ignore self-signed certificate
        'headers': {
            'Content-Type': 'application/json',
            ...customHeadersPost
        },
        body: data
    }, function (error, response, body) {
        if (!error) {
            console.log('BODY:::: ');
            console.log(body);
            content = body;
            const templateDir = path.join(__dirname, '../template');
            const responseFilePath = path.join(templateDir, 'response.html');
            console.log(`content:::: ${content}`);
            writeFile(templateDir, responseFilePath, content);
        } else {
            console.log('ERROR:::: ');
            console.error(error);
        }
    });

    return content;
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
            console.log("Response body saved to response.html");
        }
    });
}

(async () => {
    console.log('STARTS:::: ');
    console.log(`TOKEN:::: ${token}`);
    console.log(`proxyUrl:::: ${proxyUrl}`);
    console.log(`targetUrlPost:::: ${targetUrlPost}`);

    await fetchCompanyInfo('110803767');
    // content = "<html><body><h1>Hello World Again!</h1></body></html>"


})();
