<?php 
$page = 'manage_users';
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
                            <a href="./add_user.php"><button>Thêm tài khoản</button></a>
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
                                    <th><span class="las la-sort"></span> Ảnh đại diện</th>
                                    <th><span class="las la-sort"></span> Mô tả tài khoản</th>
                                    <th><span class="las la-sort"></span> Tên hiển thị</th>
                                    <th><span class="las la-sort"></span> Quyền tài khoản</th>
                                    <th><span class="las la-sort"></span> Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            <?php
                            $getAllUser = $admin->getAllUser($account['UserId']);
                            while($fetchUser = $getAllUser->fetch_assoc()) {
                            ?>		
                                <tr>
                                    <td><?= $fetchUser['UserId']?></td>
                                    <td style="width: 200px;">
                                        <img src="<?=ROOT_URL?>assets/images/avatar/<?php echo $fetchUser['UserImage']; ?>" style="height:130px" alt="thumbnail">
                                    </td>
                                    <td>
                                        <div style="max-height:150px; min-height: 150px;">
                                        <?= $fetchUser['UserBio']?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= $fetchUser['UserName']?>
                                    </td>
                                    <td style="width: 120px;">
                                        <?php if($fetchUser['UserRole'] == 1){
                                            echo "<p> Admin </p>";
                                        }else {
                                            echo "<p> User </p>";
                                        } ?>
                                    </td>
                                    <td>
                                        <div class="td-button" style="margin: 7px 0 0 0;">
                                            <a href="./edit_user.php?id=<?php echo $fetchUser['UserId']; ?>" class="btn sm">Sửa</a>
                                            <button class="btn sm danger deleteId" data-userid = "<?=$fetchUser['UserId']?>">Xóa</button>
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
                    Bạn có chắc chắn muốn xóa tài khoản này? 
                </p> 
    
                <div class="button-container"> 
                    <button id="cancelBtn" 
                        class="btn cancel-button"> 
                        Hủy 
                    </button> 
                    <button id="deleteUserBtn" 
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