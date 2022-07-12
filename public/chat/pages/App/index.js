const AppComponent = () => {
  const powerText = chatStatus ? 'Power Off' : 'Power On';

  return (
    `
      <div class="row wrap">
        <div class="col-lg-3 p-0">
            <div id="app-left">
                ${Lobby.build()}
            </div>
        </div>

        <div class="col-lg-9 p-0">
          <div id="app-right">
            <div id="empty-room" class="empty-room d-flex ct-center">
                <div class="empty-content">
                    <img class="app-logo" src="https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/Logo.png" />
                    <div class="d-flex flex-column align-items-start px-3">
                        <h4 class="title-text">Change the activity of the system.</h4>
                        <button id="empty-chat-status-button" class="btn btn-success w-100" onclick="areYouSure(() => updateChatStatus())">
                            ${powerText}
                        </button>     
                    </div>    
                </div>
            </div>
          </div>
        </div>
      </div>
      
      ${ActivityModal()}
    `
  );
};

const App = new Component({ initialize: initializeApp, component: AppComponent });
