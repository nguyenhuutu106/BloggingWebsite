<?php 
require 'inc/header.php';
?>
    <!-- ================== FOLLOWING USERS ================== -->
    <section class="section">
        <div class="container">

          <div class="post-main">
                        <!-- ================== breadcrumbs =================== -->
            <div class="wrapper__breadcrumbs">
              <div class="breadcrumbs">
                <ul>
                  <li><a href="<?=ROOT_URL?>index.php" class="a__breadscrumbs">
                  <i class="fas fa-home icon"></i>
                  <p>Trang chủ</p>
                  </a></li>
                  <li class="active"><a class="a__breadscrumbs">
                  <i class="fa-solid fa-user-plus icon"></i>
                  <p>Tài khoản đang theo dõi</p>
                  </a></li>
                </ul>
              </div>
            </div>
            <!-- ================== END OF breadcrumbs =================== -->

            <h2 class="headline headline-2 section-title">
              <span class="span">Các tài khoản đang theo dõi</span>
            </h2>
            <div class="container__following">
              <?php 
              $pageSize = 7;
              $startRow = 0; 
              $pageNum = 1; 
              $offset = 2; 
              if(isset($_GET['pageNum']) == true) $pageNum = $_GET['pageNum'];
              $startRow = ($pageNum-1)*$pageSize;
              $result = $user->getUserFollow($account['UserId'],$startRow, $pageSize);
              if($result->num_rows > 0){
              foreach($result as $row) { ?>  
              <div class="following__content">
                <div class="following__box">
                  <div class="following__user-container-detail">
                    <div class="following__user-detail">
                      <?php 
                      $getUserDetail = $user->getUserId($row['FollowedUserId']);
                      foreach($getUserDetail as $following){?>
                      <a class="following__user-avatar" href="<?=ROOT_URL?>profile.php?id=<?=$following['UserId']?>">
                        <img src="<?=ROOT_URL?>assets/images/avatar/<?=$following['UserImage']?>" alt="Avatar">
                      </a>
                      <div class="following__user-followed">
                        <div class="following__user-name">
                          <a href="<?=ROOT_URL?>profile.php?id=<?=$following['UserId']?>"><?=$following['UserName']?></a>
                        </div>
                        <div class="following__user-button">
                        <?php
                        $follow = $user->checkUserFollow($account['UserId'], $following['UserId']);
                        if($follow->num_rows > 0){ ?>
                        <button class="following__button" id="unfollow" data-follower = "<?=$account['UserId']?>" data-userid = "<?=$following['UserId']?>">Hủy theo dõi</button>
                        <?php } else { ?>
                        <button class="following__button" id="follow" data-follower ="<?=$account['UserId']?>" data-userid = "<?=$following['UserId']?>">Theo dõi</button>
                        <?php } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                    <?php }
                    $getPost = $user->getPostUserFollowing($following['UserId']);
                    if($getPost->num_rows > 0){
                    foreach($getPost as $post){?>
                  <div class="following__user-container-post">
                    <ul class="following__user-post">
                      <div class="following__user-detail-post">
                        <div class="following__user-imagine-post">
                          <a href="<?=ROOT_URL?>post.php?id=<?=$post['PostId']?>">
                            <img src="<?=ROOT_URL?>assets/images/thumbnail/<?=$post['PostThumbnail']?>" alt="Post">
                          </a>
                        </div>
                        <div class="following__user-name-post">
                          <a href="<?=ROOT_URL?>post.php?id=<?=$post['PostId']?>"><?=$post['PostTitle']?></a>
                        </div>
                      </div>
                    </ul>
                  </div>
                  <?php }
                  }else {?>
                  <h2 style="color: var(--text-carolina-blue);"> Người dùng này vẫn chưa có bài viết nào</h2>
                  <?php } ?>
                </div>
              </div>
            <?php }
            }else {?>
              <h1>Bạn vẫn chưa theo dõi người dùng nào</h1>
            <?php } ?>
            </div>

            <?php
            $totalRecord = $user->getUserFollowRowCount($account['UserId']);
            $totalRow = ceil($totalRecord/$pageSize);
            $from = $pageNum - $offset; if($from<1) $from = 1;
            $to = $pageSize + $offset; if($to > $totalRow) $to = $totalRow;
                        
            $pageNext = $pageNum + 1;
            $pagePrev = $pageNum - 1;

            if($totalRecord > 7) {  ?>
              <nav aria-label="pagination" class="pagination">
  
              <?php if($pageNum > 1) { ?>
                <a href='<?=ROOT_URL?>following.php?'  class="pagination-btn" aria-label="previous page">
                <i class="fa-solid fa-backward"></i>
                </a>
  
                <a href='<?=ROOT_URL?>following.php?&pageNum=<?=$pagePrev?>' class="pagination-btn" aria-label="previous page">
                <i class="fa-solid fa-arrow-left"></i>
                </a>
              <?php }
              for($i = $from; $i <= $to; $i++) {
                if($i == $pageNum){ ?>
                  <a href='<?=ROOT_URL?>following.php?&pageNum=<?=$i?>' class="pagination-btn active"><?=$i?></a>
                <?php } else { ?>
                  <a href='following.php?&pageNum=<?=$i?>' class="pagination-btn"><?=$i?></a>
              <?php  }
              }
              if($pageNum < $totalRow){ ?>
              <a href='<?=ROOT_URL?>following.php?&pageNum=<?=$pageNext?>' class="pagination-btn" aria-label="next page">
              <i class="fa-solid fa-arrow-right"></i>
              </a>
              <a href='<?=ROOT_URL?>following.php?&pageNum=<?=$totalRow?>' class="pagination-btn" aria-label="next page">
              <i class="fa-solid fa-forward"></i>
              </a>
              <?php } ?>
              </nav>
            <?php }?>

          </div>
          <!-- =================== END FOLLOWING USERS + PAGINATION =====================  -->
      </div>
        <!-- ======================== END POPULAR POST =========================== -->
<?php
require 'inc/footer.php';
?>
</section>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="./js/main.js"></script>
</body>
</html>