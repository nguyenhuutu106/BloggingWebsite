<?php
require 'inc/header.php';

$postId = $_GET['id'];

$sql = "UPDATE posts SET PostView = PostView + 1 WHERE PostId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $postId);
$stmt->execute();
$stmt->close();

$sql = "INSERT INTO day_view (`PostId`, `Day`, `DayViewCount`) VALUES (?, CURDATE(), 1) ON DUPLICATE KEY UPDATE DayViewCount = DayViewCount + 1;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $postId);
$stmt->execute();
$stmt->close();

$getPost = $user->getPostContent($postId);
$_SESSION['url'] = $_SERVER['REQUEST_URI'];
?>

<!-- POST -->
<section class="singlepost">
  <div class="container singlepost__container">
    <?php foreach ($getPost as $row) { ?>
      <h2 style="color: var(--bg-carolina-blue);"><?= $row['PostTitle'] ?></h2>
      <div class="post__author" style="flex-direction: row; justify-content: space-between;">
        <a href="<?= ROOT_URL ?>profile.php?id=<?= $row['UserId'] ?>">
          <div class="post__author-avatar">
            <img src="<?= ROOT_URL ?>assets/images/avatar/<?= $row['UserImage'] ?>" alt="avatar">
          </div>
          <div class="left">
            <div class="post__author-info">
              <h5>Đăng bởi: <?= $row['UserName'] ?></h5>
        </a>
        <div class="wrapper">
          <time class="publish-date"><?= $row['PostDate'] ?></time>
        </div>
      </div>
  </div>
  <div class="right">
    <span class="span" style="color: var(--text-white)"><i class="fa-regular fa-eye" style="font-size:1.1rem; margin-right: 5px"></i></ion-icon><?= $row['PostView'] ?> Lượt xem</span>
  </div>
  </div>
  <div class="singlepost__thumbnail">
    <img src="<?= ROOT_URL ?>assets/images/thumbnail/<?= $row['PostThumbnail'] ?>" alt="Thumbnail">
  </div>
  <div class="singlepost__des">
    <?= $row['PostDescription'] ?>
  </div>
  <div class="singlepost__content">
    <?= $row['PostContent'] ?>
  </div>
  <div class="singlepost__bottom">
    <div class="singlepost__reaction">
      <?php
      if (isset($account['UserId'])) {
        $like = $user->getPostLike($account['UserId'], $row['PostId']);
        if ($like->num_rows > 0) { ?>
          <div class="reaction_heart" id="unlike" data-postid="<?= $row['PostId'] ?>" data-userid="<?= $account['UserId'] ?>">
            <i class="fa-solid fa-heart" style="color:#F14141"></i><?php
                                                                    $temp = $user->CountPostLike($row['PostId']);
                                                                    $countLike = $temp->fetch_assoc(); ?>
            <span id="likeCount"><?= $countLike['COUNT(UserId)']; ?></span>
          </div>
        <?php } else { ?>
          <div class="reaction_heart" id="like" data-postid="<?= $row['PostId'] ?>" data-userid="<?= $account['UserId'] ?>">
            <i class="fa-regular fa-heart"></i><?php
                                                $temp = $user->CountPostLike($row['PostId']);
                                                $countLike = $temp->fetch_assoc(); ?>
            <span id="likeCount"><?= $countLike['COUNT(UserId)']; ?></span>
          </div>
        <?php }
      } else { ?>
        <div class="reaction_heart">
          <a href="<?= ROOT_URL ?>login.php">
            <i class="fa-regular fa-heart"></i><?php
                                                $temp = $user->CountPostLike($row['PostId']);
                                                $countLike = $temp->fetch_assoc(); ?>
            <span id="likeCount"><?= $countLike['COUNT(UserId)']; ?></span>
          </a>
        </div>
      <?php } ?>
      <div class="reaction_comment">
        <i class="fa-regular fa-comment"></i>
        <span>
          <?php $temp = $user->CountPostComment($row['PostId']);
          $countComment = $temp->fetch_assoc();
          echo $countComment['COUNT(CommentId)']; ?>
        </span>
      </div>
    </div>

    <h3 style="margin-bottom: 1.5rem;"><strong>Bình luận</strong></h3>
    <?php if (!isset($account['UserId'])) { ?>
      <a href="<?= ROOT_URL ?>login.php">
        <div style="cursor: pointer; text-align: center; margin-bottom: 1rem; position: relative;
                  display: flex;
                  flex-direction: column;
                  min-width: 0;
                  word-wrap: break-word;
                  background-color: #fff;
                  background-clip: initial;
                  border: 1px solid rgba(27,27,27,.125);
                  border-radius: 0.25rem;">
          <div style="flex: 1 1 auto;
                      min-height: 1px;
                      padding: 1.25rem;">
            <span style="color: black"><i aria-hidden="true" class="fa fa-comment-o"></i>Đăng nhập để bình luận</span>
          </div>
        </div>
      </a>

    <?php } else { ?>
      <div id="loadingSpinner" class="spinner"></div>

      <!-- ADD COMMENT -->
      <div class="add-comment-wrapper">
        <div class="add-comment">
          <img src="<?=ROOT_URL.'/assets/images/avatar/'.$account['UserImage']?>" alt="profile" class="comment-profile">

          <textarea placeholder="Nhập bình luận" style="resize: none;" id="comment-text"></textarea>

          <div class="comment-btn-wrapper">
            <button type="submit" class="comment-send" id='send-comment'>Bình luận</button>
          </div>
        </div>
      </div>
      <?php } ?>
      <div class="comments-container" id='show-comment'>

      </div>
  </div>

  <div class="singlepost__categories">
    <h3 style="color: var(--bg-carolina-blue); margin-top: 4rem;">Thể loại bài viết</h3>
    <?php
      $fetchCategoryId = $user->getPostCategory($row['PostId']);
      $i = 0;
      foreach ($fetchCategoryId as $category) {
        $i++ ?>
      <a href="<?= ROOT_URL ?>categories_post.php?id=<?= $category['CategoryId'] ?>" class="card-badge"><?= $category['CategoryName'] ?></a>
    <?php
        if ($i == 1) {
          $category1 = $category['CategoryId'];
        } else if ($i == 2) {
          $category2 = $category['CategoryId'];
        }else if ($i == 3) {
          $category3 = $category['CategoryId'];
        }
      }
      if ($i == 1) {
        $getPostSameCategories = $user->getPostSame1Categories($postId, $category1);
      } else if ($i == 2) {
        $getPostSameCategories = $user->getPostSame2Categories($postId, $category1, $category2);
      } else if ($i == 3) {
        $getPostSameCategories = $user->getPostSame3Categories($postId, $category1, $category2, $category3);
      } ?>
  </div>
  </div>
<?php } ?>
<div class="post__category">
  <h2 style="margin: 5rem 0 0 15rem; color: var(--bg-carolina-blue);">Các Bài Viết Cùng Thể Loại</h2>
</div>
<div class="slide-container">
  <swiper-container class="mySwiper">
    <?php foreach ($getPostSameCategories as $row) { ?>
      <swiper-slide>
        <a href="<?= ROOT_URL ?>post.php?id=<?= $row['PostId'] ?>">
          <img src="<?= ROOT_URL ?>assets/images/thumbnail/<?= $row['PostThumbnail'] ?>">
          <div class="slide-container-title">
            <span><?= $row['PostTitle'] ?></span>
          </div>
        </a>
      </swiper-slide>
    <?php } ?>
  </swiper-container>
  <div class="swiper-pagination"></div>
  <div class="swiper-button-next"></div>
  <div class="swiper-button-prev"></div>
</div>

</section>

<?php
require 'inc/footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-element-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<!-- Initialize Swiper -->
<script>
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 4,
    spaceBetween: 30,
    grabcursor: true,
    speed: 1000,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
    // when window width is >= 320px
    320: {
      slidesPerView: 2,
      spaceBetween: 20
    },
    // when window width is >= 480px
    480: {
      slidesPerView: 3,
      spaceBetween: 30
    },
    // when window width is >= 640px
    640: {
      slidesPerView: 4,
      spaceBetween: 40
    }
  }
  });
</script>
<script src="./js/main.js"></script>

<!-- SCRIPT COMMENT START -->
<script>
  function getComment() {
    const postId = '<?= $postId ?>';
    const data = {
      postId,
      action: 'getComment'
    }

    $(document).ready(function() {
      $.ajax({
        url: 'user_action.php',
        type: 'GET',
        dataType: "json",
        data: data,
        beforeSend: function() {
          $('#loadingSpinner').show();
        },
        success: function(response) {
          if (response?.success && response?.comments?.length) {
            const {
              comments
            } = response;

            $('#show-comment').empty().append([...comments].map((comment) => {
              const dateObject = new Date(comment.CommentCreateAt);
              const formattedDate = moment(dateObject).fromNow();


              return `
              <!-- COMMENTED -->
                <div class="comment">
                  <div class="comment-content">
                    <div class="comment-top">
                      <div class="left">
                        <a style="all: unset; display: contents; cursor: pointer;" href ="<?=ROOT_URL?>profile.php?id=${comment.UserId}"><img src="<?= ROOT_URL . '/assets/images/avatar' ?>/${comment.UserImage}" class="comment-profile-img">
                        <p class="comment-username">${comment.UserName}</p></a>
                        ${comment.UserId === +'<?= $account['UserId'] ?? '' ?> ' ? `
                        <p class="you">You</p>`
                        : ''}
                        ${comment.UserRole === 1 ? `
                          <p class="you" style="width: 4rem; background-color: hsl(0deg 77% 52% / 75%);">Admin</p>`
                        : ''}
                        <p class="comment-date">${formattedDate}</p>
                      </div>
                      <div class="right">
                      <?php if (isset($account['UserName'])){ ?>
                      ${comment.UserId !== +'<?= $account['UserId'] ?? '' ?> ' ? 
                        `<p
                          data-reply-to='${comment.UserName}'
                          id='reply-comment-${comment.CommentId}'
                          data-reply = '${comment.CommentId}'
                          onclick="replyComment(this)" 
                          data-id='${comment.CommentId}'
                          class="reply"
                        >
                          <i class="fa-solid fa-reply"></i>
                          Trả lời
                        </p>` 
                        : ''}
                        <?php } ?>
                        ${comment.UserId === +'<?= $account['UserId'] ?? '' ?> ' ? 
                          ` <p
                              data-edit='${comment.CommentText}'
                              data-id='${comment.CommentId}' 
                              id='edit-comment-${comment.CommentId}' 
                              onclick="editComment(this)" 
                              class="edit"
                            >
                              <i class="fa-solid fa-trash"></i>Sửa
                            </p>` 
                        : ''}
                          
                        ${comment.UserId === +'<?= $account['UserId'] ?? '' ?> ' || comment.UserId === +'<?= $row['UserId'] ?? '' ?>' 
                          ? `<p data-id='${comment.CommentId}' id='delete-comment-${comment.CommentId}' onclick="deleteComment(this)" class="delete"><i class="fa-solid fa-pen-to-square"></i>Xóa</p>`
                        : ''}
                      </div>
                    </div>
                    <div class="comment-text">${comment.CommentText}</div>
                  </div>
                </div>

                ${comment?.children?.length ? comment?.children?.map((child) => {
                  const dateObject = new Date(child.CommentCreateAt);
                  const formattedDate = moment(dateObject).fromNow();

                  return (`
                  <!--  REPLY COMMENTED -->
                  <div class="reply-details">
                    <div class="reply-container">
                      <div class="comment">
                        <div class="comment-content">
                          <div class="comment-top">
                            <div class="left">
                            <a style="all: unset; display: contents; cursor: pointer;" href ="<?=ROOT_URL?>profile.php?id=${child.UserId}"><img src="<?= ROOT_URL . '/assets/images/avatar' ?>/${child.UserImage}" alt="profile" class="comment-profile-img">
                              <p class="comment-username">${child.UserName}</p></a>
                              ${child.UserId === +'<?= $account['UserId'] ?? '' ?> ' ? `
                              <p class="you">You</p>`
                              : ''}
                              <p class="comment-date">${formattedDate}</p>
                            </div>
                            <div class="right">
                            <?php if (isset($account['UserName'])){ ?>
                            ${child.UserId !== +'<?= $account['UserId'] ?? '' ?> ' ? 
                            `<p
                              data-reply-to='${child.UserName}'
                              id='reply-comment-${child.CommentId}'
                              data-reply = '${child.CommentId}'
                              onclick="replyComment(this)" 
                              data-id='${comment.CommentId}'
                              class="reply"
                            >
                              <i class="fa-solid fa-reply"></i>
                              Trả lời
                            </p>` 
                            : ''}
                            <?php } ?>
                            ${child.UserId === +'<?= $account['UserId'] ?? '' ?> ' ? 
                              ` <p
                                  data-edit='${child.CommentText}'
                                  data-id='${child.CommentId}' 
                                  id='edit-comment-${child.CommentId}' 
                                  onclick="editComment(this)" 
                                  class="edit"
                                >
                                  <i class="fa-solid fa-trash"></i>Sửa
                                </p>` 
                            : ''}
                              
                            ${child.UserId === +'<?= $account['UserId'] ?? '' ?> ' || child.UserId === +'<?= $row['UserId'] ?? '' ?>' 
                              ? `<p data-id='${child.CommentId}' id='delete-comment-${child.CommentId}' onclick="deleteComment(this)" class="delete"><i class="fa-solid fa-pen-to-square"></i>Xóa</p>`
                            : ''}
                            </div>
                          </div>
                          <div class="comment-text"><span style="color:var(--bg-carolina-blue); font-weight: bold">@${child.Owner} </span>${child.CommentText}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  `)
                }).join('') : ''}
              `
            }).join(''))
          }else {
            $('.comment').remove(); 
          }
        },
        error: function(error) {
          // Xử lý khi có lỗi
          console.log('Error:', error);
          toastr.error(error.responseText, 'Lỗi')
        },
        complete: function() {
          setTimeout(() => {
            $('#loadingSpinner').hide();
          }, 500)
        }
      });

    })
  }

  // Load data comment
window.addEventListener('load', () => {
  getComment()
})

  // DELETE COMMENT
  const deleteComment = (event) => {
    const id = event.id;

    $(`#${id}`).ready((function() {
      const userId = '<?= $account['UserId'] ?? '' ?>';
      const commentId = $(`#${id}`).data('id');
      const postId = '<?= $postId ?>';

      const resultConfirm = window.confirm('Bạn có chắc muốn xóa bình luận');

      if (resultConfirm) {
        const data = {
          userId,
          commentId,
          postId,
          action: 'deleteComment'
        }

        $.ajax({
          url: 'user_action.php',
          type: 'POST',
          dataType: "json",
          data: data,
          beforeSend: function() {
            $('#loadingSpinner').show();
          },
          success: function(response) {
            if (response.success) {
              toastr.success('Xóa Comment thành công', 'Thành công');
              getComment();
            }

          },
          error: function(error) {
            // Xử lý khi có lỗi
            console.log('Error:', error);
            toastr.error(error.responseText, 'Lỗi')
          },
          complete: function() {
            setTimeout(() => {
              $('#loadingSpinner').hide();
            }, 500)
          }
        });
      }
    }))
  }

  // EDIT COMMENT
  const editComment = (event) => {
    const id = event.id;

    $(`#${id}`).ready((function() {
      const userId = '<?= $account['UserId'] ?? '' ?>';
      const commentId = $(`#${id}`).data('id');
      const value = $(`#${id}`).data('edit');
      const postId = '<?= $postId ?>';

      var $div = $(`#${id}`).closest('div[class="comment-top"]')
      var $text = $div.closest("div[class='comment-content']").find('.comment-text');

      $text.remove();
      $('.update-comment').remove();
      $('.add-reply').remove();

      $div.after(`
      <div class='update-comment'> 
        <textarea placeholder='edit your comment' id='comment-edit' style='resize: none;'>${value}</textarea>
        <button type='button' data-comment-id='${commentId}' onclick="handleEdit(this)" id='edit-comment' class='comment-update'>Sửa</button>
        <button type='button' onclick="handleCancelEdit(this)" class='edit-cancel'>Hủy</button>
      </div>`);

    }))
  }

  // HANDLE EDIT COMMENT

  const handleEdit = (event) => {
    const id = event.id;

    $(`#${id}`).ready((function() {
      const userId = '<?= $account['UserId'] ?? '' ?>';
      const commentId = $(`#${id}`).data('comment-id');
      const value = $(`#comment-edit`).val();
      const postId = '<?= $postId ?>';

      if (!value) {
        toastr.error('Vui lòng nhập nội dung comment', 'Lỗi')
        return;
      }

      const data = {
        commentId,
        value,
        action: 'editComment',
      }

      $.ajax({
        url: 'user_action.php',
        type: 'POST',
        dataType: "json",
        data: data,
        beforeSend: function() {
          $('#loadingSpinner').show();
        },
        success: function(response) {
          if (response.success) {
            toastr.success('Cập nhật thành công', 'Thành công');
            $('.update-comment').remove();
            getComment();
          }

        },
        error: function(error) {
          // Xử lý khi có lỗi
          console.log('Error:', error);
          toastr.error(error.responseText, 'Lỗi')
        },
        complete: function() {
          setTimeout(() => {
            $('#loadingSpinner').hide();
          }, 500)
        }
      });

    }))
  }

  // REPLY COMMENT
  const replyComment = (event) => {
    const id = event.id;

    $(`#${id}`).ready((function() {
      const userId = '<?= $account['UserId'] ?? '' ?>';
      const commentId = $(`#${id}`).data('id');
      const replyTo = $(`#${id}`).data('reply-to');
      const dataReply = $(`#${id}`).data('reply');
      const postId = '<?= $postId ?>';

      var $div = $(`#${id}`).closest('div[class="comment"]');

      $('.add-reply').remove();
      $('.update-comment').remove();

      $div.after(`
      <div class='add-reply'>
        <img src="<?= ROOT_URL . '/assets/images/avatar' ?>/<?= $account['UserImage'] ?? '' ?>" alt='profile' class='comment-profile'><div class='textarea-wrapper'>
        <p class='replying-to'>Đang trả lời bình luận của <span style='font-weight: bold;'>
          ${replyTo}
          </span>
        </p>

        <textarea id='reply-content' placeholder='Nhập trả lời' style='resize: none;'></textarea>
      </div>

      <div class='comment-btn-wrapper'>
        <button class='comment-send' id='reply-submit-${commentId}' data-id="${commentId}" data-reply="${dataReply}" onclick="handleReply(this)">Trả lời</button>
        <button class='comment-cancel' onclick='handleCancelReply(this)'>Cancel</button>
        </div>
      </div>`);

    }))
  }

  // Handle Cancel Reply
  const handleCancelReply = (event) => {
    $('.add-reply').remove();
  }
  const handleCancelEdit = (event) => {
    $('.update-comment').remove();
    getComment();
  }

  // Handle Reply
  const handleReply = (event) => {
    const id = event.id;

    console.log(id);


    $(`#${id}`).ready((function() {
      const userId = '<?= $account['UserId'] ?? '' ?>';
      const replyContent = $(`#reply-content`).val();
      const postId = '<?= $postId ?? '' ?>';
      const commentId = $(`#${id}`).data('id');
      const replyId = $(`#${id}`).data('reply');

      const data = {
        userId,
        value: replyContent,
        postId: postId,
        parentIdPostComment: commentId,
        replyId: replyId,
        action: 'commentPost'
      }

      console.log(data);

      if (!replyContent) {
        toastr.error('Vui lòng nhập nội dung phản hồi', 'Lỗi')
        return;
      }

      $.ajax({
        url: 'user_action.php',
        type: 'POST',
        dataType: "json",
        data: data,
        beforeSend: function() {
          $('#loadingSpinner').show();
        },
        success: function(response) {
          if (response.success) {
            toastr.success('Reply comment thành công', 'Thành công');
            $('.add-reply').remove();
            getComment();
          }

        },
        error: function(error) {
          // Xử lý khi có lỗi
          console.log('Error:', error);
          toastr.error(error.responseText, 'Lỗi')
        },
        complete: function() {
          setTimeout(() => {
            $('#loadingSpinner').hide();
          }, 500)
        }
      });
    }))
  }

  $(document).ready(function() {

    // SEND COMMENT
    $('#send-comment').click((function() {
      const userId = '<?= $account['UserId'] ?? '' ?>';
      const value = $('#comment-text').val();
      const postId = '<?= $postId ?>';

      if (!value) {
        toastr.error('Vui lòng nhập nội dung comment', 'Lỗi')
        return;
      }

      const data = {
        value,
        userId,
        postId,
        action: 'commentPost',
        parentIdPostComment: '',
        replyId: '',
      }

      $.ajax({
        url: 'user_action.php',
        type: 'POST',
        dataType: "json",
        data: data,
        beforeSend: function() {
          $('#loadingSpinner').show();
        },
        success: function(response) {
          if (response.success) {
            toastr.success('Comment thành công', 'Thành công');
            $('#comment-text').val('');
            getComment();
          }

        },
        error: function(error) {
          // Xử lý khi có lỗi
          console.log('Error:', error);
          toastr.error(error.responseText, 'Lỗi')
        },
        complete: function() {
          setTimeout(() => {
            $('#loadingSpinner').hide();
          }, 500)
        }
      });
    }))


  })
</script>
</body>

</html>