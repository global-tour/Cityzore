const { interceptors } = http._getHttpClient();

interceptors.request.use(
  async (config) => {
    if (user) {
      config.headers = {
        'x-access-token': `${user?.token}`
      };
    }

    return config;
  },
  (errorStack) => {
    Promise.reject(errorStack);
  }
);

interceptors.response.use(
  (response) => {
    return response.data;
  },
  (errorStack) => {
    let response = errorStack?.response;
    let errorMessage = response ? response.data.error.message : 'Server not response!';
    let errorData = response
      ? response.data.error
      : {
          success: false,
          statusCode: 400,
          error: {
            message: errorMessage,
            key: 'server_not_response',
            detail: null,
          },
          data: null,
          extra: null
      }

    toastr.error('Error', errorMessage);
    return Promise.reject(errorData);
  }
);
