<?php 
$page = 'manage_users';
require 'inc/sidebar.php';

$editUserMessage = '';

$id = $_GET['id'];
$fetchUserId = $admin->getUserId($id);
while ($result = $fetchUserId->fetch_assoc()) { 
   $userName = $result['UserName'];
   $userImageValue = $result['UserImage'];
   $userBio = $result['UserBio'];
   $userRole = $result['UserRole'];
}

if(isset($_POST['submit']) && $_POST['randcheck'] == $_SESSION['rand']){
	$userName = $_POST["userName"];
    $userImageValue = $_POST["userImageValue"];
    @$userBio = htmlspecialchars($_POST['userBio']);
    $userRole = $_POST['userRole'];

    if (strlen($userName) == 0){
        $editUserMessage = 'Không được để trống tên hiển thị';
    } else if (!$userImageValue){
        $editUserMessage = 'Vui lòng chọn ảnh bìa cho tài khoản!';
    }
    if($editUserMessage == '') {	
        $admin->name = $_POST["userName"];
        $admin->image = $_POST["userImageValue"];
        $admin->bio = $_POST["userBio"];
        $admin->role = $_POST["userRole"];

        if($admin->editUser($id)){
            $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc sửa tài khoản!';
          } else if ($admin->editUser($id) == "nodata"){
            $editUserMessage = "Có lỗi xảy ra trong việc thực hiện sửa tài khoản!";
        }
    }
}
?>
    <div class="admin-management-form">
        <a href="manage_users.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Sửa Tài Khoản</h2>
            <?php if ($editUserMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $editUserMessage ?>
                </div>
           <?php } else if (isset($_SESSION['success_message'])) { ?>
                <div class="alert__message success" style="width: 80%">
                        <?= $_SESSION['success_message'] ?>
                </div>
            <?php  unset($_SESSION['success_message']); }?> 
            <form action="<?php echo $_SERVER['PHP_SELF']."?id=".$id; ?>"  method="POST" enctype="multipart/form-data">
            <!-- Stop resubmission when refreshing page -->
                <?php
                $rand=rand();
                $_SESSION['rand']=$rand;
                ?>
                <div class="form__control">
                    <input type="hidden" name="randcheck" value="<?=$rand; ?>" >
                    <label for="userDisplayName">Tên hiển thị</label>
                    <input type="text" placeholder="Tên hiển thị" name="userName" value="<?php echo isset($userName)? $userName: "" ?>">
                </div>
                <div class="form__control">
                    <label for="thumbnail">Ảnh đại diện</label>
                    <div class="btn up_image">
                        <i class="fa-solid fa-plus"></i>
                        <span>Đăng tải ảnh đại diện</span>
                        <input type="file" accept="image/*" name="userImage" id="thumbnail"  onchange="loadFile(event); getFileName(event); ">
                    </div>
                    <input type="hidden" id="imageValue" name="userImageValue" <?php if(isset($userImageValue)) echo "value =".$userImageValue."" ?>>
                    <p><img id="output" style="width:50vh; vertical-align: middle;" <?php if(!empty($userImageValue)) echo "src='".ROOT_URL."assets/images/avatar/".$userImageValue."'";?>/></p>
                </div>
                <div class="form__control">
                    <label for="userDescribe">Mô tả tài khoản</label>
                    <textarea placeholder="Mô tả tài khoản" name="userBio"><?php echo isset($userBio)? $userBio: "" ?></textarea>
                </div>
                <div class="form__control">
                    <label for="postCategory">Quyền tài khoản</label>
                    <select name="userRole">
                        <?php if($userRole == 0){
                            echo "<option value=".$userRole." selected >User</option>";
                            echo "<option value=". 1 .">Admin</option>";
                        } else {
                            echo "<option value=". 0 ." >User</option>";
                            echo "<option value=".$userRole." selected >Admin</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn_admin" name="submit">Sửa tài khoản</button>
            </form>
        </div>
        <script src="./js/main.js"></script>
</body>
</html>