class LobbySocketHelper extends SocketHelper {
  constructor() {
    super();
  }

  listenLobbyMessage = async (value) => {
    // console.log('listenLobbyMessage', value);
    if (!["Staff", "Guide"].includes(value?.member?.role)) {
      let options = {
        body: value.message.message,
        requireInteraction: true,
        image: "https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/Eiffel-Reactangle.jpg"
      };


      let n = new Notification('You Have New Chat Message!', options);
      n.onclick = function (x) {
        window.focus();
        this.close();
      };
    }

    let roomIndex = this.roomIndex(value.room);

    if (roomIndex !== -1) {
      rooms[roomIndex].messages.unshift(value.message);
      rooms[roomIndex].lastMessage = value.message.createdAt;

      await updateQueue(value.room);
      await updateLastMessage(value.room);
      await updateUnreadMessageCount(value.room);
    } else {
      await getRoomAndAddLobby(value.room);
    }
  }

  lobbyCreateRoom = async (value) => {
    // console.log('lobbyCreateRoom', value)

    excludeRooms.push(value.room);
    rooms.unshift(value?.data);
    roomsWrapper.prepend(LobbyItem(value.room))
  }

  lobbyRemoveRoom = async (value) => {
    // console.log('lobbyRemoveRoom', value)
    let roomIndex = this.roomIndex(value.room);

    if (roomIndex !== -1) {
      excludeRooms = excludeRooms.filter((room) => room._id === value.room)
      rooms = rooms.filter((room) => room._id === value.room);
      $("#rooms" + value.room).remove();
    }
  }

  lobbyAddMember = async (value) => {
    // console.log('lobbyAddMember', value)
    let roomIndex = this.roomIndex(value.room);

    if (roomIndex !== -1) {
      rooms[roomIndex].members.push(value.data);
      rooms[roomIndex].messages.unshift(value.message);
      await updateLastMessage(value.room);
      await updateUnreadMessageCount(value.room);
    } else {
      await getRoomAndAddLobby(value.room);
    }
  }

  lobbyRemoveMember = async (value) => {
    // console.log('lobbyRemoveMember', value)
    let roomIndex = this.roomIndex(value.room);

    if (roomIndex !== -1) {
      rooms[roomIndex].members = rooms[roomIndex].members.filter((memberData) => memberData.member._id !== value.data._id);
      rooms[roomIndex].messages.unshift(value.message);
      await updateLastMessage(value.room);
      await updateUnreadMessageCount(value.room);
    } else {
      await getRoomAndAddLobby(value.room);
    }
  }

  listenLobbyIsTyping = async (value) => {
    // console.log('listenLobbyIsTyping', value)
    let roomIndex = this.roomIndex(value.room);

    if (roomIndex !== -1) {
      await updateTyping(value.room, value.member, value.data);
    }
  }
}
