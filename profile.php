<?php 
require 'inc/header.php';

$id = $_GET['id'];

$countUserPost = $user->CountUserPost($id);
$countUserFollowers = $user->CountUserFollowers($id);
$countUserFollowing = $user->CountUserFollowing($id);

$getProfile = $user->getProfile($id);
$_SESSION['url'] = $_SERVER['REQUEST_URI'];
?>
    
    <section class="tags" aria-labelledby="tag-label">
        <div class="container">
            <!-- ================== breadcrumbs =================== -->
            <div class="wrapper__breadcrumbs">
              <div class="breadcrumbs">
                <ul>
                  <li><a href="./index.php" class="a__breadscrumbs">
                  <i class="fas fa-home icon"></i>
                  <p>Trang chủ</p>
                  </a></li>
                  <li class="active"><a class="a__breadscrumbs">
                    <i class="fa-solid fa-user icon"></i>
                  <p>Trang cá nhân</p>
                  </a></li>
                </ul>
              </div>
            </div>
        </div>
    </section>
        <div class="container-profile">
            <!-- ================== END OF breadcrumbs =================== -->
            <div class="profile">
                <?php foreach($getProfile as $row) {?>
                <div class="profile-image">
                    <img src="<?=ROOT_URL?>assets/images/avatar/<?=$row['UserImage']?>" alt="avatar" class="profile-img">
                </div>
                <div class="profile-user-settings">
                    <h1 class="profile-user-name"><?=$row['UserName']?></h1>
                </div>
                <?php 
                    if(@$account['UserId'] == $id){ ?>
                        <button class="following__button profile-btn" data-userid = "<?=$row['UserId']?>" style="display:none">Theo dõi</button>
                    <?php }else if(isset($account['UserId']) && $account['UserId'] != $id) {
                        $follow = $user->checkUserFollow($account['UserId'], $row['UserId']);
                        if($follow->num_rows > 0){ ?>
                        <button class="following__button profile-btn" id="unfollow" data-follower = "<?=$account['UserId']?>" data-userid = "<?=$row['UserId']?>">Hủy theo dõi</button>
                        <?php } else { ?>
                        <button class="following__button profile-btn" id="follow" data-follower ="<?=$account['UserId']?>" data-userid = "<?=$row['UserId']?>">Theo dõi</button>
                        <?php } 
                    }else {?>
                        <a href="<?=ROOT_URL?>login.php">
                        <button class="following__button profile-btn" data-userid = "<?=$row['UserId']?>">Theo dõi</button>
                        </a>
                    <?php } ?>
                <div class="profile-stats">
                    <ul>
                        <li><span class="profile-stats-count">Bài đăng: <p style="display: inline"><?=$countUserPost?></p></span></li>
                        <li><span class="profile-stats-count">Người theo dõi: <p style="display: inline"><?=$countUserFollowers?></p></span></li>
                        <li><span class="profile-stats-count">Đang theo dõi: <p style="display: inline"><?=$countUserFollowing?></p></span></li>
                    </ul>
                </div>
                <div class="profile-bio">
                    <p><?=$row['UserBio'] ?></p>
                    <!-- <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit 📷✈️🏕️</p> -->
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="container-profile" style="padding-bottom: 7rem;">
            <div class="gallery">
            <?php 
            $getPostProfile = $user->getPostProfile($id);
            if($getPostProfile->num_rows > 0 ){
            foreach($getPostProfile as $row) { ?>
            <div class="gallery">
                <div class="gallery-item" tabindex="0">
                    <a href="<?=ROOT_URL?>post.php?id=<?=$row['PostId']?>" style="all: unset;">
                    <img src="<?=ROOT_URL?>assets/images/thumbnail/<?=$row['PostThumbnail']?>" alt="Thumbnail" class="gallery-image">
                    <div class="gallery-item-info">
                        <ul>
                        <?php 
                            if(isset($account['UserId'])){
                              $like = $user->getPostLike($account['UserId'], $row['PostId']);
                              if($like->num_rows > 0){ ?>
                              <li class="gallery-item-likes"><i class="fa-solid fa-heart" style="color:#F14141"></i>
                              <?php 
                              $temp = $user->CountPostLike($row['PostId']);
                              $countLike = $temp->fetch_assoc();
                              echo $countLike['COUNT(UserId)']; ?></li>
                              <?php } else { ?>
                              <li class="gallery-item-likes"><i class="fa-regular fa-heart"></i>
                              <?php 
                              $temp = $user->CountPostLike($row['PostId']);
                              $countLike = $temp->fetch_assoc();
                              echo $countLike['COUNT(UserId)']; ?></li>
                              <?php } 
                            }else { ?>
                              <li class="gallery-item-likes"><i class="fa-regular fa-heart"></i>
                              <?php 
                              $temp = $user->CountPostLike($row['PostId']);
                              $countLike = $temp->fetch_assoc();
                              echo $countLike['COUNT(UserId)']; ?></li>
                            <?php } ?>
                            <li class="gallery-item-comments"><i class="fa-regular fa-eye"></i><?=$row['PostView']?></li>
                        </ul>
                    </div>
                    <span><?=$row['PostTitle']?></span>
                </a>
                </div>
            </div>
            <?php }
            }?>
            </div>
            <?php if($getPostProfile->num_rows == 0 ) {?>
            <div>
                <h2 style="margin-top: 5rem; color: var(--bg-carolina-blue); display: flex; justify-content: center;">Người dùng này vẫn chưa có bài đăng nào</h2>
            </div>
            <?php } ?>
        </div>
<?php 
require 'inc/footer.php';
?>
      <script src="./js/main.js"></script>
</body>
</html>