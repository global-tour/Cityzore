const getExtension = (filename) => {
  return filename.split('.').pop();
}

const getCompressAttach = (fileExtension, file, callBack) => {
  switch (fileExtension) {
    case 'png':
    case 'jpg':
    case 'jpeg':
      new Compressor(file, {
        quality: 0.6,

        success(result) { callBack(result) },
        error(err) {},
      });
      break;

    default:
      callBack(file);
      break;
  }
};

const renderAttachFile = (fileExtension, url, file) => {
  switch (fileExtension) {
    case 'png':
    case 'jpg':
    case 'jpeg':
      return (`
            <img src="${url}" class="attach-image" />
          `)

    case 'mp4':
    case 'webm':
      return (`
            <video class="attach-video" controls>
                <source src="${url}" type="video/${fileExtension}">
            </video>
          `)

    case 'pdf':
    case 'csv':
    case 'doc':
    case 'docx':
    case 'xls':
    case 'xlsx':
      return (`
        <div class="attach-file">
            <div class="attach-left"><span class="fa fa-file"></span></div>
            <div class="attach-right">${file?.name}</div>
        </div>
      `);

    default:
      break;

  }
}

const listenAttachFile = () => {
  let attachContent = $('#attach-content');
  let attachMessageBox = document.getElementById('attach-message-box')

  attachInput.onchange = async (event) => {
    if (attachInput?.files) {
      let file = attachInput.files[0];
      let fileExtension = getExtension(file?.name)

      getCompressAttach(fileExtension, file, (compressFile) => {
        attachFile = compressFile;
      });

      let url = URL.createObjectURL(file);
      attachMessageBox.textContent = '';

      attachContent.empty();
      attachContent.append(renderAttachFile(fileExtension, url, attachFile));

      attachModal.modal('show');
    }

  };
};

const listenAttachMessageBox = () => {
  let attachMessageBox = document.getElementById('attach-message-box');

  attachMessageBox.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      attachModal.modal('hide');
      confirmAttachModal();
    }
  });
};
