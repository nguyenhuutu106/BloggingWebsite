<?php 
$page = 'manage_categories';
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
                            <a href="./add_category.php"><button>Thêm thể loại</button></a>
                        </div>

                        <div class="browse">
                           <input type="text" placeholder="Tìm kiếm" class="record-search" id="record-search">
                        </div>
                    </div>

                    <div>
                        <table width="100%" id="table-id">
                            <thead>
                                <tr>
                                    <th><span class="las la-sort"></span> ID</th>
                                    <th><span class="las la-sort"></span> Tên thể loại</th>
                                    <th><span class="las la-sort"></span> Mô tả thể loại</th>
                                    <th><span class="las la-sort"></span> Ảnh thể loại</th>
                                    <th><span class="las la-sort"></span> Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            <?php
                            $getAllCategory = $admin->getAllCategory();
                            while($fetchCategory = $getAllCategory->fetch_assoc()) {
                            ?>	
                                <tr>
                                    <td><?= $fetchCategory['CategoryId']?></td>
                                    <td>
                                        <?= $fetchCategory['CategoryName']?>
                                    </td>
                                    <td>
                                        <div style="max-height: 100px; min-height: 100px;">
                                        <?= $fetchCategory['CategoryContent']?>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="<?=ROOT_URL?>assets/images/thumbnail/<?php echo $fetchCategory['CategoryImage']; ?>" alt="thumbnail" style="max-height: 145px; min-height: 145px;">
                                    </td>
                                    <td>
                                        <div class="td-button">
                                            <?php
                                            if($fetchCategory['CategoryId'] == 0){ ?>
                                                <a href="./edit_category.php?id=<?php echo $fetchCategory['CategoryId']; ?>" class="btn sm">Sửa</a> 
                                        <?php } else { ?>
                                            <a href="./edit_category.php?id=<?php echo $fetchCategory['CategoryId']; ?>" class="btn sm">Sửa</a>
                                            <button class="btn sm danger deleteId" data-categoryid = "<?=$fetchCategory['CategoryId']?>">Xóa</button>
                                        <?php } ?>
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
                    Bạn có chắc chắn muốn xóa thể loại này? 
                </p> 
    
                <div class="button-container"> 
                    <button id="cancelBtn" 
                        class="btn cancel-button"> 
                        Hủy 
                    </button> 
                    <button id="deleteCategoryBtn" 
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