const MessageBubble = (message) => {
  let isMe;
  let member;
  let fullName;
  let time;
  let parseMessage;

  let isHasQuote = message.hasOwnProperty('quoteMessage');
  let quoteMessage;

  if (message.messageType === 'System') {
    fullName = `System:`;
  } else {
    isMe = message.hasOwnProperty('ownerId') ? message?.ownerId === user._id : false;
    member = currentRoom.members.find((memberData) => memberData.member?._id === message?.ownerId)?.member;
    fullName = member ? `${member?.name} ${member?.surname}` : 'Old User';
  }

  time = moment(message.createdAt).format('HH:mm');
  parseMessage = twemoji.parse(message.message);

  if(isHasQuote) {
    quoteMessage = message.quoteMessage;
  }

  if (message.messageType === 'System') {
    return `
       <div id="${message._id}" class="chat-bubble system">
        <div class="content">
              <span>${parseMessage}</span>
        </div>
      </div>
    `;
  } else {
    return isMe
      ? `
      <div id="${message._id}" class="chat-bubble me">
        ${isHasQuote ? QuoteMessage(quoteMessage) : '<span></span>'}
        <div class="my-mouth"></div>
        <div class="content">
            ${renderMessageContent(message)}
            <span class="message-text">${parseMessage}</span>
        </div>
        <div class="time">${time}</div>
      </div>
    `
      : `
      <div id="${message._id}" class="chat-bubble you">
        <div class="your-mouth"></div>
        <h6>${fullName}</h6>
        ${isHasQuote ? QuoteMessage(quoteMessage) : '<span></span>'}
        <div class="content">
              ${renderMessageContent(message)}
              <span class="message-text">${parseMessage}</span>
        </div>
        <div class="time">${time}</div>
      </div>
   `;
  }
};

const renderMessageContent = (message) => {
  switch (message.messageType) {
    case 'Photo':
      return `<div class="message-attach d-flex justify-content-center">
            <img class="message-image" src="${message.attach.url}" />
        </div>`

    case 'Video':
      return `<div class="message-attach d-flex justify-content-center">
        <video class="message-video" controls>
          <source src="${message.attach.url}" type="video/mp4">
        </video>
      </div>`

    case 'File':
      return `<div class="message-attach d-flex justify-content-center">
        <div class="message-file">
            <div class="message-file-left"><i class="fa fa-file"></i></div>
            <div class="message-file-center">${message.attach.name}</div>
            <a href="${message.attach.url}" target="_blank" class="message-file-right">
              <i class="fa fa-download"></i>
            </a>
        </div>
      </div>`

    default:
      return `<span></span>`
  }
}
