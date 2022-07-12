const ChatHeader = () => {
  let isCustomer = user.role === 'Customer';
  let member;
  let fullName;

  if (!isCustomer) {
    member = currentRoom.members.find((memberData) => memberData.member._id === currentRoom._id).member;
    fullName = `${member.name} ${member.surname}`;
  }
  else {
    fullName = 'Global Tours And Tickets';
  }

  return (
    `
    <div class="chat-head">
      <div class="row m-0">
        <div class="col-8 p-0">
            <div class="chat-head-detail">
              <img src="https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/Customer.png">
                <div class="chat-head-preview">
                  <div class="chat-head-text">
                    <span class="title-text">${fullName}</span>
                    <p id="chat-status" class="body-text"></p>
                  </div>
              </div>
            </div>
        </div>
        <div class="col-4 p-0 d-flex justify-content-end">
          <span class="icon ${isCustomer ? 'd-flex' : 'd-none'}" onclick="changeTheme()">
            <i id="theme-icon" class="fa fa-moon fa-lg text-icon" aria-hidden="true"></i>
          </span>
        
          <span class="icon d-flex">
            <i class="fa fa-search fa-lg text-icon" aria-hidden="true"></i>
          </span>
    
          <span class="icon d-flex" onclick="openRoomDetail()">
            <i id="room-detail-icon" class="fa fa-bars fa-lg text-icon" aria-hidden="true"></i>
          </span>
        </div>
      </div>
    </div>
    `
  );
};
