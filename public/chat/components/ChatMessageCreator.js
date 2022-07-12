const ChatMessageCreator = () => {
  return (
    `
    <div id="message-creator" class="d-flex d-none px-2">
      <div class="d-flex justify-content-center">
        <span class="icon d-flex" onclick="openEmojiKeyboard()">
          <i class="fa fa-smile fa-lg text-icon" aria-hidden="true"></i>
        </span>
      </div>
    
      <div class="d-flex flex-grow-1 px-2">
        <div class="message-box-out ">
            <div id="message-box" class="message-box" role="textbox" data-text="Enter text here" contentEditable="true" spellcheck="true"></div>
        </div>
      </div>

      <div class="d-flex justify-content-center">
        <span class="icon d-flex" onclick="selectFile()">
          <i class="fa fa-paperclip fa-lg text-icon" aria-hidden="true"></i>
          <input 
            class="d-none" 
            type="file" 
            id="attach-file" 
            accept="image/png,
                    image/jpg,
                    image/jpeg,
                    video/mp4,
                    video/webm,
                    application/pdf,
                    application/msword,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                    application/vnd.ms-excel,
                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                    .csv"
          >
        </span>
      </div>
    
      <div class="d-flex justify-content-center">
        <span class="icon d-flex" onclick="sendMessage()">
          <i class="fa fa-paper-plane fa-lg text-icon" aria-hidden="true"></i>
        </span>
      </div>
    </div>
    `
  );
};
