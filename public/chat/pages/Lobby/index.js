const LobbyComponent = () => {
  let fullName = `${user.name} ${user.surname}`;

  return (
    `
      ${Profile(fullName)}
      ${Search()}
      <div class="contact-list-wrap">
            <div id="contact-list" class="contact-list"></div>
      </div>
    `
  );
};

const Lobby = new Component({ initialize: initializeLobby, component: LobbyComponent });
