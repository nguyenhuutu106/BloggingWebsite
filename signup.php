<?php 
require 'config/database.php';
require 'class/user.php';
require 'admin/class/admin.php';
session_start();

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$admin = new Admin($db);
if($admin->loggedIn()) {	
	header("Location:" . ROOT_URL . "admin/index.php");	
}else if($user->loggedIn()){
   header("Location:" . ROOT_URL . "index.php");
}
$registerMessage = '';

if(isset($_POST['submit'])&& $_POST['randcheck']==$_SESSION['rand']){
   $userAccount = $_POST["userAccount"];
	$userName = $_POST["userName"];
   $userEmail = $_POST["userEmail"];
   $userPassword = $_POST["userPassword"];
   $userRePassword = $_POST["userRePassword"];	
   if (strlen($userAccount) == 0){
      $registerMessage = 'Vui lòng nhập tên đăng nhập!';
   } else if(strlen($userName) == 0){
      $registerMessage = 'Vui lòng nhập tên hiển thị!';
   } else if(strlen($userEmail) < 5){
      $registerMessage = 'Vui lòng nhập Email!';
   } else if(strlen($userPassword) < 5){
      $registerMessage = 'Mật khẩu của bạn phải có tối thiểu 5 kí tự!';
   }else if(strlen($userRePassword) == 0){
      $registerMessage = 'Trường nhập lại mật khẩu của bạn chưa được nhập!';
   } else if($userPassword !== $userRePassword){
      $registerMessage = 'Mật khẩu nhập lại của bạn không trùng khớp!';
   }
   if($registerMessage == '') {	
      $user->account = $_POST["userAccount"];
      $user->name = $_POST["userName"];
      $user->email = $_POST["userEmail"];
      $user->password = $_POST["userPassword"];
      $user->repassword = $_POST["userRePassword"];		
      if($user->register() == "account") {		
         $registerMessage = "Tên tài khoản bạn đặt đã tồn tại!";
      }else if($user->register() == "email"){
         $registerMessage = "Email bạn đặt đã tồn tại!";
      } 
      else if($user->register()){
         header("Location:" . ROOT_URL . "login.php?action=success");
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
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/css/style.css">
    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    
    <div class="register">
        <img class="login__bg" src="./assets/images/lg-bg.jpg" alt="image" />
  
    <form class="login__form" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
           <h1 class="login__title">Đăng Ký</h1>
   
           <?php if($registerMessage != '') {?>
               <div class="alert__message error">
                  <p>
                     <?php echo  $registerMessage; ?>
                  </p>
               </div> <?php }?>
               <?php
                $rand=rand();
                $_SESSION['rand']=$rand;
                ?>
           <div class="register__inputs">
              <div class="login__box">
                  <input type="hidden" name="randcheck" value="<?=$rand; ?>" >
                 <input class="login__input" type="text" name="userAccount" value="<?php echo isset($userAccount)? $userAccount: "" ?>" placeholder="Tên đăng nhập" />
                 <i class="ri-user-line"></i>
              </div>
              <div class="login__box">
                 <input class="login__input" type="text" name="userName" value="<?php  echo isset($userName)? $userName: "" ?>" placeholder="Tên hiển thị" />
                 <i class="ri-user-line"></i>
              </div>
              <div class="login__box">
                <input class="login__input" type="email" name="userEmail" value="<?php  echo isset($userEmail)? $userEmail: "" ?>" placeholder="Email" />
                <i class="ri-mail-line"></i>
             </div>
              <div class="login__box">
                <div>
                    <input id="password-field" class="login__input" type="password" name="userPassword" placeholder="Mật khẩu mới"/>
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
                 <i class="ri-lock-line"></i>
              </div>
              <div class="login__box">
                <div>
                    <input id="re-password-field" class="login__input" type="password" name="userRePassword" placeholder="Xác nhận mật khẩu"/>
                    <span toggle="#re-password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
                 <i class="ri-lock-line"></i>
              </div>
        </div>
        <button type="submit" class="login__button" name="submit">Đăng ký</button>

         <div class="login__register">
            Bạn đã có tài khoản? <a href="./login.php">Đăng nhập</a>
         </div>
      </form>
   </div>
   <script src="./js/main.js"></script>
</body>
</html>