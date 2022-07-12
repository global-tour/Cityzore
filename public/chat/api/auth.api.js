class AuthApi {
  async loginCustomer(name, surname, meetingId, bookingId, bookingReferenceCode) {
    const [err, response] = await http.post(`${apiEndpoint}/authentication/login/customer`, {
      name,
      surname,
      meetingId,
      bookingId,
      bookingReferenceCode
    }).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async loginStaff(email, password) {
    const [err, response] = await http.post(`${apiEndpoint}/authentication/login/staff`, {
      email,
      password
    }).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }
}
