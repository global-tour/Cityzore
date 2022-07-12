const RoomDetail = () => {
  let roomOwner;
  let { inside, room } = findRoomAndInsideUser(currentRoom?._id)

  if (user.role === 'Staff') {
    roomOwner = currentRoom.members.find((memberData) => memberData.member._id === currentRoom._id).member;
  } else {
    roomOwner = user;
  }

  return (
    `
    <div class="room-detail">
        <div class="row m-0 mb-3 room-detail-card">
            <h3>Customer Data</h3>
            <div class="col-12 p-0">
                <div class="d-flex flex-row">
                    <div class="flex-grow-1">
                        <h6 class="title-text">Booking Id:</h6>
                    </div>
                    <div class="flex-grow-1">
                        <span class="body-text">${roomOwner.bookingId}</span>
                    </div>
                </div>
                
                <div class="d-flex flex-row">
                    <div class="flex-grow-1">
                        <h6 class="title-text">Meeting Id:</h6>
                    </div>
                    <div class="flex-grow-1">
                        <span class="body-text">${roomOwner?.meetingId}</span>
                    </div>
                </div>
                
                <div class="d-flex flex-row">
                    <div class="flex-grow-1">
                        <h6 class="title-text">Reference Code:</h6>
                    </div>
                    <div class="flex-grow-1">
                        <span class="body-text">${roomOwner.bookingReferenceCode}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-0 mb-3 room-detail-card">
            <h3>Members</h3>
            <div class="col p-0">
                <div id="room-detail-members"></div>
            </div>
        </div>
        
        <div id="leave-room" class="row m-0 mb-3 room-detail-card ${!inside && 'd-none'}">
            <div class="col-12 p-0">
                <div class="btn btn-danger w-100" onclick="areYouSure(() => leaveRoom(currentRoom._id), 'Are you sure you want to leave the room?')">
                    Leave Room
                </div>
            </div>
        </div>
      
    </div>
    `
  );
};
