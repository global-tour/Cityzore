const ChatQuoteMessage = (message) => {
  return (
    `
    <div id="chat-quote-message" class="d-flex m-0 px-2 py-2">
      <div class="d-flex justify-content-center">
        <i class="fa fa-times fa-lg text-icon invisible" aria-hidden="true"></i>
      </div>
    
      <div class="d-flex flex-grow-1 px-2">
        ${QuoteMessage(message)}
      </div>
    
      <div class="d-flex justify-content-center">
        <span class="icon d-flex" onclick="removeQuoteMessage()">
          <i class="fa fa-times fa-lg text-icon" aria-hidden="true"></i>
        </span>
      </div>
      
      <div class="d-flex justify-content-center">
        <i class="fa fa-times fa-lg text-icon invisible" aria-hidden="true"></i>
      </div>
    </div>
    `
  );
};
