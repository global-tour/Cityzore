class SocketHelper {
  constructor() {}

  roomIndex(roomId) {
    return rooms.findIndex(room => room._id === roomId);
  }
}
