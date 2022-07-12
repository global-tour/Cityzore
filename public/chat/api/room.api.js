class RoomApi {
  async createRoom(bookingId, bookingReferenceCode, guidesId, socketId) {
    const [err, response] = await http.post(`${apiEndpoint}/api/room`, {
      bookingId,
      bookingReferenceCode,
      guidesId,
      socketId
    }).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async getRoom(roomId) {
    const [err, response] = await http.get(`${apiEndpoint}/api/room/${roomId}`).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async getRooms(params) {
    const [err, response] = await http.get(`${apiEndpoint}/api/room`, params).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async addMember(roomId, memberId, memberRole = 'Staff', socketId) {
    const [err, response] = await http.put(`${apiEndpoint}/api/room/addMember/${roomId}`, {
      memberId,
      memberRole,
      socketId
    }).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async removeMember(roomId, memberId, socketId) {
    const [err, response] = await http.put(`${apiEndpoint}/api/room/removeMember/${roomId}`, {
      memberId,
      socketId
    }).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async updateLastSeen(roomId, socketId) {
    const [err, response] = await http.put(
      `${apiEndpoint}/api/room/updateLastSeen/${roomId}`,
      { socketId }
    ).toPromiseArrayApi();

    if (!err && response) {
      return response;
    }

    return err;
  }
}
