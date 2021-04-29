<?php
$login_message='Login';
$incorrect_login=NULL;
if (!empty($_POST['username']) && !empty($_POST['password'])){
  $user = $_POST['username'];
  $pass = $_POST['password'];
  if(authenticate($user, $pass) == true){
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];
    /* d($_SESSION); */
    header('Location: //'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
  } else {
    $incorrect_login='<span style="color:#fa1c48;display:block;text-align:center;">Access is denied</span>';
  }
}
?>
<!DOCTYPE html><html lang="en">
                                                   <head>
                                                     <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
                                                     <meta http-equiv="content-type" content="text/html; charset=utf-8" />
                                                     <title><?php echo $sitename; ?></title>
                                                     <style>
html{ height: 100%; }
body {
  margin: 0;
  padding: 0;
  font-family: 'Arial', sans-serif;
  background: linear-gradient(#141e30, #243b55);
}

.login-box {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 400px;
  padding: 40px;
  transform: translate(-50%, -50%);
  background: rgba(0,0,0,.5);
  box-sizing: border-box;
  box-shadow: 0 15px 25px rgba(0,0,0,.6);
  border-radius: 10px;
}

.login-box h2 {
  margin: 0 0 30px;
  padding: 0;
  color: #F1EEE6;
  text-align: center;
}

.login-box .user-box {
  position: relative;
}

.login-box .user-box input {
  width: 100%;
  padding: 10px 0;
  font-size: 16px;
  color: #F1EEE6;
  margin-bottom: 30px;
  border: none;
  border-bottom: 1px solid #F1EEE6;
  outline: none;
  background: transparent;
}

.login-box .user-box label {
  position: absolute;
  top: 0;
  left: 0;
  padding: 10px 0;
  font-size: 16px;
  color: #F1EEE6;
  pointer-events: none;
  transition: .5s;
}

.login-box .user-box input:focus ~ label, .login-box .user-box input:valid ~ label {
  top: -20px;
  left: 0px;
  color: #03E9F4;
  font-size: 12px;
  outline: none;
}


.login-box form button {
  position: relative;
  display: inline-block;
  padding: 10px 20px;
  color: #03E9F4;
  font-size: 16px;
  text-decoration: none;
  text-transform: uppercase;
  overflow: hidden;
  transition: .5s;
  margin-top: 40px;
  letter-spacing: 4px;
  background: transparent;
  border:0;
  float:right;
}

.login-box form button:hover {
  background: #03E9F4;
  color: #F1EEE6;
  border-radius: 5px;
  box-shadow: 0 0 5px #03E9F4,
    0 0 25px #03E9F4;
}

.login-box button span {
  position: absolute;
  display: block;
}



                                                     </style>
                                                   </head>
                                                   <body>

                                                     <div class="login-box">
                                                       <h2><?php echo $sitename; ?></h2>
                                                       <p><?php echo $incorrect_login; ?></p>
                                                       <form method="POST">
                                                         <div class="user-box">
                                                           <input type="text" name="username" required autofocus>
                                                           <label>Username</label>
                                                         </div>
                                                         <div class="user-box">
                                                           <input type="password" name="password" required>
                                                           <label for="">Password</label>
                                                         </div>
                                                         <button type="submit">
                                                           <span></span>
                                                           <span></span>
                                                           <span></span>
                                                           <span></span>
                                                           Login</button>
                                                       </form>
                                                     </div>
                                                   </body>
                                                 </html>
