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

$emailMessage = '';
if(isset($_POST['submit'])){
   $userEmail = $_POST["userEmail"];

   $token = bin2hex(random_bytes(16));

   $tokenHash = hash("sha256", $token);

   $expiry = date("Y-m-d H:i:s", time() + 60 * 10);

   if (strlen($userEmail) < 5){
      $emailMessage = 'Email bạn nhập không hợp lệ!';
   }else if($emailMessage == '') {	
      $user->email = $_POST['userEmail'];
      $user->token = $token;
      $user->tokenHash = $tokenHash;
      $user->expiry = $expiry;
      if($user->checkEmail() == "email"){
         $emailMessage = "Email bạn nhập không tồn tại trên hệ thống!";
      } else if ($user->sendEmail()){
         header("Location:" . ROOT_URL . "forgot_password.php?action=success");
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
    
    <div class="forgot">
        <img class="login__bg" src="./assets/images/lg-bg.jpg" alt="image" />
  
    <form class="login__form" action="<?php echo $_SERVER['PHP_SELF']?>"  method="POST">
           <h1 class="login__title">Quên Mật Khẩu </h1>
  
         <?php if(@$_GET['action'] == 'success' ) { ?>
            <div class="alert__message success">
                  <p>Mail khôi phục mật khẩu đã được gửi, hãy kiểm tra hòm thư của bạn!</p>
            </div>
         <?php }?>
         <?php if($emailMessage != '') {?>
               <div class="alert__message error">
                  <p>
                     <?php echo  $emailMessage; ?>
                  </p>
               </div> <?php }?>
           <div class="login__inputs">
              <div class="login__box">
                 <input class="login__input" type="email" name="userEmail" placeholder="Email" value="<?php echo isset($userEmail)? $userEmail: "" ?>"/>
                 <i class="ri-mail-line"></i>
              </div>
        </div>
        <button type="submit" class="login__button" name="submit">Lấy lại mật khẩu</button>

         <div class="login__register">
            <a href="./login.php">Quay lại đăng nhập</a>
         </div>
      </form>
   </div>
</body>
</html>