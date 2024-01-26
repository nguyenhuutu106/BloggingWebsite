<?php
require 'config/database.php';
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
    <link href="<?=ROOT_URL?>assets/css/toastr.css" rel="stylesheet">

    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Ajax -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="./js/toastr.js"></script>
    <script src="./js/ajax.js"></script>
    <!-- MOMENT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>
<body>
<!--=================== BEGIN OF NAV ====================-->
<?php 
require 'check_account_session.php';
require 'class/user.php';
$database = new Database();
$db = $database->getConnection();

$user = new User($db);

?>
<div class="nav">
        <div class="header nav__header">
            <a href="<?=ROOT_URL?>index.php" class="nav__logo">Travel Châu Á</a>
            <div class="search__bar">
                <form class="header search__bar-header" action="<?=ROOT_URL?>search_post.php" method="GET">
                    <div>
                        <i class="uil uil-search"></i>
                        <input type="search" name="postSearch" id="postSearch" placeholder="Tìm Kiếm">
                        <button type="submit" class="btn">Tìm</button>
                    </div>
                </form>
            </div>
            <ul class="nav__items">
            <?php
            if(!isset($_SESSION['userAccount'])) {  ?>
                    <li><a href="<?=ROOT_URL?>categories.php">Thể Loại</a></li>
                    <li><a href="<?=ROOT_URL?>login.php">Đăng nhập</a></li>
                    <li><a href="<?=ROOT_URL?>signup.php">Đăng ký</a></li>
                <?php
            }else { ?>
                <li><a href="<?=ROOT_URL?>categories.php">Thể Loại</a></li>
                <li><div class="notification_wrap">
                    <div class="notification_icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge"></span>
                </div>
                <div class="notify_dropdown">
                  <?php 
                  $result = $user->loadNotification($account['UserId']);
                  echo $result?>
                </div>
              </div></li>
                <li class="nav__profile">
                    <div class="avatar">
                        <img style="height: 100%;" src="./assets/images/avatar/<?php echo $account['UserImage']; ?>" alt="Avatar" onclick="openProfile()">
                    </div>
                    <ul>
                      <?php if ($account['UserRole'] == 0) { ?>
                        <li><a href="<?=ROOT_URL?>account_management.php">Quản Lý Tài Khoản</a></li>
                        <li><a href="<?=ROOT_URL?>profile.php?id=<?=$account['UserId']?>">Trang Cá Nhân</a></li>
                        <li><a href="<?=ROOT_URL?>liked_post.php">Bài Viết Đã Thích</a></li>
                        <li><a href="<?=ROOT_URL?>following.php">Tài Khoản Đã Theo Dõi</a></li>
                        <li><a href="<?=ROOT_URL?>logout.php">Đăng xuất</a></li>
                        <?php } else { ?>
                        <li><a href="<?=ROOT_URL?>admin/index.php">Trang Admin</a></li>
                        <li><a href="<?=ROOT_URL?>profile.php?id=<?=$account['UserId']?>">Trang Cá Nhân</a></li>
                        <li><a href="<?=ROOT_URL?>liked_post.php">Bài Viết Đã Thích</a></li>
                        <li><a href="<?=ROOT_URL?>following.php">Tài Khoản Đã Theo Dõi</a></li>
                        <li><a href="<?=ROOT_URL?>logout.php">Đăng xuất</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
            </ul>
                <button id="open__nav-btn"><i class="uil uil-bars"></i></button>
                <button id="close__nav-btn"><i class="uil uil-bars"></i></button>
            </div>
    </div>
    <!--=================== END OF NAV ====================-->
