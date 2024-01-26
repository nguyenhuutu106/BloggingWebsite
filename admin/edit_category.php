<?php 
$page = 'manage_categories';
require 'inc/sidebar.php';

$editCategoryMessage = "";

$id = $_GET['id'];

$fetchCategoryId = $admin->getCategoryId($id);
while ($result = $fetchCategoryId->fetch_assoc()) { 
    $categoryName = $result['CategoryName'];
    $categoryImageValue = $result['CategoryImage'];
    $categoryContent = $result['CategoryContent'];
}
if(isset($_POST['submit']) && $_POST['randcheck']==$_SESSION['rand']){
    $categoryName = $_POST["categoryName"];
	$categoryContent = $_POST["categoryContent"];
    $categoryImageValue = $_POST["categoryImageValue"];

    if (strlen($categoryName) == 0){
        $editCategoryMessage = 'Vui lòng nhập tên thể loại!';
    } else if(strlen($categoryContent) == 0){
        $editCategoryMessage = 'Vui lòng nhập mô tả thể loại!';
    }  else if(!$categoryImageValue){
        $editCategoryMessage = 'Vui lòng đăng tải ảnh thể loại!';
    }
    if($editCategoryMessage == '') {	
        $admin->name = $_POST["categoryName"];
        $admin->content = $_POST["categoryContent"];
        $admin->image = $_POST["categoryImageValue"];

        if($admin->editCategory($id) == "name") {		
            $editCategoryMessage = "Thể loại ".$admin->name." đã tồn tại!";
        }else if ($admin->editCategory($id) == "nodata"){
            $editCategoryMessage = "Có lỗi trong việc sửa thể loại!";
        }else if ($admin->editCategory($id)){
            $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc sửa thể loại!';
        }
    }
}
?>
        <div class="admin-management-form">
        <a href="manage_categories.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Sửa Thể Loại</h2>
            <?php if ($editCategoryMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $editCategoryMessage ?>
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
                <button type="submit" class="btn btn_admin" name="submit">Sửa thể loại</button>
            </form>
        </div>
        <script src="./js/main.js"></script>
</body>
</html>