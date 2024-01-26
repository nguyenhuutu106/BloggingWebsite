<?php 
$page = 'account_management';
require 'inc/sidebar.php';

$changePasswordMessage = '';

$id = $account['UserId'];
$fetchUserId = $admin->getUserId($id);
while ($result = $fetchUserId->fetch_assoc()) { 
   $userAccount = $result['UserAccount'];
}

if(isset($_POST['submit']) && $_POST['randcheck'] == $_SESSION['rand']){
    $userOldPassword = $_POST["userOldPassword"];
    $userPassword = $_POST["userPassword"];
    $userRePassword = $_POST["userRePassword"];

    if (strlen($userOldPassword) < 5){
        $changePasswordMessage = 'Mật khẩu cũ của bạn không hợp lệ!';
    } else if (strlen($userPassword) < 5){
        $changePasswordMessage = 'Mật khẩu mới của bạn cần tối thiểu 5 ký tự!';
    } else if ($userRePassword != $userPassword){
        $changePasswordMessage = 'mật khẩu mới được nhập lại của bạn không trùng khớp với mật khẩu mới đã được nhập!';
    } else if ($userRePassword == $userOldPassword){
        $changePasswordMessage = 'mật khẩu mới không được trùng khớp với mật khẩu cũ!';
    }
    if($changePasswordMessage == '') {	
        $admin->oldPassword = $_POST["userOldPassword"];
        $admin->password = $_POST["userPassword"];
        $admin->rePassword = $_POST["userRePassword"];

        if($admin->changePassword($id) == "password"){
            $changePasswordMessage = "Mật khẩu cũ của bạn không chính xác!";
        }else if($admin->changePassword($id)){
            $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc đổi mật khẩu!';
        } else if ($admin->changePassword($id) == "nodata"){
            $changePasswordMessage = "Có lỗi xảy ra trong việc đổi mật khẩu!";
        }
    }
}
?>
    <div class="admin-management-form" style="padding-bottom: 134px">
        <a href="account_management.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Đổi Mật Khẩu</h2>
            
            <?php if ($changePasswordMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $changePasswordMessage ?>
                </div>
           <?php } else if (isset($_SESSION['success_message'])) { ?>
                <div class="alert__message success" style="width: 80%">
                        <?= $_SESSION['success_message'] ?>
                </div>
            <?php  unset($_SESSION['success_message']); }?> 
            <form action="<?php echo $_SERVER['PHP_SELF']?>"  method="POST">
            <!-- Stop resubmission when refreshing page -->
                <?php
                $rand=rand();
                $_SESSION['rand']=$rand;
                ?>
                <div class="form__control">
                    <input type="hidden" name="randcheck" value="<?=$rand; ?>" >
                    <label for="userAccount">Tên đăng nhập</label>
                    <input type="text" name="userAccount" value="<?=$userAccount ?>" style="width: 50%;" readonly>
                </div>
                <div class="form__control">
                    <label for="userName">Nhập mật khẩu cũ</label>
                    <div>
                        <input id="new-password-field" type="password" placeholder="Nhập mật khẩu cũ" style="width: 50%;" name="userOldPassword">
                        <span toggle="#new-password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                </div>
                <div class="form__control">
                    <label for="userName">Nhập mật khẩu mới</label>
                    <div>
                        <input id="password-field" type="password" placeholder="Nhập mật khẩu mới" style="width: 50%;" name="userPassword">
                        <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                </div>
                <div class="form__control">
                    <label for="userName">Nhập lại mật khẩu mới</label>
                    <div>
                        <input id="re-password-field" type="password" placeholder="Nhập lại mật khẩu mới" style="width: 50%;" name="userRePassword">
                        <span toggle="#re-password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                </div>
                <button class="btn admin_btn" style="width:7rem; text-align:center; padding-top: 0.8rem;
                  padding-bottom: 0.9rem; color: white" name="submit">Lưu</button>
            </form>
        </div>
    <script src="./js/main.js"></script>
</body>
</html>