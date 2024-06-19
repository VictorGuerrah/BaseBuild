var View = {
    index: function () {
        View.checkAuth(function (isAuthenticated) {
            if (isAuthenticated) {
                View.render('auth/view-dashboard', '.container', function () { });
            } else {
                View.render('auth/view-login', '.container', function () { });
            }
        });
    },

    render: function (path, insertObject, callback, loading = false) {
        let parameters = [];

        HTTP.post(path, parameters, loading, function (responseText) {
            $(insertObject).html(responseText);
            if (typeof callback === 'function') {
                callback();
            }
        }, function (error) {
            if (!HTTP.handleErrors(error)) {
                showError(error);
            }
        });
    },

    checkAuth: function (callback) {
        HTTP.post('auth/check-authentication', {}, false, function (responseText) {
            try {
                let response = JSON.parse(responseText);
                if (response.data.isAuthenticated === true) {
                    callback(true);
                } else {
                    callback(false);
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
    }
};
