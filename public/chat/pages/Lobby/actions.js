const initializeLobby = async () => {
  Notification.requestPermission().then( (result) => {
    console.log('Notification: ', result);
  });

  roomsWrapper = $('#contact-list');
  searchInput = document.getElementById('search-room-input')

  // Search
  listenSearchInput();
  listenSearchAction();

  // Lobby
  listenToLobby();
  listenLobbyList();

  // Room
  listenToRoom();

  await getRooms();
  twemoji.parse(appLeft[0]);
};

// DATA UPDATES
const getRooms = async () => {
  roomLoading = true;

  let {success, data, extra} = await roomApi.getRooms({
    page: roomPagination.page,
    limit: roomPagination.limit,
    ids: excludeRooms,
  });

  if (success) {
    rooms = [...rooms, ...data];

    await data.forEach((room) => {
      roomsWrapper.append(LobbyItem(room._id));
    });

    roomPagination = { ...extra.pagination, page: roomPagination.page + 1 };
  }

  roomLoading = false;
};

const searchRoom = async () => {
  roomLoading = true;

  let search = `${searchInput.value}`.trim();
  let {success, data, extra} = await roomApi.getRooms({
    name: search,
    page: roomPagination.page,
    limit: roomPagination.limit,
  });

  if (success) {
    rooms = [...rooms, ...data];

    await data.forEach((room) => {
      roomsWrapper.append(LobbyItem(room._id));
    });

    roomPagination = { ...extra.pagination, page: roomPagination.page + 1 };
  }

  roomLoading = false;
}

const getRoomAndAddLobby = async (roomId) => {
  let {success, data} = await roomApi.getRoom(roomId);

  if (success) {
    excludeRooms.push(roomId);
    rooms.unshift(data);
    roomsWrapper.prepend(LobbyItem(roomId));
  }
}

const calculateUnreadMessageCount = (roomId) => {
  let targetRoom = rooms.find((room) => room._id === roomId);
  let activityRoom = activities.map((x) => x.room);
  let count = 0;

  if(targetRoom) {
    if(!activityRoom.includes(roomId)) {
      targetRoom.messages.forEach((message) => {
        if (!targetRoom.staffLastSeen || moment(targetRoom.staffLastSeen).diff(message.createdAt) < 0) {
          count++;
        }
      })
    }
  }

  return count;
};

const getLastMessage = (roomId) => {
  let targetRoom = rooms.find((room) => room._id === roomId);

  if(targetRoom) {
    if (targetRoom?.messages[0]) {
      let ownerName;
      let lastMessage = targetRoom.messages[0];
      if (lastMessage.messageType !== 'System') {
        let findOwner = targetRoom.members.find((memberData) => memberData.member?._id === lastMessage?.ownerId)?.member;
        ownerName = findOwner ? `${findOwner.name} ${findOwner.surname}` : 'Old User';
      } else {
        ownerName = 'System';
      }

      let current = moment().toISOString()
      let messageDate = moment(lastMessage.createdAt);
      let time = calculateTimeOrDateTime(current, messageDate);

      return {
        message: `${ownerName}: ${lastMessage.message}`,
        messageType: lastMessage.messageType,
        time: time,
      }
    }
  }

  return '';
};

// UI UPDATES
const listenToLobby = () => {
  const lobbySocketHelper = new LobbySocketHelper();

  socketService.joinLobby();
  socketService.listen('listenLobbyMessage', lobbySocketHelper.listenLobbyMessage);
  socketService.listen('lobbyCreateRoom', lobbySocketHelper.lobbyCreateRoom);
  socketService.listen('lobbyRemoveRoom', lobbySocketHelper.lobbyRemoveRoom);
  socketService.listen('lobbyAddMember', lobbySocketHelper.lobbyAddMember);
  socketService.listen('lobbyRemoveMember', lobbySocketHelper.lobbyRemoveMember);
  socketService.listen('listenLobbyIsTyping', lobbySocketHelper.listenLobbyIsTyping);
}

const updateTyping = (roomId, user, isWriting) => {
  let targetDiv = $('#last-message-'+roomId);
  let fullName = `${user.name} ${user.surname}`;

  isWriting
    ? targetDiv.text(`${fullName}: Is typing...`)
    : updateLastMessage(roomId);
};

const updateLastMessage = (roomId) => {
  let targetLastMessageTime = $('#last-message-time-'+roomId);
  let targetLastMessage = $('#last-message-'+roomId);
  let lastMessageDetail = getLastMessage(roomId);

  targetLastMessage.html(getLastMessageItem(lastMessageDetail));
  targetLastMessageTime.text(lastMessageDetail.time);
};

const markAsRead = async (roomId) => {
  event.preventDefault();
  event.stopPropagation();

  $('#contact-dropdown').click()
  const { success, data } = await roomApi.updateLastSeen(roomId, socketService.socketId);
}

const updateUnreadMessageCount = (roomId) => {
  let targetDiv = $('#unread-message-'+roomId);
  let count = calculateUnreadMessageCount(roomId);

  if(count > 0) {
    targetDiv.removeClass('d-none');
    targetDiv.text(count);
  } else {
    targetDiv.addClass('d-none');
  }
};

const updateQueue = (roomId) => {
  let targetDiv = $('#room-'+roomId);
  let cloneDiv = targetDiv.clone(true);
  roomsWrapper.prepend(cloneDiv);

  targetDiv.remove();
};

const selectActivityTab = (role) => {
  if(role === 'Customer') {
    $('#model-tab-customer').addClass('active')
    $('#model-tab-staff').removeClass('active');
    $('#body-customer').removeClass('d-none');
    $('#body-staff').addClass('d-none');
  } else {
    $('#model-tab-staff').addClass('active')
    $('#model-tab-customer').removeClass('active');
    $('#body-staff').removeClass('d-none');
    $('#body-customer').addClass('d-none');
  }
};

const getLastMessageItem = (message) => {
  switch (message.messageType) {
    case 'System':
    case 'Text':
      return (`
        <span>${message.message}</span>
      `)

    case 'Photo':
      return (`
        <span>
            <span class="fa fa-image"></span>
            Image
        </span>
      `);

    case 'Video':
      return (`
        <span>
            <span class="fa fa-video"></span>
            Video
        </span>
      `)

    case 'File':
      return (`
        <span>
            <span class="fa fa-file"></span>
            File
        </span>
      `);

    default:
      return (`
        <span>${message.message}</span>
      `)

  }
}
