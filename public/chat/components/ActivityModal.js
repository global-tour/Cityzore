const ActivityModal = () => `
    <div class="modal fade" id="activity-modal" tabindex="-1" aria-labelledby="activity-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="activity-modal-label">Online Users</h5>
              
              <span class="icon d-flex" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times fa-lg text-icon" aria-hidden="true"></i>
              </span>

            </div>
            <div class="modal-body">
            
                <div class="d-flex flex-column">
                  <div class="d-flex flex-row">
                      <span id="model-tab-customer" class="model-tab title-text active" onclick="selectActivityTab('Customer')">Customer</span>
                      <span id="model-tab-staff" class="model-tab title-text"  onclick="selectActivityTab('Staff')">Staff</span>
                  </div>
                
                    <div class="tab-main-body">
                      <div id="body-customer" class="tab-body"></div>
                      <div id="body-staff" class="tab-body d-none"></div>
                    </div>

                </div>

            </div>
          </div>
        </div>
      </div>
  `;
