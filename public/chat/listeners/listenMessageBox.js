const listenMessageBox = () => {
  messageBox = document.getElementById('message-box');

  messageBox.addEventListener('input', (e) => {
    let value = messageBox.textContent.trim();
    let outerValue = messageBox.outerText;

    if (!$.isEmptyObject(currentRoom)) {
      if (value.length === 1) {
        typing = true;
        messageService.isTyping(currentRoom._id, true);
      }
      else if (value.length < 1 && typing) {
        typing = false;
        messageService.isTyping(currentRoom._id, false);
      }

      messageSubject.next(messageBox.outerText);
    }

    message = outerValue;
  });

  messageBox.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey && message.trim().length > 0) {
      e.preventDefault();
      sendMessage();
    }
  });
};

const subscriptionMessageBox = () => {
  messageSubject.pipe(
    debounceTime(1000),
    map((val) => val.trim()),
    distinctUntilChanged()
  ).subscribe((data) => {
    if (data.length > 0) {
      if (!typing) {
        messageService.isTyping(currentRoom._id, true);
      }

      typing = true;
      messageSubjectEndChange.next(data);
    }
  });

  messageSubjectEndChange.pipe(
    debounceTime(1000)
  ).subscribe((data) => {
    if ((data === message || message === '') && typing) {
      typing = false;
      messageService.isTyping(currentRoom._id, false);
    }
  });
}
