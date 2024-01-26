<?php 
require 'inc/header.php';
?>
    <section class="section recent-post" id="recent" aria-labelledby="recent-label">
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
                    <i class="fa-solid fa-thumbs-up icon"></i>
                  <p>Bài viết đã thích</p>
                  </a></li>
                </ul>
              </div>
            </div>
            <!-- ================== END OF breadcrumbs =================== -->

            <h2 class="headline headline-2 section-title">
              <span class="span">Bài viết đã thích</span>
            </h2>
            <?php
            $pageSize = 7;
            $startRow = 0; 
            $pageNum = 1; 
            $offset = 2; 
            if(isset($_GET['pageNum']) == true) $pageNum = $_GET['pageNum'];
            $startRow = ($pageNum-1)*$pageSize;
            $result = $user->getPostByUserLike($account['UserId'], $startRow, $pageSize); 
            if($result->num_rows == 0) { ?>
              <h1 class="headline headline-1 section-title">
                <span class="span">Bạn vẫn chưa thích bài viết nào</span>
              </h1>
              <?php } else { ?>
              <ul class="grid-list">

              <?php foreach($result as $row) { ?>
              <li>
                <div class="recent-post-card">
                  <a href="<?=ROOT_URL?>post.php?id=<?=$row['PostId']?>" style="display: inline-block;">
                  <figure class="card-banner img-holder" style="--width: 271; --height: 258;">
                    <img src="<?=ROOT_URL?>assets/images/thumbnail/<?=$row['PostThumbnail']?>" width="271" height="271" loading="lazy"
                      alt="<?=$row['PostDescription']?>" class="img-cover" style="min-height: 271px; max-height: 271px;">
                  </figure>
                  </a>

                  <div class="card-content">
                    <?php 
                    $fetchCategoryId = $user->getPostCategory($row['PostId']); 
                    foreach($fetchCategoryId as $category)
                    { ?>
                      <a href="<?=ROOT_URL?>categories_post.php?id=<?=$category['CategoryId']?>" class="card-badge"><?=$category['CategoryName']?></a>
                    <?php } ?> 

                    <h3 class="headline headline-3 card-title">
                      <a href="<?=ROOT_URL?>post.php?id=<?=$row['PostId']?>" class="link hover-2"><?=$row['PostTitle']?></a>
                    </h3>
                    <div class="post__author">
                      <a href="<?=ROOT_URL?>profile.php?id=<?=$row['UserId']?>">
                      <div class="post__author-avatar">
                        <img src="<?=ROOT_URL?>assets/images/avatar/<?=$row['UserImage']?>" alt="avatar">
                      </div>
                      <div class="post__author-info">
                        <h5>Đăng bởi: <?=$row['UserName']?></h5>
                      </a>
                          <div class="wrapper">
                            <time class="publish-date" datetime="<?=$row['PostDate']?>"><?=$row['PostDate']?></time>
                          </div>
                      </div>
                    </div>
                    <p class="card-text">
                      <?=$row['PostDescription']?>
                    </p>

                    <div class="card-wrapper">
                      <div class="wrapper">
                        <span class="span"><i class="fa-regular fa-eye" style="font-size:1.1rem; margin-right: 5px"></i></ion-icon> <?=$row['PostView']?> Lượt xem</span>
                        <div style="display: flex; margin-left: 170px;">
                            <?php 
                            $like = $user->getPostLike($account['UserId'], $row['PostId']);
                            if($like->num_rows > 0){ ?>
                              <span class="span"><ion-icon name="heart" color="danger"></ion-icon><?php 
                              $temp = $user->CountPostLike($row['PostId']);
                              $countLike = $temp->fetch_assoc();
                              echo $countLike['COUNT(UserId)']; ?></span>
                            <?php } else { ?>
                              <span class="span"><ion-icon name="heart-outline"></ion-icon><?php 
                              $temp = $user->CountPostLike($row['PostId']);
                              $countLike = $temp->fetch_assoc();
                              echo $countLike['COUNT(UserId)']; ?></span>
                            <?php } ?>
                            <span class="span" style="margin-left: 15px"><ion-icon name="chatbox"></ion-icon></ion-icon><?php 
                              $temp = $user->CountPostComment($row['PostId']);
                              $countComment = $temp->fetch_assoc();
                              echo $countComment['COUNT(CommentId)']; ?></span>
                      </div>
                    </div>

                  </div>

                </div>
              </li>
              <?php }?>
            </ul>
            <?php } ?>
            <?php
            $totalRecord = $user->getPostByUserLikeRowCount($account['UserId']);
            $totalRow = ceil($totalRecord/$pageSize);
            $from = $pageNum - $offset; if($from<1) $from = 1;
            $to = $pageSize + $offset; if($to > $totalRow) $to = $totalRow;
                        
            $pageNext = $pageNum + 1;
            $pagePrev = $pageNum - 1;

            if($totalRecord > 7) {  ?>
              <nav aria-label="pagination" class="pagination">
  
              <?php if($pageNum > 1) { ?>
                <a href='<?=ROOT_URL?>liked_post.php?'  class="pagination-btn" aria-label="previous page">
                <i class="fa-solid fa-backward"></i>
                </a>
  
                <a href='<?=ROOT_URL?>liked_post.php?&pageNum=<?=$pagePrev?>' class="pagination-btn" aria-label="previous page">
                <i class="fa-solid fa-arrow-left"></i>
                </a>
              <?php }
              for($i = $from; $i <= $to; $i++) {
                if($i == $pageNum){ ?>
                  <a href='<?=ROOT_URL?>liked_post.php?&pageNum=<?=$i?>' class="pagination-btn active"><?=$i?></a>
                <?php } else { ?>
                  <a href='liked_post.php?&pageNum=<?=$i?>' class="pagination-btn"><?=$i?></a>
              <?php  }
              }
              if($pageNum < $totalRow){ ?>
              <a href='<?=ROOT_URL?>liked_post.php?&pageNum=<?=$pageNext?>' class="pagination-btn" aria-label="next page">
              <i class="fa-solid fa-arrow-right"></i>
              </a>
              <a href='<?=ROOT_URL?>liked_post.php?&pageNum=<?=$totalRow?>' class="pagination-btn" aria-label="next page">
              <i class="fa-solid fa-forward"></i>
              </a>
              <?php } ?>
              </nav>
            <?php }?>

          </div>
          <!-- =================== END RECENT POST + PAGINATION =====================  -->

          <!-- ======================= POPULAR POST ======================== -->
<?php 
require 'inc/aside.php';
?>
        <!-- ======================== END POPULAR POST =========================== -->
      </div>

<?php 
require 'inc/footer.php';
?>
</section>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="./js/main.js"></script>
</body>
</php>