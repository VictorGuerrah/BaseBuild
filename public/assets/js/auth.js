var Auth = {
    validateCredentials: function () {
        let parameters = $('#login-form').serializeArray();
        HTTP.post('auth/validate-credentials', parameters, false, function (responseText) {
            try {
                let response = JSON.parse(responseText).data;
                if (response.isValidated === true) {
                    View.render('auth/view-dashboard', '.container', function () {
                    });
                } else {
                    alert('Access denied!')
                }
            } catch (error) {
                if (!HTTP.handleErrors(error)) {
                    showError(error);
                }
                callback(false);
            }
        }, function (error) {
            if (!HTTP.handleErrors(error)) {
                showError(error);
            }
            callback(false);
        });
    },

    logout: function () {
        HTTP.post('auth/logout', null, false, function () {
            View.render('auth/view-login', '.container', function () { });
        }, function (error) {
            if (!HTTP.handleErrors(error)) {
                showError(error);
            }
            callback(false);
        });
    },


}