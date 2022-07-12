const LobbyItem = (roomId) => {
  let lastMessageDetail = getLastMessage(roomId);
  let unreadMessageCount = calculateUnreadMessageCount(roomId);

  let room = rooms.find((room) => room._id === roomId);
  let member = room.members.find((memberData) => memberData.member?._id === roomId)?.member;

  let fullName = member ? `${member.name} ${member.surname}` : 'Old User';
  let isActive = roomId === currentRoom?._id;

  let [time, date] = lastMessageDetail.time.split(' ');

  let classNameContact = isActive
    ? 'contact active-contact'
    : 'contact';

  let classNameUnreadMessage = unreadMessageCount > 0
    ? 'badge bg-primary contact-badge'
    : 'badge bg-primary contact-badge d-none';

  return (
    `
     <div id="room-${roomId}" class="${classNameContact}" onclick="selectRoom(` + `'${roomId}'` + `)">
        <img src="https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/Customer.png" class="contact-img">
        <div class="contact-preview">
          <div class="contact-text">
            <span class="title-text one-line-text">${fullName}</span>
            <p id="last-message-${roomId}" class="body-text one-line-text">
                ${getLastMessageItem(lastMessageDetail)}
            </p>
          </div>
        </div>
        <div class="contact-right">
          <div id="last-message-time-${roomId}" class="contact-time">
            <p class="time-text">${date ? date : time}</p>
          </div>
          
          <div class="d-flex align-items-center">
            <div class="me-1">
                <span id="unread-message-${roomId}" class="${classNameUnreadMessage}">${unreadMessageCount}</span>
            </div>
            
            <div class="me-1 contact-action">
                <div class="dropdown">
                  <button 
                    onclick="event.stopPropagation();"
                    type="button" 
                    id="contact-dropdown" 
                    class="btn badge bg-secondary contact-badge" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                    <span class="fas fa-caret-down fa-sm" />
                  </button>

                  <ul id="contact-dropdown-menu-${roomId}" class="dropdown-menu" aria-labelledby="contact-dropdown">
                    <li><button onclick="markAsRead(` + `'${roomId}'` + `)" class="dropdown-item" type="button">Mark as read</button></li>
                  </ul>
                </div>
            </div>
          </div>
        </div>
     </div>
      `
  );
};
