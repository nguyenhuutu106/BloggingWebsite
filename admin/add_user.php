<?php 
$page = 'manage_users';
require 'inc/sidebar.php';

$addUserMessage = '';

if(isset($_POST['submit']) && $_POST['randcheck']==$_SESSION['rand']){
    $userAccount = $_POST["userAccount"];
	$userName = $_POST["userName"];
    $userEmail = $_POST["userEmail"];
    $userPassword = $_POST["userPassword"];
    $userRePassword = $_POST["userRePassword"];	
    $userRole = $_POST['userRole'];

    if (strlen($userAccount) == 0){
        $addUserMessage = 'Vui lòng nhập tên đăng nhập!';
    } else if(strlen($userName) == 0){
        $addUserMessage = 'Vui lòng nhập tên hiển thị!';
    } else if(strlen($userEmail) < 5){
        $addUserMessage = 'Vui lòng nhập Email!';
    } else if(strlen($userPassword) < 5 ){
        $addUserMessage = 'Mật khẩu tài khoản phải có tối thiểu 5 kí tự!';
    }else if(strlen($userRePassword) == 0){
        $addUserMessage = 'Mật nhập nhập lại chưa được nhập!';
    } else if($userPassword !== $userRePassword){
        $addUserMessage = 'Mật khẩu nhập lại tài khoản không trùng khớp!';
    }
    if($addUserMessage == '') {	
        $admin->account = $_POST["userAccount"];
        $admin->name = $_POST["userName"];
        $admin->email = $_POST["userEmail"];
        $admin->password = $_POST["userPassword"];
        $admin->role = $_POST["userRole"];

        if($admin->createUser() == "account") {		
            $addUserMessage = "Tên tài khoản đã tồn tại!";
        }else if($admin->createUser() == "email"){
            $addUserMessage = "Email đã tồn tại!";
        }else if ($admin->createUser() == "nodata"){
            $addUserMessage = "Có lỗi xảy ra trong việc thực hiện thêm tài khoản!";
        }else if($admin->createUser()){
            $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc thêm tài khoản!';
            $userAccount = "";
            $userName = "";
            $userEmail = "";
            $userPassword = "";
            $userRePassword = "";
        }
    }
}
?>
        <div class="admin-management-form">
        <a href="manage_users.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Thêm Tài Khoản</h2>
            <?php if ($addUserMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $addUserMessage ?>
                </div>
           <?php } else if (isset($_SESSION['success_message'])) { ?>
                <div class="alert__message success" style="width: 80%">
                        <?= $_SESSION['success_message'] ?>
                    </div>
            <?php  unset($_SESSION['success_message']); }?> 
            <form action="<?php echo $_SERVER['PHP_SELF']?>"  method="POST" enctype="multipart/form-data">
            <!-- Stop resubmission when refreshing page -->
                <?php
                $rand=rand();
                $_SESSION['rand']=$rand;
                ?>
                <div class="form__control">
                    <input type="hidden" name="randcheck" value="<?=$rand; ?>" >
                    <label for="userName">Tên đăng nhập</label>
                    <input type="text" placeholder="Tên đăng nhập" name="userAccount" value="<?php echo isset($userAccount)? $userAccount: "" ?>">
                </div>
                <div class="form__control">
                    <label for="userDisplayName">Tên hiển thị</label>
                    <input type="text" placeholder="Tên hiển thị" name="userName" value="<?php echo isset($userName)? $userName: "" ?>">
                </div>
                <div class="form__control">
                    <label for="userEmail">Email</label>
                    <input type="email" placeholder="Email" name="userEmail" value="<?php echo isset($userEmail)? $userEmail: "" ?>">
                </div>
                <div class="form__control">
                    <label for="userPassword">Mật khẩu</label>
                    <input type="password" placeholder="Mật khẩu" name="userPassword" value="<?php echo isset($userPassword)? $userPassword: "" ?>">
                </div>
                <div class="form__control">
                    <label for="userRePassword">Nhập lại mật khẩu</label>
                    <input type="password" placeholder="Nhập lại mật khẩu" name="userRePassword" value="<?php echo isset($userRePassword)? $userRePassword: "" ?>">
                </div>
                <div class="form__control">
                    <label for="userRole">Quyền tài khoản</label>
                    <select name="userRole">
                        <option value="0">User</option>
                        <option value="1">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn_admin" name="submit">Thêm tài khoản</button>
            </form>
        </div>
        <script src="./js/main.js"></script>
</body>
</html>