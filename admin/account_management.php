<?php 
$page = 'account_management';
require 'inc/sidebar.php';

$editAccountMessage = '';

$id = $account['UserId'];
$fetchUserId = $admin->getUserId($id);
while ($result = $fetchUserId->fetch_assoc()) { 
   $userName = $result['UserName'];
   $userImageValue = $result['UserImage'];
   $userBio = $result['UserBio'];
   $userEmail = $result['UserEmail'];
}

if(isset($_POST['submit']) && $_POST['randcheck'] == $_SESSION['rand']){
	$userName = $_POST["userName"];
    $userImageValue = $_POST["userImageValue"];
    @$userBio = htmlspecialchars($_POST['userBio']);
    $userEmail = $_POST['userEmail'];

    if (strlen($userName) == 0){
        $editAccountMessage = 'Không được để trống tên hiển thị';
    } else if (!$userImageValue){
        $editAccountMessage = 'Vui lòng chọn ảnh bìa cho tài khoản!';
    } else if (strlen($userEmail) == 0){
        $editAccountMessage = 'Vui lòng nhập Email cho tài khoản!';
    } else if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $editAccountMessage = "Email được nhập không hợp lệ";
    }
    if($editAccountMessage == '') {	
        $admin->name = $_POST["userName"];
        $admin->image = $_POST["userImageValue"];
        $admin->bio = $_POST["userBio"];
        $admin->email = $_POST["userEmail"];

        if($admin->editAccount($id) == "email"){
            $editAccountMessage = "Email được nhập đã bị trùng với Email đã có trên hệ thống!";
        }else if($admin->editAccount($id)){
            $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc sửa tài khoản!';
        } else if ($admin->editAccount($id) == "nodata"){
            $editAccountMessage = "Có lỗi xảy ra trong việc thực hiện sửa tài khoản!";
        }
    }
}
?>
    <div class="admin-management-form" style="padding-top: 2rem">
            <h2 style="color: black;">Thông Tin Tài Khoản</h2>
            
            <div style="width: 80%; display: flex; justify-content: space-between;">
                <button type="submit" class="btn account_edit" style="margin-bottom: 3rem;"><i class="fa-solid fa-pen-to-square" style="margin-right: 5px;"></i>CẬP NHẬP THÔNG TIN</button>
                <a href="./change_password.php" class="btn account_password" style="margin-bottom: 3rem;"><i class="fa-solid fa-pen-to-square" style="margin-right: 5px;"></i>ĐỔI MẬT KHẨU</a>
            </div>
            <?php if ($editAccountMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $editAccountMessage ?>
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
                    <label for="userDisplayName">Tên hiển thị</label>
                    <input type="text" placeholder="Tên hiển thị" name="userName" value="<?php echo isset($userName)? $userName: "" ?>" readonly>
                </div>
                <div class="form__control">
                    <label for="thumbnail">Ảnh đại diện</label>
                    <div class="btn up_image" style="display: none">
                        <i class="fa-solid fa-plus"></i>
                        <span>Đăng tải ảnh đại diện</span>
                        <input type="file" accept="image/*" name="userImage" id="thumbnail" onchange="loadFile(event); getFileName(event); ">
                    </div>
                    <input type="hidden" id="imageValue" name="userImageValue" <?php if(isset($userImageValue)) echo "value =".$userImageValue."" ?>>
                    <p><img id="output" style="width:50vh; vertical-align: middle;" <?php if(!empty($userImageValue)) echo "src='".ROOT_URL."assets/images/avatar/".$userImageValue."'";?>/></p>
                </div>
                <div class="form__control">
                    <label for="userDescribe">Mô tả tài khoản</label>
                    <textarea placeholder="Mô tả tài khoản" name="userBio" readonly><?php echo isset($userBio)? $userBio: "" ?></textarea>
                </div>
                <div class="form__control">
                    <label for="userContent">Email</label>
                    <input type="email" id="email" placeholder="Email" name="userEmail" value="<?php echo isset($userEmail)? $userEmail: "" ?>" readonly>
                    <p id="result"></p>
                </div>
                <div>
                  <button class="btn sm edit" style="display:none; width:7rem; text-align:center; margin-right: 2rem; padding-top: 0.8rem;
                  padding-bottom: 0.9rem; color: white" name="submit">Lưu</button>
                  <button class="btn sm danger" style="display:none; width:7rem; text-align:center">Hủy</button>
                </div>
            </form>
        </div>
    <script src="./js/main.js"></script>
</body>
</html>