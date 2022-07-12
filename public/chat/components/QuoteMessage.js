const QuoteMessage = (message) => {
  let targetMember = currentRoom.members.find((memberData) => memberData.member?._id === message.ownerId)?.member;
  let fullName = targetMember ? `${targetMember.name} ${targetMember.surname}` : 'Old User';
  let parseMessage = twemoji.parse(message.message);

  let content = message.message
    ? parseMessage
    : message.attach
      ? message.attach.name
      : '';

  return (
    `
      <div class="quote-message d-flex flex-grow-1">
         <div class="quote-message-left"></div>
         <div class="quote-message-right-row flex-grow-1">         
            <div class="quote-message-right-column flex-grow-1">
              <h6 class="primary-text">${fullName}</h6>
              <div class="content"><span class="message-text">${content}</span></div>
            </div>
            
            ${renderQuoteContent(message)}
         </div>
      </div>
    `
  );
};

const renderQuoteContent = (message) => {
  switch (message.messageType) {
    case 'Photo':
      return `<div class="quote-message-attach"><img class="quote-message-image" src="${message.attach.url}" /></div>`;

    case 'Video':
      return `<i class="fa fa-video"></i>`;

    case 'File':
      return `<i class="fa fa-file"></i>`;

    default:
      return `<span></span>`;
  }
}
