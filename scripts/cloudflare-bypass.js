const scrapePage = async (code) => {

    return {
        companyName: 'ABCD Company "title VŠĮ"',
        code: code,
        vat:  '987654321',
        address: 'Miško g. 25 Vilnius ',
        mobilePhone: 'https://rekvizitai.vz.lt/timages/%3DHGZ1VQAmVQV1NPZ3ZmX.gif',
    };
}


(async () => {

    const registrationCode = process.argv[2];
    try {
        const result = await scrapePage(registrationCode);
        console.log(JSON.stringify(result)); // Print the result as JSON
    } catch (error) {
        if (error.message.includes("net::ERR_TIMED_OUT")) {
            console.error("Timed out error. Retrying...");
        } else {
            console.error(error.message);
        }
    }

})();
