const ActivityItem = (user) => {
  return `
    <div class="row">
        <div class="col-3">
            <span class="body-text activity-text">${user.meta.name}</span>
        </div>
        <div class="col-3">
            <span class="body-text activity-text">${user.meta.surname}</span>
        </div>
        <div class="col-6">
            <span class="body-text activity-text">${moment(user.when).format('LLL')}</span>
        </div>
    </div>
  `;
}