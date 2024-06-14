var HTTP = {
    post: function(endpoint, parameters, loading = false, callback, fallback) {
        let url = baseUrl + 'services/' + endpoint;

        $.ajax({
            method: "POST",
            url: url,
            data: parameters,
            dataType: "text",
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(error) {
                console.error('Erro na requisição AJAX:', error);
                if (typeof fallback === 'function') {
                    fallback(error);
                } else {
                    showError('Erro na requisição AJAX'); 
                }
            },
            complete: function() {
                // Lógica de finalização, se necessário
            }
        });
    },

    handleErrors: function (error) {
        // Implemente a lógica para lidar com erros específicos, se necessário
        return false;
    }
};
