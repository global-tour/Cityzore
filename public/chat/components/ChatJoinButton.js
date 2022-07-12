const ChatJoinButton = () => {
  return (
    `
    <div id="join-room-wrapper" class="d-flex">
      <div class="d-flex justify-content-center flex-grow-1">
        <div 
          onclick="joinRoom(currentRoom?._id)"
          id="join-room-button" 
          role="button" 
          class="btn btn-success rounded-0 d-flex w-100 h-100"
        >
            Join Room
        </div>
      </div>
    </div>
    `
  );
};
