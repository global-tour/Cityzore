class StatusApi {
  async getChatStatus() {
    const [err, response] = await http.get(`${apiEndpoint}/api/status/chat`).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async updateChatStatus(status, socketId) {
    const [err, response] = await http.put(`${apiEndpoint}/api/status/chat`, {status, socketId}).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

}
