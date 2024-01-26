<?php
require '../config/database.php';
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
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Ajax -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="admin">
<!--=================== BEGIN OF NAV ====================-->
<?php 
require '../check_account_session.php';
require 'class/Admin.php';

$database = new Database();
$db = $database->getConnection();

$admin = new Admin($db);
if($admin->loggedIn() != 1) {	
	header("Location:" . ROOT_URL . "login.php");	
}else if($account['UserRole'] == 0){
    header("Location:" . ROOT_URL . "index.php");	
}
?>
<!--=================== BEGIN OF SIDEBAR ====================-->
    <div class="admin-sidebar">
        <a href="<?=ROOT_URL?>index.php">
            <div class="side-header">
                <h3>T<span>rang</span>  C<span>hủ</span></h3>
            </div>
        </a>
        <div class="side-content">
            <div class="side-menu">
                <a href="./account_management.php" <?php echo $page == 'account_management' ? 'class="active"' : ''; ?> style="text-align: center;">
                    <i class="fa-solid fa-user"></i>
                    <small>Đổi Thông Tin</small>
                </a>
            </div>
            <div class="admin-profile">
                <div class="admin-profile-img bg-img" style="background-image: url(<?=ROOT_URL?>assets/images/avatar/<?php echo $account['UserImage']; ?>)"></div>
                <h4><?php echo $account['UserName']; ?></h4>
                <small>Admin</small>
            </div>

            <div class="side-menu">
                <ul>
                    <li>
                       <a href="./index.php" <?php echo $page == 'index' ? 'class="active"' : ''; ?>>
                            <i class="fa-solid fa-house"></i>
                            <small>Dashboard</small>
                        </a>
                    </li>
                    <li>
                       <a href="./manage_posts.php" <?php echo $page == 'manage_posts' ? 'class="active"' : ''; ?>>
                            <i class="fa-solid fa-pen"></i>
                            <small>Bài Viết</small>
                        </a>
                    </li>
                    <li>
                       <a href="./manage_categories.php" <?php echo $page == 'manage_categories' ? 'class="active"' : ''; ?>>
                            <i class="fa-solid fa-list"></i>
                            <small>Thể Loại</small>
                        </a>
                    </li>
                    <li>
                       <a href="./manage_users.php" <?php echo $page == 'manage_users' ? 'class="active"' : ''; ?>>
                            <i class="fa-solid fa-user"></i>
                            <small>Tài Khoản</small>
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php">
                             <i class="fa-solid fa-right-from-bracket"></i>
                             <small>Đăng xuất</small>
                         </a>
                     </li>
                </ul>
            </div>
        </div>
    </div>
        <!--=================== END OF SIDEBAR ====================-->