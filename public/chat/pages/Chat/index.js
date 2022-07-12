const ChatComponent = () => (
  `  
    ${ChatHeader()}
     <div class="main-wrap">
        <div class="chat-wrap">
            <div class="messages-wrap">
                <div id="drop-wrap" class="drop-wrap d-none">
                    <h3>Drag and drop the file here.</h3>
                </div> 
                <div id="chat" class="chat"></div>
            </div>
            
            <div id="emojis"></div>
            <div id="message-wrap" class="message-wrap">
                ${ChatJoinButton()}
                ${ChatMessageCreator()}
            </div>
        </div>
        
        <div id="room-detail-wrap" class="room-detail-wrap d-none">
            ${RoomDetail()}
        </div>
     </div>
     ${AttachModal()}
   `
);

const Chat = new Component({ initialize: initializeChat, component: ChatComponent });
