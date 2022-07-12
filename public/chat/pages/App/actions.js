const initializeApp = () => {
  appLeft = $('#app-left');
  appRight = $('#app-right');

  getChatStatus();
};

// ROOM ACTIONS
const addMember = async (roomId) => {
  const { success, data } = await roomApi.addMember(roomId, user._id, 'Staff', socketService.socketId);
};

const findRoomAndInsideUser = (roomId) => {
  let targetRoom = rooms.find(r => r._id === roomId);
  let controlInside = targetRoom.members.findIndex(memberData => memberData.member?._id === user._id);

  return {
    room: targetRoom,
    inside: controlInside !== -1,
  }
}

const selectRoom = async (roomId) => {
  if(currentRoom?._id !== roomId || !currentRoom) {
    $('#message-creator').addClass('d-none');
    $('#join-room-wrapper').removeClass('d-none');

    let { room, inside } = findRoomAndInsideUser(roomId);

    if(!currentRoom) {
      $('#empty-room').remove();
    } else {
      roomDetailIsOpen = false;
      messageLoading = false;
      messagePagination = { page: 1, limit: 30, total: 0 };

      appRight.empty();
      $(`#room-${currentRoom._id}`).removeClass('active-contact');
    }

    currentRoom = room;
    appRight.prepend(Chat.build());
    $(`#room-${roomId}`).addClass('active-contact');

    if (inside) {
      joinRoom(roomId)
    }
  }
}

const joinRoom = async (roomId) => {
  $('#message-creator').removeClass('d-none');
  $('#join-room-wrapper').addClass('d-none');

  let { inside } = findRoomAndInsideUser(roomId)

  connectionRoom();
  if (!inside) {
    await addMember(roomId).then(() => {
      $('#leave-room').removeClass('d-none');
    });
  }
}

const leaveRoom = (roomId) => {
  let roomEqual = currentRoom._id === roomId;
  let {inside} = findRoomAndInsideUser(roomId);

  if(roomEqual && inside) {
    roomApi.removeMember(roomId, user._id, socketService.socketId).then(() => {
      $('#message-creator').addClass('d-none');
      $('#join-room-wrapper').removeClass('d-none');
      $('#leave-room').addClass('d-none');
    })
  } else {
    alert('This operation cannot be performed.')
  }
}

const connectionRoom = () => {
  socketService.joinRoom(currentRoom._id);
}

// MESSAGE ACTIONS
const sendMessage = async () => {
  if (currentRoom) {
    if (message.trim().length > 0 || attachFile) {
      let body = {
        message: message,
        attachFile: attachFile,
      }
      resetMessageState();

      await messageApi.postMessage(
        currentRoom._id,
        user._id,
        body.message,
        quoteMessage?._id,
        body.attachFile,
        socketService.socketId
      )
    } else {
      toastr.warning('You cannot send a message without writing something.', 'Warning');
    }
  }
};

const resetMessageState = () => {
  attachFile = null;
  message = '';
  removeQuoteMessage();
  messageBox.textContent = '';

  if(typing) {
    messageSubjectEndChange.next('');
  }
}

const removeQuoteMessage = () => {
  $('#chat-quote-message').remove();
  quoteMessage = undefined;
};

const selectFile = () => {
  attachInput.click();
}

const confirmAttachModal = async () => {
  attachInput.value = "";
  if (attachFile) {
    let attachMessageBox = $('#attach-message-box');
    let value = attachMessageBox.text().trim();

    if (value.length > 0) {
      message = attachMessageBox[0].outerText;
    }

    attachMessageBox.empty();
    await sendMessage();
  }
}

const cancelAttachModal = () => {
  attachInput.value = "";
  attachFile = null;
}

const openEmojiKeyboard = () => {
  if (!emojiKeyboardActive) {
    emojiPicker.show();
    emojiKeyboardActive = true;
  } else {
    emojiPicker.hide();
    emojiKeyboardActive = false;
  }
};

// APP ACTIONS
const changeTheme = () => {
  let app = $('html:first');
  let [icon, text] = Array.from($('#more-theme-button').children()).map((i) => $(i));

  if (theme === 'Light') {
    icon.removeClass('fa-moon');
    icon.addClass('fa-sun');
    text.text('Light Mode');

    theme = 'Dark';
    app.addClass("theme-dark");

  } else {
    icon.removeClass('fa-sun');
    icon.addClass('fa-moon');
    text.text('Dark Mode');

    theme = 'Light';
    app.removeClass("theme-dark");
  }
};

const getChatStatus = async () => {
  const {data, success} = await statusApi.getChatStatus();

  if (success) {
    changeChatStatus(data)
  }
}

const updateChatStatus = async () => {
  const { data, success } = await statusApi.updateChatStatus(!chatStatus, socketService.socketId);
}

const changeChatStatus = (status) => {
  let [icon, text] = Array.from($('#more-power-button').children()).map((i) => $(i));
  let emptyStatusButton = $('#empty-chat-status-button');

  if (status) {
    text.text('Power Off');
    emptyStatusButton.text('Power Off');
    emptyStatusButton.removeClass('btn-success');
    emptyStatusButton.addClass('btn-danger');
  } else {
    text.text('Power On');
    emptyStatusButton.text('Power On');
    emptyStatusButton.removeClass('btn-danger');
    emptyStatusButton.addClass('btn-success');
  }

  chatStatus = status;
};
