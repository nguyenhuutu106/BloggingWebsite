<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Blogging</title>
    <!--- favicon-->
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <!-- Custom Css -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Ajax -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<?php 
$page = 'index';
require 'inc/sidebar.php';

$countUser = $admin->countUsers();
$countPosts = $admin->countPosts();
$countCategories = $admin->countCategories();
$countViews = $admin->countViews();
?>
    <div class="main-content">    
        <main>
            <div class="page-header">
                <h1>Dashboard</h1>
            </div>
            <div class="page-content">
                <div class="analytics">
                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $countUser ?></h2>
                            <i class="fa-solid fa-users" style="font-size: 3.2rem;"></i>
                        </div>
                        <div class="card-progress">
                            <small>Số lượng thành viên</small>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $countPosts ?></h2>
                            <i class="fa-solid fa-pen-to-square" style="font-size: 3.2rem;"></i>
                        </div>
                        <div class="card-progress">
                            <small>Số lượng bài viết</small>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $countCategories ?></h2>
                            <i class="fa-solid fa-tag" style="font-size: 3.2rem;"></i>
                        </div>
                        <div class="card-progress">
                            <small>Số lượng thể loại</small>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $countViews ?></h2>
                            <i class="fa-solid fa-eye" style="font-size: 3.2rem;"></i>
                        </div>
                        <div class="card-progress">
                            <small>Tổng số lượng View</small>
                        </div>
                    </div>
                </div>
                </div>
            </div> 
        </main>
    </div>
    <script src="../js/main.js"></script>
</body>
</html>