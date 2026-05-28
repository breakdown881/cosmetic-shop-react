const getHttpClient = () => {
    if (!window.axios) {
        throw new Error('Axios is not available. Make sure resources/js/bootstrap.js is imported first.');
    }

    return window.axios;
};

export const get = async (url, config = {}) => {
    const response = await getHttpClient().get(url, config);

    return response.data;
};

export const post = async (url, data = {}, config = {}) => {
    const response = await getHttpClient().post(url, data, config);

    return response.data;
};

export const patch = async (url, data = {}, config = {}) => {
    const response = await getHttpClient().patch(url, data, config);

    return response.data;
};

export const destroy = async (url, config = {}) => {
    const response = await getHttpClient().delete(url, config);

    return response.data;
};
