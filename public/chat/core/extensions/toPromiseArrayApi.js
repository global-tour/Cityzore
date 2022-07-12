Promise.prototype.toPromiseArrayApi = function () {
  return this.then((responseData) => {
    return [null, responseData]
  }).catch((errorData) => {
    return [errorData, undefined]
  });
};
