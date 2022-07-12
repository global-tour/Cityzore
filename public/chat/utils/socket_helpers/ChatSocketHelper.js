class ChatSocketHelper extends SocketHelper {
  constructor() {
    super();
  }

  listenRoomMessage = async (value) => {
    // console.log('listenRoomMessage', value);

    messagesWrapper.append(MessageBubble(value.message));
    scrollToForceBottom(messagesWrapper);
  }

  roomAddMember = async (value) => {
    // console.log('roomAddMember', value);

    roomDetailMembers.append(MemberItem(value.data));
    messagesWrapper.append(MessageBubble(value.message));
    scrollToForceBottom(messagesWrapper);
  }

  roomRemoveMember = async (value) => {
    // console.log('roomRemoveMember', value);

    $(`#room-detail-member-${value.data.member._id}`).remove();
    messagesWrapper.append(MessageBubble(value.message));
    scrollToForceBottom(messagesWrapper);
  }

  listenRoomIsTyping = async (value) => {
    // console.log('listenRoomIsTyping', value);

    let targetDiv = $('#chat-status');
    let fullName = `${value.member.name} ${value.member.surname}`;

    value.data
      ? targetDiv.text(`${fullName}: Is typing...`)
      : targetDiv.text('');
  }
}
