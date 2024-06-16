var Auth = {
    validateCredentials: function () {
        let parameters = $('#login-form').serializeArray();
        HTTP.post('auth/validate-credentials', parameters, false, function(responseText) {
            try {
                let response = JSON.parse(responseText);
                console.log(response);
            } catch (e) {
                console.error('Something went wrong:', e);
                callback(false);
            }
        }, function(error) {
            console.error('Something went wrong:', error);
            callback(false);
        });
    }
}