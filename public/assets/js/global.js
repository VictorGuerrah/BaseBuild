function showError(error) {
    let message = '';

    try {
        const parsedError = JSON.parse(error.responseText);
        
        if (parsedError.statusCode === 400 && parsedError.data && typeof parsedError.data === "object" && parsedError.data.hasOwnProperty('errors')) {
            message = `${parsedError.message}\n\n`;
            message += '<ul>';

            parsedError.data.errors.forEach(err => {
                message += `${err}\n`;
            });

            message += '\n';
        } else {
            message = `${parsedError.message}`;
        }
    } catch (e) {
        if (error.responseText) {
            message = `${error.responseText}`;
        } else {
            message = `Error not known.`;
        }
    }
    console.log(message);
}