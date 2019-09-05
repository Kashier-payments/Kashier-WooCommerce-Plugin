class Kashier {
    constructor(baseUrl) {
        this._baseUrl = baseUrl;
    }

    _sendRequest(method, options, data = null) {
        let request = {
            method: method,
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        };

        if (data) {
            request.body = JSON.stringify(data)
        }

        return fetch(this._baseUrl + options.requestPath, request)
            .then((response) => {
                if ((response.status >= 200 && response.status < 300)) {
                    return Promise.resolve(response.json())
                } else {
                    return Promise.reject(new Error(response.statusText))
                }
            })
            .then((response) => {
                if ("".concat(response.body.status).toUpperCase() !== "SUCCESS") {
                    return Promise.reject(new Error(response.error.explanation))
                }

                return Promise.resolve(response);
            });
    }

    checkout(data, options = {}) {
        let _options = Object.assign({
            requestPath: '/checkout'
        }, options);

        return this._sendRequest('post', _options, data);
    }

    tokenize(data, options = {}) {
        let _options = Object.assign({
            requestPath: '/tokenization'
        }, options);

        return this._sendRequest('post', _options, data);
    }
}

// var global = window || global;
// global.Kashier = new Kashier();