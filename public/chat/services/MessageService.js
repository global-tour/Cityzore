class MessageService {
  isTyping(room, isTyping) {
    socketService.emit('isTyping', { room, data: isTyping });
  }

  sendToLobby(room, message) {
    socketService.emit('sendToLobby', { room, data: message });
  }

  sendToRoom(room, message) {
    socketService.emit('sendToRoom', { room, data: message });
  }

  sendAll(room, message) {
    this.sendToLobby(room, message);
    this.sendToRoom(room, message);
  }
}
