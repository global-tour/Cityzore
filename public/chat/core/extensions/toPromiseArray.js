Promise.prototype.toPromiseArray = function () {
  return this.then((data) => {
    return [null, data];
  }).catch((err) => {
    return [err, undefined];
  });
};
