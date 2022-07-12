const ALLOWED_FORMATS = [
  'image/png',
  'image/jpg',
  'image/jpeg',
  'video/mp4',
  'video/webm',
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/vnd.ms-excel',
  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'text/csv'
]

const listenAttachDrop = () => {
  messagesWrapper.on("dragover", function(event) {
    attachDropWrapper.addClass('dragging');
    attachDropWrapper.removeClass('d-none');

    event.stopPropagation();
    event.preventDefault();
  });

  messagesWrapper.on("dragleave", function(event) {
    attachDropWrapper.removeClass('dragging');
    attachDropWrapper.addClass('d-none');

    event.stopPropagation();
    event.preventDefault();
  });

  messagesWrapper.on("drop", function(event) {
    attachDropWrapper.removeClass('dragging');
    attachDropWrapper.addClass('d-none');

    let files = event.originalEvent.dataTransfer.files
    let allow = Array.from(files).some(file => ALLOWED_FORMATS.includes(file.type));
    let { inside } = findRoomAndInsideUser(currentRoom?._id)

    if (inside) {
      if (allow) {
        attachInput.files = files;
        attachInput.dispatchEvent(attachInputEvent);
      } else {
        toastr.warning('Warning', 'Invalid file format.');
      }
    } else {
      toastr.warning('Warning', 'You cannot add content without joining the room.');
    }

    event.stopPropagation();
    event.preventDefault();
  });
}
