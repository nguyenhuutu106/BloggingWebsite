<?php 
$page = 'manage_categories';
require 'inc/sidebar.php';

$addCategoryMessage = "";
if(isset($_POST['submit']) && $_POST['randcheck']==$_SESSION['rand']){
    $categoryName = $_POST["categoryName"];
	$categoryContent = $_POST["categoryContent"];
    $categoryImageValue = $_POST["categoryImageValue"];

    if (strlen($categoryName) == 0){
        $addCategoryMessage = 'Vui lòng nhập tên thể loại!';
    } else if(strlen($categoryContent) == 0){
        $addCategoryMessage = 'Vui lòng nhập mô tả thể loại!';
    }  else if(!$categoryImageValue){
        $addCategoryMessage = 'Vui lòng đăng tải ảnh thể loại!';
    }
    if($addCategoryMessage == '') {	
        $admin->name = $_POST["categoryName"];
        $admin->content = $_POST["categoryContent"];
        $admin->image = $_POST["categoryImageValue"];

        if($admin->createCategory() == "name") {		
            $addCategoryMessage = "Thể loại ".$admin->name." đã tồn tại!";
        }else if($admin->createCategory()){
            $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc thêm thể loại!';
            $categoryName = "";
            $categoryContent = "";
            $categoryImageValue = "";
        }
    }
}
?>
        <div class="admin-management-form" style="padding-bottom: 179px">
        <a href="manage_categories.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Thêm Thể Loại</h2>
            <?php if ($addCategoryMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $addCategoryMessage ?>
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
                    <label for="categoryName">Tên thể loại</label>
                    <input type="text" placeholder="Tên thể loại" name="categoryName" value="<?php echo isset($categoryName)? $categoryName: "" ?>">
                </div>
                <div class="form__control">
                    <label for="thumbnail">Ảnh thể loại</label>
                    <div class="btn up_image">
                        <i class="fa-solid fa-plus"></i>
                        <span>Đăng tải ảnh thể loại</span>
                        <input type="file" accept="image/*" name="categoryImage" id="thumbnail"  onchange="loadFile(event); getFileName(event); ">
                    </div>
                    <input type="hidden" id="imageValue" name="categoryImageValue" <?php if(isset($categoryImageValue)) echo "value =".$categoryImageValue."" ?>>
                    <p><img id="output" style="width:50vh; vertical-align: middle;" <?php if(!empty($categoryImageValue)) echo "src='".ROOT_URL."assets/images/thumbnail/".$categoryImageValue."'";?>/></p>
                </div>
                <div class="form__control">
                    <label for="categoryDescrible">Mô tả thể loại</label>
                    <textarea name="categoryContent" placeholder="Nhập mô tả thể loại"><?php echo isset($categoryContent)? $categoryContent: "" ?></textarea>
                </div>
                <button type="submit" class="btn btn_admin" name="submit">Thêm thể loại</button>
            </form>
        </div>
        <script src="./js/main.js"></script>
</body>
</html>