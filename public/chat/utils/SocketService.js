class SocketService {
  socket;
  currentRoom = null;
  isConnect = false;
  defaultConfig = {
    // "force new connection": true,
    // "reconnectionAttempts": "Infinity",
    "timeout": 10000,
    "transports": ["websocket"]
  }

  get socketId() {
    return this.socket.id;
  }

  stopSocket() {
    if (this.isConnect) {
      this.socket.close();
      console.log('Socket disconnect');
    }
  }

  startSocket(queryConfig) {
    this.socket = io.connect(chatSocket, {...this.defaultConfig, query: queryConfig})
    this.socket.on('connect', () => {
      console.log('Socket connected!');
    });

    this.socket.on('reconnect', () => {
      console.log('Socket reconnected!');
    });

    this.isConnect = true;
    return this.socket;
  }

  listen(key, callback) {
    this.socket.on(key, (data) => callback(data));
  }

  emit(key, data) {
    try {
      this.socket.emit(key, data);
    } catch (err) {
      console.log(err);
    }
  }

  joinLobby(room) {
    try {
      this.socket.emit('joinLobby', {room});
    } catch (err) {
      console.log(err);
    }
  }

  joinRoom(room) {
    try {
      if(this.currentRoom) {
        this.leaveRoom(this.currentRoom);
      }

      this.socket.emit('joinRoom', {room});
      this.currentRoom = room;
    } catch (err) {
      console.log(err);
    }
  }

  leaveRoom(room) {
    try {
      this.socket.emit('leaveRoom', {room});
      this.currentRoom = null;
    } catch (err) {
      console.log(err);
    }
  }
}
