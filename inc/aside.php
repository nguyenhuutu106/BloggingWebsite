<div class="post-aside grid-list">

<div class="card aside-card">

  <h3 class="headline headline-2 aside-title">
    <!-- <span class="span">Popular Posts</span> -->
    <nav>
      <ul>
        <li id="tab1" class="inactive">Top View</li>
        <li id="tab2" >Top Hot</li>
      </ul>
    </nav>
  </h3>

  <ul class="popular-list" id="tab1-content">

  <?php
  $countTopView = $user->CountTopView();
  foreach($countTopView as $top){
  ?>
    <li>
      <div class="popular-card">

        <figure class="card-banner img-holder">
          <img src="<?= ROOT_URL?>assets/images/thumbnail/<?=$top['PostThumbnail']?>" width="64" height="64" loading="lazy"
            alt="<?=$top['PostTitle']?>" class="img-cover">
        </figure>

        <div class="card-content">

          <h4 class="headline headline-4 card-title">
            <a href="<?= ROOT_URL?>post.php?id=<?=$top['PostId']?>" class="link hover-2"><?=$top['PostTitle']?></a>
          </h4>

          <div class="warpper">
            <span class="span__popular-icon" style="font-size: .9rem;"><i class="fa-regular fa-eye" style="margin-right: 3px"></i><?=$top['View']?></span>

            <time class="publish-date"><?=$top['PostDate']?></time>
          </div>

        </div>

      </div>
    </li>
  <?php } ?>
  </ul>

  <ul class="popular-list" id="tab2-content">

  <?php
  $countTopView = $user->CountHotView();
  foreach($countTopView as $top){
  ?>
    <li>
      <div class="popular-card">

        <figure class="card-banner img-holder">
          <img src="<?= ROOT_URL?>assets/images/thumbnail/<?=$top['PostThumbnail']?>" width="64" height="64" loading="lazy"
            alt="<?=$top['PostTitle']?>" class="img-cover">
        </figure>

        <div class="card-content">

          <h4 class="headline headline-4 card-title">
            <a href="<?= ROOT_URL?>post.php?id=<?=$top['PostId']?>" class="link hover-2"><?=$top['PostTitle']?></a>
          </h4>

          <div class="warpper">
            <span class="span__popular-icon" style="font-size: .9rem;"><i class="fa-regular fa-eye" style="margin-right: 3px"></i><?=$top['View']?></span>

            <time class="publish-date"><?=$top['PostDate']?></time>
          </div>

        </div>

      </div>
    </li>
  <?php } ?>
  </ul>
</div>
</div>