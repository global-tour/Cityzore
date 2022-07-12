const Profile = (fullName) => {
  const powerText = chatStatus ? 'Power Off' : 'Power On';

  return (
    `
      <div class="profile">
        <div class="d-flex m-0">
            <div class="d-flex align-items-center">
                <img src="https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/StaffMan.png">
                <span class="title-text one-line-text" id="user-name">${fullName}</span>
            </div>

            <div class="d-flex dropdown ms-auto">
              <span class="btn p-0 icon d-flex" id="profile-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-h fa-lg text-icon" aria-hidden="true"></i>
              </span>
  
              <ul class="dropdown-menu" aria-labelledby="profile-dropdown">
                  <li>
                    <a id="more-theme-button" class="dropdown-item" onclick="changeTheme()" href="#">
                      <span class="fa fa-moon fa-sm"></span>
                      <span class="ms-1">Dark Mode</span>
                    </a>
                  </li>
                  
                  <li>
                    <a id="more-action-button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#activity-modal" href="#">
                        <span class="fa fa-eye fa-sm"></span>
                        <span class="ms-1">See Actions</span>
                    </a>
                  </li>
                  
                  <li>
                    <a id="more-power-button" class="dropdown-item" href="#" onclick="areYouSure(() => updateChatStatus())">
                        <span class="fas fa-power-off fa-sm"></span>
                        <span class="ms-1">${powerText}</span>
                    </a>
                  </li>
                  
                  <li>
                    <a id="more-logout-button" class="dropdown-item" href="#" onclick="areYouSure(() => logoutStaff(), 'Are you sure you want to logout?')">
                        <span class="fas fa-sign-out-alt fa-sm"></span>
                        <span class="ms-1">Logout</span>
                    </a>
                  </li>
              </ul>
          </div>
        </div>
      </div>
  `
  )
};
