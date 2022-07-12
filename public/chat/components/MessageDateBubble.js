const MessageDateBubble = (date) => {
  let momentDate = moment(date);

  return `
    <div id="${Math.random()}" class="chat-bubble date">
        <div class="content">
              <span>${momentDate.format('LL')}</span>
        </div>
    </div>
  `
};