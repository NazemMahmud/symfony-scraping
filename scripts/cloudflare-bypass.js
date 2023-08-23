const scrapePage = async () => {

    const res = {
        code: ''
    };

    const desiredElementExists= res;
    return desiredElementExists ? res : null;
}


(async () => {

    try {
        const result = await scrapePage();
        console.log(JSON.stringify(result)); // Print the result as JSON
    } catch (error) {
        if (error.message.includes("net::ERR_TIMED_OUT")) {
            console.error("Timed out error. Retrying...");
        } else {
            console.error(error.message);
        }
    }

})();
