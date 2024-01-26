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
$token = $_GET['token'];
$tokenHash = hash("sha256", $token);

$user->tokenHash = $tokenHash;

$tokenMessage = '';
if(isset($_POST['submit'])&& $_POST['randcheck']==$_SESSION['rand']){
    $userPassword = $_POST['userPassword'];
    $userRePassword = $_POST['userRePassword'];

    if(strlen(@$userPassword) < 5){
      $tokenMessage = 'Mật khẩu của bạn phải có tối thiểu 5 kí tự!';
    }else if(strlen($userRePassword) == 0){
      $tokenMessage = 'Trường nhập lại mật khẩu của bạn chưa được nhập!';
    } else if($userPassword !== $userRePassword){
      $tokenMessage = 'Mật khẩu nhập lại của bạn không trùng khớp với mật khẩu đã nhập!';
    }else if($tokenMessage == '') {	
      $user->password = $_POST['userPassword'];
      $user->tokenHash = $tokenHash;
      if($user->resetPassword() == "nodata"){
        $tokenMessage = "Có lỗi xảy ra trong việc thực hiện đổi mật khẩu!";
      } else if ($user->resetPassword()){
        header("Location:" . ROOT_URL . "login.php?action=success_password");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="" />
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    
    <div class="forgot">
        <img class="login__bg" src="./assets/images/lg-bg.jpg" alt="image" />
  
    <form class="login__form" action="<?php echo $_SERVER['PHP_SELF']."?token=".$token; ?>"  method="POST">
           <h1 class="login__title">Đặt Lại Mật Khẩu</h1>
            <?php
            if($user->checkToken() == "token not found"){ ?>
            <div class="alert__message error">
                  <p>
                     Trang đặt lại mật khẩu không hợp lệ
                  </p>
               </div> 
               <div class="login__register">
                    <a href="./login.php">Quay lại đăng nhập</a>
                </div>
            <?php } else if($user->checkToken() == "token expired"){?>
                <div class="alert__message error">
                  <p>
                     Phiên đổi mật khẩu của bạn đã hết hạn
                  </p>
               </div> 
               <div class="login__register">
                    <a href="./login.php">Quay lại đăng nhập</a>
                </div>
               <?php } else { ?>
                <?php if($tokenMessage != '') {?>
               <div class="alert__message error">
                  <p>
                     <?php echo  $tokenMessage; ?>
                  </p>
               </div> <?php }?>
                <?php
                $rand=rand();
                $_SESSION['rand']=$rand;
                ?>
            <div class="login__inputs">
              <div class="login__box">
                <div>
                    <input type="hidden" name="randcheck" value="<?=$rand; ?>" >
                    <input id="password-field" class="login__input" type="password" name="userPassword" placeholder="Mật khẩu mới"/>
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
                 <i class="ri-lock-line"></i>
              </div>
            </div>
            <div class="login__inputs">
              <div class="login__box">
                <div>
                    <input id="re-password-field" class="login__input" type="password" name="userRePassword" placeholder="Xác nhận mật khẩu"/>
                    <span toggle="#re-password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
                 <i class="ri-lock-line"></i>
              </div>
            </div>
        <button type="submit" class="login__button" name="submit">Đặt lại mật khẩu</button>

         <div class="login__register">
            <a href="./login.php">Quay lại đăng nhập</a>
         </div>
         <?php } ?>
      </form>
   </div>
  <script src="./js/main.js"></script>
</body>
</html>