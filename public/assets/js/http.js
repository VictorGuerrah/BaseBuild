var HTTP = {
    post: function (endpoint, parameters, loading = false, callback, fallback) {
        let url = baseUrl + 'api/' + endpoint;

        $.ajax({
            method: "POST",
            url: url,
            data: parameters,
            dataType: "text",
            success: function (response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function (error) {
                console.error('Erro na requisição AJAX:', error);
                if (typeof fallback === 'function') {
                    fallback(error);
                } else {
                    showError('Erro na requisição AJAX');
                }
            },
        });
    },

    handleErrors: function (error) {
        let response;
        try {
            response = JSON.parse(error.responseText);
        } catch (e) {
            console.log(error);
            return false;
        }

        if (error.status === 401 && response && ["Invalid token", "Invalid session."].includes(response.message)) {
            View.render('auth/view-login', '.container', function () { });
            return true;
        }

        if (error.status === 500) {
            if (response || response.message) {
                alert(response.message)
            }
            return true;
        }
    }
};
