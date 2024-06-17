var View = {
    index: function() {
        View.checkAuth(function(isAuthenticated) {
            if (isAuthenticated) {
                View.load('dashboard/index', '.container', function() {
                });
            } else {
                View.load('auth/view-login', 'body', function() {
                });
            }
        });
    },

    load: function(path, insertObject, callback, loading = false) {
        let parameters = [];

        HTTP.post(path, parameters, loading, function(responseText) {
            $(insertObject).html(responseText);
            if (typeof callback === 'function') {
                callback();
            }
        }, function(error) {
            console.error('Something went wrong:', error);
        });
    },

    checkAuth: function(callback) {
        HTTP.post('auth/check-authentication', {}, false, function(responseText) {
            try {
                let response = JSON.parse(responseText);
                if (response.isAuthenticated !== undefined) {
                    callback(response.isAuthenticated);
                } else {
                    callback(false);
                }
            } catch (e) {
                console.error('Something went wrong:', e);
                callback(false);
            }
        }, function(error) {
            console.error('Something went wrong:', error);
            callback(false);
        });
    }
};
