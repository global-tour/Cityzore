class MessageApi {
  async postMessage(roomId, ownerId, message, quoteMessage, file, socketId) {
    let data = file ? new FormData() : {};

    if (file) {
      data.append('file', file);
      data.append('roomId', roomId);
      data.append('ownerId', ownerId);
      data.append('message', message);
      if (quoteMessage) {
        data.append('quoteMessage', quoteMessage);
      }
      data.append('socketId', socketId);
    }
    else {
      data.roomId = roomId;
      data.ownerId = ownerId;
      data.message = message;
      data.quoteMessage = quoteMessage;
      data.socketId = socketId;
    }

    const [err, response] = await http.post(`${apiEndpoint}/api/message`, data, {
      'Content-Type': 'application/json'
    }).toPromiseArray();

    if (!err && response) {
      return response;
    }

    return err;
  }
}
