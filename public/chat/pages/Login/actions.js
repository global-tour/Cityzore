const initializeLogin = () => {
  let chatToken = localStorage.getItem("chatTokens");
  let chatUser = localStorage.getItem("chatUser");

  if (chatToken) {
    user = JSON.parse(chatUser);
    appStart();
    socketService.startSocket(JSON.parse(chatToken));
    listenActivity();
  } else {
    let status = $("#app").attr("data-status");

    if (status == "1") {
      let data = $("#app").data("response");

      if (data) {
        let queryConfig = {
          token: `${data.token}`,
          userId: data._id,
          userRole: data.role
        }

        user = data;
        appStart();
        socketService.startSocket(queryConfig);
        listenActivity();
      }
    } else {
      $.ajax({
        url: '/admin/ajax',
        type: 'POST',
        dataType: 'json',
        data: {
          user_id: $("input[name='defaultUserID']").val(),
          action: "chat_status_set_to_zero",
          _token: $("input[name='_token']").val()
        },
      }).done(function (response) {
        if (response.status == "success") {
          logoutStaff();
        }
      })
    }
  }
};

$(document).ready(() => {
  appWrapper = $('#app');
  appWrapper.prepend(Login.build());

  $('#staffEmail').change(function () {
    staff.email = this.value;
  });

  $('#staffPassword').change(function () {
    staff.password = this.value;
  });
});

let control = true;

const loginStaff = async () => {
  if (!control) {
    alert("Too Many Attempt!");
    return;
  }

  control = false;
  let {success, data} = await authApi.loginStaff($("#staffEmail").val(), $("#staffPassword").val());
  control = true;

  if (success) {
    user = data;

    let queryConfig = {
      token: `${data.token}`,
      userId: data._id,
      userRole: data.role
    };

    localStorage.setItem("chatTokens", JSON.stringify(queryConfig));
    localStorage.setItem("chatUser", JSON.stringify(user));

    appStart();
    socketService.startSocket(queryConfig);
    listenActivity();
  }
};

const logoutStaff = async () => {
  $("#app").attr("data-status", "0");

  localStorage.removeItem("chatTokens");
  localStorage.removeItem('chatUser');
  socketService.stopSocket();
  appWrapper.empty();
  appWrapper.prepend(Login.build());
};

const listenActivity = () => {
  const activitySocketHelper = new ActivitySocketHelper();

  socketService.listen('onlineCustomer', activitySocketHelper.onlineCustomer);
  socketService.listen('onlineStaff', activitySocketHelper.onlineStaff);
  socketService.listen('listenActivity', activitySocketHelper.listenActivity);
  socketService.listen('listenChatStatus', activitySocketHelper.listenChatStatus);
};

const appStart = () => {
  $('#app-login').remove();
  appWrapper.prepend(App.build());
};
