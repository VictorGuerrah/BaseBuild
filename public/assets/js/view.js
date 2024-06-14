var View = {
    index: function() {
        View.checkAuth(function(isAuthenticated) {
            if (isAuthenticated) {
                View.load('dashboard/index', '.container', function() {
                    // Callback vazio, você pode adicionar lógica aqui se necessário
                });
            } else {
                View.load('auth/login', 'body', function() {
                    // Callback vazio, você pode adicionar lógica aqui se necessário
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
            console.error('Erro na requisição AJAX:', error);
        });
    },

    checkAuth: function(callback) {
        HTTP.post('auth/checkAuthentication', {}, false, function(responseText) {
            try {
                let responseObj = JSON.parse(responseText);
                if (responseObj.isAuthenticated !== undefined) {
                    callback(responseObj.isAuthenticated);
                } else {
                    console.error('Something went wrong.')
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
