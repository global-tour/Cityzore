class RoomApi {
  async createRoom(bookingId, bookingReferenceCode, guidesId, socketId) {
    const [err, response] = await http.post(`${apiEndpoint}/api/room`, {
      bookingId,
      bookingReferenceCode,
      guidesId,
      socketId
    }).toPromiseArray();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async getRoom(userId) {
    const [err, response] = await http.get(`${apiEndpoint}/api/room/${userId}`).toPromiseArray();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async getAllRoom() {
    const [err, response] = await http.get(`${apiEndpoint}/api/room`).toPromiseArray();

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
    }).toPromiseArray();

    if (!err && response) {
      return response;
    }

    return err;
  }

  async removeMember(roomId, memberId, socketId) {
    const [err, response] = await http.put(`${apiEndpoint}/api/room/removeMember/${roomId}`, {
      memberId,
      socketId
    }).toPromiseArray();

    if (!err && response) {
      return response;
    }
  }

  async updateLastSeen(roomId) {
    const [err, response] = await http.put(`${apiEndpoint}/api/room/updateLastSeen/${roomId}`).toPromiseArray();

    if (!err && response) {
      return response;
    }

    return err;
  }
}
