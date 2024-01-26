//DELETE POST
$(document).on("click", ".deleteId", function(){
    var row = $(this).closest('tr');
    modal.style.display = 'flex'; 
    var postId = $(this).data("postid");
    var action = 'deletePost';
    $(document).on("click", "#deletePostBtn", function(){
      modal.style.display = 'none';
      $.ajax({
        url: 'user_action.php',
        method: 'POST',
        data:{PostId:postId, action:action},
        dataType:"json",
        success:function(response){				
          if(response.success == 1) {
            row.fadeOut(800, function(){
              row.remove();
            });
          }				
        }
      })
    });
});

//LIKE POST
$(document).on("click", "#like", function(){
  var userId = $(this).data("userid");
  var postId = $(this).data("postid");
  var action = 'likePost';
  var self = $(this);
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{PostId:postId, UserId:userId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        var likes = parseInt(self.children("#likeCount").clone().remove().end().text().replace(/[()]/g, '').trim())
        $(".reaction_heart i").removeClass("fa-regular fa-heart");
        $(".reaction_heart i").addClass("fa-solid fa-heart").css("color", "#F14141");
        $(".reaction_heart").attr("id","unlike");
        self.children("#likeCount").text(likes + 1);
      }				
    }
  })
});

//UNLIKE POST
$(document).on("click", "#unlike", function(){
  var userId = $(this).data("userid");
  var postId = $(this).data("postid");
  var action = 'unlikePost';
  var self = $(this);
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{PostId:postId, UserId:userId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        var likes = parseInt(self.children("#likeCount").clone().remove().end().text().replace(/[()]/g, '').trim())
        $(".reaction_heart i").removeClass("fa-solid fa-heart").css("color", "");
        $(".reaction_heart i").addClass("fa-regular fa-heart");
        $(".reaction_heart").attr("id","like");
        self.children("#likeCount").text(likes - 1);
      }				
    }
  })
});

//FOLLOW USER
$(document).on("click", "#follow", function(){
  var userId = $(this).data("userid");
  var followerId = $(this).data("follower");
  var action = 'followUser';
  var self = $(this);
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{UserId:userId, FollowerId:followerId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        var follow = parseInt($('.profile-stats > ul > li:nth-child(2) > span > p').text());
        self.attr("id","unfollow").text("Hủy theo dõi")
        $('.profile-stats > ul > li:nth-child(2) > span > p').text(follow + parseInt(1));
      }				
    }
  })
});

//UNFOLLOW USER
$(document).on("click", "#unfollow", function(){
  var userId = $(this).data("userid");
  var followerId = $(this).data("follower");
  var action = 'unFollowUser';
  var self = $(this);
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{UserId:userId, FollowerId:followerId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        var follow = parseInt($('.profile-stats > ul > li:nth-child(2) > span > p').text());
        self.attr("id","follow").text("Theo dõi")
        $('.profile-stats > ul > li:nth-child(2) > span > p').text(follow - parseInt(1));
      }				
    }
  })
});

// Href notification
$(document).on("click", ".notify_item a", function(event){
  event.preventDefault();
  var notiId = $(this).parents('.notify_item').data("notiid");
  var action = 'updateNotification';
  var userId = $(this).parents('.notify_item').data("userid");
  var self = $(this);
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{NotiId:notiId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        var href = self.attr('href');
        window.location.href = href;
      }	
    }
  })
});

// UPDATE NOTIFICATION
$(document).on("click", ".notify_item", function(){
  var notiId = $(this).data("notiid");
  var action = 'updateNotification';
  var userId = $(this).data("userid")
  var self = $(this);
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{NotiId:notiId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        self.removeClass("notify_item").addClass("notify_item seen");
        fetchNotification(userId);
      }	
    }
  })
});

window.addEventListener('load', () => {
  var userId = $('.notify_item').data("userid");
  fetchNotification(userId);
})

var fetchNotification = function(a){
  var action = 'fetchNotification';
  var userId = a;
  $.ajax({
    url: 'user_action.php',
    method: 'POST',
    data:{UserId:userId, action:action},
    dataType:"json",
    success:function(response){				
      if(response.success == 1) {
        var num = response.total;
        if(num > 0) {
          $('.notification_wrap span.badge').text(num);
        }else {
          $('.notification_wrap').find('span').remove();
        }
      }	
    }
  });
}

