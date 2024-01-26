<?php
require 'config/database.php';
require 'class/user.php';
require 'admin/class/admin.php';
session_start();
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$admin = new Admin($db);

if($admin->loggedIn() == 1) {	
	header("Location:" . ROOT_URL . "admin/index.php");	
}else if($user->loggedIn()){
   header("Location:" . ROOT_URL . "index.php");
}

$loginMessage = '';
if(isset($_POST['submit'])){
   $userAccount = $_POST["userAccount"];
   $userPassword = $_POST["userPassword"];
   if (strlen($userAccount) == 0){
      $loginMessage = 'Vui lòng nhập tên đăng nhập!';
   } else if(strlen($userPassword) < 5){
      $loginMessage = 'Mật khẩu của bạn không hợp lệ!';
   } 
   if($loginMessage == '') {	
      $user->account = $_POST['userAccount'];
      $user->password = $_POST['userPassword'];
      if($user->login() == "admin"){
         header("Location:" . ROOT_URL . "admin/index.php");
      } else if ($user->login() == "user" && isset($_SESSION['url'])){
         header("Location:".$_SESSION['url']."");
      } else if ($user->login() == "user"){
         header("Location:" . ROOT_URL . "index.php");
      } else {
         $loginMessage = "Tài khoản hoặc mật khẩu của bạn không đúng!";
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Blogging</title>
    <link rel="icon" type="image/x-icon" href="https://img.freepik.com/free-vector/detailed-travel-logo_23-2148616611.jpg?w=740&t=st=1705736374~exp=1705736974~hmac=6a131cfe66e3090b7220d21dec75fcac073bc83d69f3bc103dc7cebd09a9fa17">
    <!--- favicon-->
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <!-- Custom Css -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="" />
</head>
<body>
    
    <div class="login">
        <img class="login__bg" src="./assets/images/lg-bg.jpg" alt="image" />
  
    <form class="login__form" action="<?php echo $_SERVER['PHP_SELF']?>"  method="POST">
           <h1 class="login__title">Đăng Nhập</h1>
  
         <?php if(@$_GET['action'] == 'success' ) { ?>
            <div class="alert__message success">
                  <p>Bạn đã đăng ký tài khoản thành công! Có thể đăng nhập!</p>
            </div>
         <?php }else if(@$_GET['action'] == 'success_password' ) { ?>
            <div class="alert__message success">
                  <p>Bạn đã làm mới mật khẩu thành công! Có thể đăng nhập!</p>
            </div>
         <?php }?>
         <?php if($loginMessage != '') {?>
               <div class="alert__message error">
                  <p>
                     <?php echo  $loginMessage; ?>
                  </p>
               </div> <?php }?>
           <div class="login__inputs">
              <div class="login__box">
                 <input class="login__input" type="text" name="userAccount" value="<?php echo isset($userAccount)? $userAccount: "" ?>" placeholder="Tên đăng nhập" />
                 <i class="ri-user-line"></i>
              </div>
              <div class="login__box">
                 <input class="login__input" type="password" name="userPassword" placeholder="Mật khẩu" />
                 <i class="ri-lock-2-fill"></i>
              </div>
        </div>
        <button type="submit" class="login__button" name="submit">Đăng nhập</button>

         <div class="login__register">
            Bạn chưa có tài khoản? <a href="./signup.php">Đăng ký</a>
         </div>
         <div class="login__register">
            <a href="./forgot_password.php">Quên mật khẩu?</a>
         </div>
         <div class="login__register">
            <a href="./index.php">Về lại trang chủ</a>
         </div>
      </form>
   </div>
  
</body>
</html>