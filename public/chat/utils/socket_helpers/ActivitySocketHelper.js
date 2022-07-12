class ActivitySocketHelper extends SocketHelper {
  constructor() {
    super();
  }

  onlineCustomer = async (value) => {
    // console.log('onlineCustomer', value);
    onlineCustomers = value.data;

    $('#body-customer').empty();
    value.data.forEach((customer) => {
      $('#body-customer').append(ActivityItem(customer));
    })
  }

  onlineStaff = async (value) => {
    // console.log('onlineStaff', value);
    onlineStaffs = value.data;

    $('#body-staff').empty();
    value.data.forEach((staff) => {
      $('#body-staff').append(ActivityItem(staff));
    });
  }

  listenChatStatus = async (value) => {
    // console.log('listenChatStatus', value);

    changeChatStatus(value.data)
  }

  listenActivity = async (value) => {
    // console.log('listenActivity', value);

    if (value.data.activity !== 'Open') {
      let roomIndex = this.roomIndex(value.room);

      if (roomIndex !== -1) {
        rooms[roomIndex].staffLastSeen = value.data.staffLastSeen;
        updateUnreadMessageCount(value.room);
      }
    }

    activities = value.data.activities;
  }
}
