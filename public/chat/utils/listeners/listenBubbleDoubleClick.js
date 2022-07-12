const listenBubbleDoubleClick = () => {
  $(document).on('dblclick', '.chat-bubble', (e) => {

    if (quoteMessage?._id !== e.currentTarget.id) {

      let inside = currentRoom.members.findIndex(memberData => memberData.member?._id === user._id)
      let findQuoteMessage = currentRoom.messages.find((message) => message._id === e.currentTarget.id);

      if (findQuoteMessage.messageType !== 'System' && inside !== -1) {
        if (!$.isEmptyObject(quoteMessage)) {
          quoteMessage = undefined;
          $('#chat-quote-message').remove();
        }

        quoteMessage = findQuoteMessage;
        chatBottomWrapper.prepend(ChatQuoteMessage(findQuoteMessage));
      }

    }
  });
};
