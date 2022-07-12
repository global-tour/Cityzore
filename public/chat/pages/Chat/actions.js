const initializeChat = () => {
  messagesWrapper = $('#chat');

  chatBottomWrapper = $('#message-wrap');
  roomDetailWrapper = $('#room-detail-wrap');
  roomDetailMembers = $('#room-detail-members');

  attachDropWrapper = $('#drop-wrap');
  attachInput = document.getElementById('attach-file');
  attachModal = $('#attach-modal');

  emojiPicker = $('#emojis');
  emojiPicker.disMojiPicker();
  emojiPicker.hide();

  writeMembers(currentRoom?.members);
  writeMessage(currentRoom?.messages);
  scrollToForceBottom(messagesWrapper);

  twemoji.parse(appRight[0]);
  initializeMessageArea();
};

const initializeMessageArea = () => {
  // Messages
  listenMessageList();
  listenMessageBox();
  subscriptionMessageBox();
  listenBubbleDoubleClick();

  // Attachs
  listenAttachFile();
  listenAttachDrop();
  listenAttachMessageBox();

  emojiPicker.picker((emoji) => {
    messageBox.textContent += emoji;
    messageBox.dispatchEvent(messageBoxEvent);
  });
}

// DATA UPDATES
const getMessagesByRoom = async () => {
  messageLoading = true;

  const { data, success, extra } = await messageApi.getMessages(currentRoom?._id, {
    page: messagePagination.page,
    limit: messagePagination.limit,
  });

  if (success) {
    currentRoom.messages = [...currentRoom.messages, ...data]
    writeMessage(data);
    messagePagination = { ...extra.pagination, page: messagePagination.page + 1 }
  }

  messageLoading = false;
}

// UI UPDATES
const listenToRoom = () => {
  const chatSocketHelper = new ChatSocketHelper();

  socketService.listen('listenRoomMessage', chatSocketHelper.listenRoomMessage);
  socketService.listen('roomAddMember', chatSocketHelper.roomAddMember);
  socketService.listen('roomRemoveMember', chatSocketHelper.roomRemoveMember);
  socketService.listen('listenRoomIsTyping', chatSocketHelper.listenRoomIsTyping);
};

const writeMessage = (messages) => {
  messages.forEach((message, index) => {
    let dateDifference = findDateDifferences(message?.createdAt, messages[index + 1]?.createdAt);
    messagesWrapper.prepend(MessageBubble(message));

    if (dateDifference) {
      messagesWrapper.prepend(MessageDateBubble(dateDifference))
    }
  });
};

const writeMembers = (members) => {
  members.forEach((memberData) => {
    roomDetailMembers.append(MemberItem(memberData));
  })
}

const openRoomDetail = () => {
  let icon = $('#room-detail-icon');

  if(!roomDetailIsOpen) {
    icon.removeClass('fa-bars');
    icon.addClass('fa-times');
    roomDetailWrapper.removeClass('d-none');
    roomDetailIsOpen = true;
  } else {
    icon.removeClass('fa-times');
    icon.addClass('fa-bars');
    roomDetailWrapper.addClass('d-none');
    roomDetailIsOpen = false;
  }
}
