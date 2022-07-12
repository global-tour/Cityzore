const MemberItem = (memberData) => {
  let { member } = memberData;
  let memberImage = member?.role === 'Customer'
    ? 'https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/Customer.png'
    : 'https://cityzore.s3.eu-central-1.amazonaws.com/mobile/global-tickets/StaffMan.png';

  let memberFulName = member ? `${member.name} ${member.surname}` : 'Old User'

  return `
      <div id="room-detail-member-${member?._id}" class="room-detail-member">
          <img src="${memberImage}" width="40" height="40" />
          <span class="title-text ml-1">${memberFulName}</span>
      </div>
    `;
};
