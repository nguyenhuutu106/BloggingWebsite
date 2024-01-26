<?php 
$page = 'manage_posts';
require 'inc/sidebar.php';

?>
    <div class="main-content">    
        <main>
            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">
                        <!--		Show Numbers Of Rows 		-->
                        <div class="form-group"> 
                            <select class  ="form-control" name="state" id="maxRows" style="
                            background-color: white;
                            color: black;
                            border: 1px solid;">
                                <option value="5000">Hiển thị hết data</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="70">70</option>
                                <option value="100">100</option>
                               </select>
                         </div>
       
                        <div class="add">
                            <a href="./add_post.php"><button>Thêm bài viết</button></a>
                        </div>

                        <div class="browse">
                           <input type="text" placeholder="Tìm kiếm" class="record-search" id="record-search" >
                        </div>
                    </div>

                    <div>
                        <table width="100%" id="table-id">
                            <thead>
                                <tr>
                                    <th><span class="las la-sort"></span> ID</th>
                                    <th><span class="las la-sort"></span> Tên bài viết</th>
                                    <th><span class="las la-sort"></span> Người đăng</th>
                                    <th><span class="las la-sort"></span> Thể loại bài viết</th>
                                    <th><span class="las la-sort"></span> Thời gian bài viết</th>
                                    <th><span class="las la-sort"></span> Ảnh bìa bài viết</th>
                                    <th><span class="las la-sort"></span> Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            <?php
                            $getAllPost = $admin->getAllPost();
                            while($fetchPost = $getAllPost->fetch_assoc()) {
                            ?>		
                                <tr id="row_<?=$fetchPost['PostId'] ?>">
                                    <td><?= $fetchPost['PostId']?></td>
                                    <td><div style="max-height:100px; min-height: 100px;">
                                    <?= $fetchPost['PostTitle']?>
                                    </div>
                                    </td>
                                    <td>
                                    <?= $fetchPost['UserName']?>
                                    </td>
                                    <td>
                                        <?php
                                        $sqlQuery = "SELECT categories.CategoryName 
                                        FROM `posts`, `categories`, `post_category` 
                                        WHERE posts.PostId = post_category.PostId AND categories.CategoryId = post_category.CategoryId AND posts.PostId = ".$fetchPost['PostId'].";";
                                        $stmt = $db->prepare($sqlQuery);
                                        $stmt->execute();			
                                        $categories = $stmt->get_result();
                                        while($fetchCategories = $categories->fetch_assoc()) { ?>
                                        <p style="margin-bottom: 2px">-<?= $fetchCategories['CategoryName'] ?></p>
                                        <?php } ?>
                                    </td>
                                    <td>
                                    <?= $fetchPost['PostDate']?>
                                    </td>
                                    <td>
                                        <img src="<?=ROOT_URL?>assets/images/thumbnail/<?php echo $fetchPost['PostThumbnail']; ?>" alt="thumbnail" style="max-height: 145px; min-height: 145px;">
                                    </td>
                                    <td>
                                        <div class="td-button">
                                            <a href="./edit_post.php?id=<?php echo $fetchPost['PostId']; ?>" class="btn sm">Sửa</a>
                                            <button class="btn sm danger deleteId" data-postid = "<?=$fetchPost['PostId']?>">Xóa</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            
                <!-- <div class='pagination-container' >  -->
                    <div style="display: block;"></div>
                      <ul class="table-pagination">
                        <li data-page="prev" >
                            <span> < <span class="sr-only">(current)</span></span></li>
                       
                        <li data-page="next" id="prev">
                            <span> > <span class="sr-only">(current)</span></span></li>
                      </ul>
                </div>
            </div>
     
        </main>
        
    </div>

    <!-- MODAL DELETE -->
  
    <div id="modal" class="modal-container"> 
        <div class="modal-content"> 
  
            <h2 style="color: black">Xác nhận xóa</h2> 
            <p class="confirmation-message"> 
                Bạn có chắc chắn muốn xóa bài viết này? 
            </p> 
  
            <div class="button-container"> 
                <button id="cancelBtn" 
                    class="btn cancel-button"> 
                    Hủy 
                </button> 
                <button id="deletePostBtn" 
                    class="btn delete-button"> 
                    Xóa 
                </button> 
            </div> 
        </div> 
    </div> 

    <script src="./js/main.js"></script>
    <script src="./js/ajax.js"></script>
</body>
</html>