const listenMessageList = () => {
  messagesWrapper.scroll(async function() {
    if($(this).scrollTop() <= 0) {
      if(!messageLoading && (messagePagination.page === 1 || currentRoom.messages.length < messagePagination.total)) {
        let currentContentHeight = $(this)[0].scrollHeight;

        await getMessagesByRoom(currentRoom?.id);

        setTimeout(() => {
          let newContentHeight = $(this)[0].scrollHeight;
          scrollToTop($(this), newContentHeight - currentContentHeight, 0)
        }, 25)
      }
    }
  })
};
