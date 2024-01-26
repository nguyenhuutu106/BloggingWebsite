//DELETE POST
$(document).on("click", ".deleteId", function(){
    var row = $(this).closest('tr');
    modal.style.display = 'flex'; 
    var postId = $(this).data("postid");
    var action = 'deletePost';
    $(document).on("click", "#deletePostBtn", function(){
      modal.style.display = 'none';
      $.ajax({
        url: 'admin_action.php',
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
  
  // DELETE USER
  $(document).on("click", ".deleteId", function(){
    var row = $(this).closest('tr');
    modal.style.display = 'flex'; 
    var userId = $(this).data("userid");
    var action = 'deleteUser';
    $(document).on("click", "#deleteUserBtn", function(){
      modal.style.display = 'none';
      $.ajax({
        url: 'admin_action.php',
        method: 'POST',
        data:{UserId:userId, action:action},
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
  
  // DELETE CATEGORY
  $(document).on("click", ".deleteId", function(){
    var row = $(this).closest('tr');
    modal.style.display = 'flex'; 
    var categoryId = $(this).data("categoryid");
    var action = 'deleteCategory';
    $(document).on("click", "#deleteCategoryBtn", function(){
      modal.style.display = 'none';
      $.ajax({
        url: 'admin_action.php',
        method: 'POST',
        data:{CategoryId:categoryId, action:action},
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