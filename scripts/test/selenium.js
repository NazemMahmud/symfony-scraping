// const { WebDriver, Builder, Browser, By, Key, until } = require('selenium-webdriver');
// // const driver = new WebDriver();
//
// (async () => {
//     let driver = await new Builder().forBrowser(Browser.CHROME).build();
//     try {
//         await driver.get('https://en.wikipedia.org/wiki/Selenium');
//         const element = await driver.findElement(By.css('#firstHeading'));
//         const html = await element.innerHTML;
//         console.log('HTML: ');
//         console.log(html);
//     } catch (error) {
//         console.error(error);
//     } finally {
//         await driver.quit();
//     }

// })();


const { Builder, By, Browser } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');

(async () => {
    const driver = await new Builder()
        .forBrowser(Browser.CHROME)
        .setChromeOptions(new chrome.Options().addArguments('--headless')) // Optional: Run in headless mode
        .setChromeService(new chrome.ServiceBuilder(process.env.PUPPETEER_EXECUTABLE_PATH)) // Specify the path to chromedriver
        .build();
    try {
        await driver.get('https://en.wikipedia.org/wiki/Selenium');
        const element = await driver.findElement(By.css('#firstHeading'));
        const html = await element.getAttribute('innerHTML');
        console.log('HTML: ');
        console.log(html);
    } catch (error) {
        console.error(error);
    } finally {
        await driver.quit();
    }
})();
