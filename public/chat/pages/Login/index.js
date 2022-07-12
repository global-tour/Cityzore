const LoginComponent = () => {

if(localStorage.getItem('chatTokens')){
  return '';
}

  return (
  `<div id="app-login" class="row m-0 h-100 ct-center">
      <div class="col-md-4 p-0">
        <div class="ct-center"><h3>Login Staff</h3></div>
        <input id="staffEmail" class="form-control form-control-lg mt-1" type="text" placeholder="Email"
               value="${defaultUserName}">
        <input id="staffPassword" class="form-control form-control-lg mt-1" type="password" placeholder="Password"
                 value="${defaultPassword}">
        <button type="button" class="btn btn-dark mt-2 w-100" onclick="loginStaff()">Login Staff</button>
      </div>
    </div>`
)};

const Login = new Component({ initialize: initializeLogin, component: LoginComponent });
