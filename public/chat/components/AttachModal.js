const AttachModal = () => `
      <div class="modal fade" id="attach-modal" tabindex="-1" aria-labelledby="attach-modal" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Send File</h5>
              
              <span id="attach-modal-close-button" class="icon d-flex" data-bs-dismiss="modal" aria-label="Close" onclick="cancelAttachModal()">
                <i class="fa fa-times fa-lg text-icon" aria-hidden="true"></i>
              </span>

            </div>
            <div class="modal-body">
                <div id="attach-content" class="d-flex justify-content-center mb-2">
                </div>
                
                <div class="d-flex flex-grow-1">
                    <div class="message-box-out">
                        <div id="attach-message-box" class="message-box" role="textbox" data-text="Enter text here" contentEditable="true" spellcheck="true"></div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal" onclick="cancelAttachModal()">Close</button>
              <button type="button" class="btn btn-modal-confirm" data-bs-dismiss="modal" onclick="confirmAttachModal()">Send message</button>
            </div>
          </div>
        </div>
      </div>
  `;
