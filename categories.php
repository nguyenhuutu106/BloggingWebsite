<?php 
require 'inc/header.php';
$_SESSION['url'] = $_SERVER['REQUEST_URI'];
?>
    <section class="tags" aria-labelledby="tag-label">
        <div class="container">
            <!-- ================== breadcrumbs =================== -->
            <div class="wrapper__breadcrumbs">
              <div class="breadcrumbs">
                <ul>
                  <li><a href="<?=ROOT_URL?>index.php" class="a__breadscrumbs">
                  <i class="fas fa-home icon"></i>
                  <p>Trang chủ</p>
                  </a></li>
                  <li class="active"><a class="a__breadscrumbs">
                  <i class="fa-solid fa-list icon"></i>
                  <p>Thể loại</p>
                  </a></li>
                </ul>
              </div>
            </div>
            <!-- ================== END OF breadcrumbs =================== -->

          <h2 class="headline headline-2 section-title" id="tag-label">
            <span class="span">Thể Loại</span>
          </h2>


          <ul class="grid-list">

          <?php 
          $fetchCategory = $user->getAllCategory();
          foreach ($fetchCategory as $row) { ?>
            <li>
              <a href="<?=ROOT_URL?>categories_post.php?id=<?=$row['CategoryId']; ?>">
                <div class="tag-btn">
                  <img src="<?php ROOT_URL ?>assets/images/thumbnail/<?=$row['CategoryImage']?>" loading="lazy" alt="Travel" style="height: 230px">

                  <div class="categories-overlay">
                    <div class="categories-text-overlay"><?=$row['CategoryName']?></div>
                  </div>
                </div>
              </a>
            </li>

          <?php } ?>
          </ul>

        </div>
      </section>
      <!-- ================== END OF CATEGORIES ================================ -->
<?php 
require 'inc/footer.php';
?>
      <script src="./js/main.js"></script>
</body>
</html>