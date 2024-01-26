<?php
class User {	
   
	private $userTable = 'users';	
    private $postTable = 'posts';	
    private $categoryTable = 'categories';	
    private $postCategoryTable = 'post_category';	
	private $notificationsTable = 'notifications';	
    private $commentsTable = 'comments';	
    private $post_likeTable = 'post_like';	
    private $user_followTable = 'user_follow';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
    public function register() {
		if($this->account && $this->name && $this->email && $this->password) {	
			$sqlCheckAccount = "SELECT * FROM ".$this->userTable." WHERE UserAccount = ? ";	
			$sqlCheckEmail = "SELECT * FROM ".$this->userTable." WHERE UserEmail = ? ";	
			$stmt1 = $this->conn->prepare($sqlCheckAccount);
			$stmt1->bind_param('s', $this->account);
			$stmt1->execute();
			$account = $stmt1->get_result();
			$stmt2 = $this->conn->prepare($sqlCheckEmail);
			$stmt2->bind_param('s', $this->email);
			$stmt2->execute();
			$email = $stmt2->get_result();
			if($account->num_rows > 0) {
				return "account";
			}else if($email->num_rows > 0) {
				return "email";
			}else {
				$sqlQuery = "INSERT INTO ".$this->userTable."(`UserAccount`, `UserName`, `UserEmail`, `UserPassword`) VALUES (?, ?, ?, ?)";
				$stmt = $this->conn->prepare($sqlQuery);
				$password = md5($this->password);	
				$stmt->bind_param('ssss', $this->account, $this->name, $this->email, $password);
				$stmt->execute();
				return 1;
			}
		}
    }
	public function login() {
		if($this->account && $this->password) {	
			$sqlCheck = "SELECT * FROM ".$this->userTable." WHERE UserAccount = ? AND UserPassword = ?";	
			$stmt = $this->conn->prepare($sqlCheck);
			$password = md5($this->password);
			$stmt->bind_param('ss', $this->account, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0) {
				$account = $result->fetch_assoc();
				$role = $account['UserRole'];
				if ($role == 1){
					$_SESSION['UserRole'] = $role;
					$_SESSION["userId"] = $account['UserId'];	
					$_SESSION["userAccount"] = $account['UserAccount'];					
					$_SESSION["userName"] = $account['UserName'];	
					return "admin";
				}else if($role != 1){
					$_SESSION["userId"] = $account['UserId'];	
					$_SESSION["userAccount"] = $account['UserAccount'];					
					$_SESSION["userName"] = $account['UserName'];	
					$_SESSION['UserRole'] = $role;		
					setcookie('userAccount',$account['UserAccount'],time() + (365 * 24 * 60 * 60));
					return "user";
				}
			}else{
				return "invalid";
			}
		}
	}
	public function loggedIn (){
		if(isset($_SESSION["userAccount"])) {
			return 1;
		} else {
			return 0;
		}
	}
	public function checkEmail (){
		if($this->email){
			$sqlQuery = "
			SELECT * FROM `".$this->userTable."`
			WHERE UserEmail = ? ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param('s', $this->email);
			$stmt->execute();
			$email = $stmt->get_result();
			if($email->num_rows > 0){
				return 1;
			}else {
				return "email";
			}
		}else {
			return "nodata";
		}
	}
	public function sendEmail (){
		$sqlQuery = "
		UPDATE `".$this->userTable."`
		SET reset_token_hash = ?, reset_token_expires_at = ?
		WHERE UserEmail = ? ";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('sss', $this->tokenHash, $this->expiry, $this->email);
		$stmt->execute();

		$sqlPassword = "
		UPDATE `".$this->userTable."`
		SET UserPassword = ?
		WHERE UserEmail = ? ";
		$stmt1 = $this->conn->prepare($sqlPassword);
		$password = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
		$stmt1->bind_param('ss', $password, $this->email);
		$stmt1->execute();
		if($this->conn->affected_rows){
			$mail = require 'mailer.php';
			
			$mail->setFrom("nhuutu106@gmail.com", 'Admin Trang WebBlogging');
			$mail->addAddress($this->email);
			$mail->Subject = "Password Reset";
			$mail->Body = <<<END

			Xin Chào,
			<br>
			Chúng tôi gửi cho bạn email này vì đã có yêu cầu làm mới mật khẩu đến từ gmail của bạn.
			<br>
			Để có thể thực hiện làm mới mật khẩu của bạn, vui lòng ấn vào đường link dưới đây:
			<br>
			<a href="http://localhost/BloggingWebsite/reset_password.php?token=$this->token">Đổi mật khẩu</a>
			<br>
			Xin lưu ý, nếu qua 10 phút bạn vẫn chưa thay đổi mật khẩu, phiên đổi mật khẩu sẽ hết hạn. Bạn có thể bỏ qua email này nếu bạn không phải người yêu cầu quên mật khẩu.
			END;

			try{
				$mail->send();
				return 1;
			} catch (Exception $e) {

				echo "Tin nhắn không thể gửi. Mailer error: {$this->email->ErrorInfo}";
			}
		}
	}
	public function checkToken (){
		if($this->tokenHash){
			$sqlQuery = "
			SELECT * FROM `".$this->userTable."`
			WHERE reset_token_hash = ? ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param('s', $this->tokenHash);
			$stmt->execute();
			$result = $stmt->get_result();
			$user = $result->fetch_assoc();
			if($user === null){
				return "token not found";
			}else if(strtotime($user['reset_token_expires_at']) <= time()) {
				return "token expired";
			}else {
				return "token valid";
			}
		}else {
			return "nodata";
		}
	}
	public function resetPassword (){
		if($this->password){
			$sqlQuery = "
			UPDATE `".$this->userTable."`
			SET UserPassword = ? 
			WHERE reset_token_hash = ? ";	
			$stmt = $this->conn->prepare($sqlQuery);
			$password = md5($this->password);
			$stmt->bind_param('ss', $password, $this->tokenHash);
			$stmt->execute();
			if($stmt->execute()){
				$sqlQuery = "
				UPDATE `".$this->userTable."`
				SET reset_token_hash = NULL, reset_token_expires_at = NULL 
				WHERE reset_token_hash = ? ";	
				$stmt = $this->conn->prepare($sqlQuery);
				$stmt->bind_param('s', $this->tokenHash);
				$stmt->execute();
				return 1;
			}
		}else {
			return "nodata";
		}
	}
	public function getUserId($id){	
		$sqlQuery = "
        SELECT *
		FROM `".$this->userTable."`
		WHERE users.UserId = ".$id.";";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function editAccount($id){
		if($this->name && $this->image && $this->email) {
			$sqlCheckEmail = "SELECT * FROM ".$this->userTable." WHERE UserEmail = ? AND UserId NOT LIKE ?";	
			$stmt1 = $this->conn->prepare($sqlCheckEmail);
			$stmt1->bind_param('ss', $this->email, $id);
			$stmt1->execute();
			$email = $stmt1->get_result();
			if($email->num_rows > 0) {
				return "email";
			}else {
				$sqlEditAccount = "
				UPDATE ".$this->userTable."
				SET UserName = ?, UserImage = ?, UserBio = ?, UserEmail = ?
				WHERE UserId = ".$id." ";
				$stmt = $this->conn->prepare($sqlEditAccount);	
				$stmt->bind_param('ssss', $this->name, $this->image, $this->bio, $this->email);
				$stmt->execute();
				return 1;
			}
		}else {
			return "nodata";
		}
	}
	public function changePassword($id){
		if($this->oldPassword && $this->password) {
			$sqlCheckPassword = "
			SELECT * FROM ".$this->userTable."
			WHERE UserPassword = ? AND UserId = ? ";
			$stmt = $this->conn->prepare($sqlCheckPassword);
			$hashPassword = md5($this->oldPassword);
			$stmt->bind_param('ss', $hashPassword, $id);
			$stmt->execute();
			$password = $stmt->get_result();
			if($password->num_rows > 0) {
				$sqlChangePassword = "
				UPDATE ".$this->userTable."
				SET UserPassword = ?
				WHERE UserId = ? ";
				$stmt = $this->conn->prepare($sqlChangePassword);	
				$hashPassword = md5($this->password);
				$stmt->bind_param('ss',$hashPassword, $id);
				$stmt->execute();
				return 1;
			}else {
				return "password";
			}
		}else {
			return "nodata";
		}
	}
	public function getAllPostbyUser($id){	
		$sqlQuery = "
        SELECT DISTINCT posts.PostId, posts.PostTitle, users.UserName, posts.PostDate, posts.PostThumbnail 
        FROM `".$this->postTable."`, `".$this->userTable."` 
		WHERE users.UserId = posts.UserId AND posts.UserId = ? ORDER BY posts.PostDate DESC;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function createPost(){	
		if($this->userId && $this->postTitle && $this->postThumbnail  && $this->postDes && $this->postContent && $this->postCategory) {
			$sqlInsertPost = "
			INSERT INTO ".$this->postTable."(`UserId`, `PostTitle`, `PostDescription`, `PostThumbnail`, `PostContent`) 
			VALUES (?, ?, ?, ?, ?)";
			$stmt = $this->conn->prepare($sqlInsertPost);	
			$stmt->bind_param('issss', $this->userId, $this->postTitle, $this->postDes, $this->postThumbnail, $this->postContent);
			$stmt->execute();
			$postId = $stmt->insert_id;
			foreach($this->postCategory as $row){
				$sqlInsertCategoryPost = "INSERT INTO ".$this->postCategoryTable."(`PostId`, `CategoryId`) VALUES (?, ?)";
				$stmt = $this->conn->prepare($sqlInsertCategoryPost);	
				$categoryId = intval($row);
				$stmt->bind_param('ii', $postId, $categoryId);
				$stmt->execute();
			}	

			// Get Name who send notifi
			$getUserName = "
			SELECT UserName FROM `".$this->userTable."`
			WHERE UserId = ?";
			$stmt1 = $this->conn->prepare($getUserName);
			$stmt1->bind_param('i', $this->userId);
			$stmt1->execute();		
			$result = $stmt1->get_result();
			foreach($result as $row){
				$userName = $row['UserName'];
			}

			// get all user who follow Uploader
			$notificationQuery = "
			SELECT FollowerId FROM `user_follow`
			WHERE FollowedUserId = ?;";

			$stmt2 = $this->conn->prepare($notificationQuery);	
			$stmt2->bind_param('i', $this->userId);
			$stmt2->execute();
			$notificationResult = $stmt2->get_result();
			foreach($notificationResult as $notificationRow){
				$notificationText ='<a style="all: unset; cursor: pointer;" href="'.ROOT_URL.'post.php?id='.$postId.'"> <b>' .$userName.'</b> đã đăng tải bài viết mới </a>';

				$notifiType = "Post";
				$insertNoti = "
				INSERT INTO `notifications`
				(`UserId`, `PostId`, `FromUserId`,`NotificationType` , `NotificationText`) 
				VALUES (?, ?, ?, ?, ?)";

				$stmt3 = $this->conn->prepare($insertNoti);	
				$stmt3->bind_param('iiiss', $notificationRow['FollowerId'], $postId, $this->userId, $notifiType , $notificationText);
				$stmt3->execute();
			}
			return 1;
		}else {
			return "nodata";
		}
	}
	public function getAllCategory(){	
		$sqlQuery = "
		SELECT *
		FROM `".$this->categoryTable."`
		ORDER BY 
		  CASE 
			WHEN CategoryId = 0 THEN 1
			ELSE 0
		  END, CategoryName;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getUserById($id){	
		$sqlQuery = "
		SELECT *
		FROM `".$this->userTable."`
		WHERE UserId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getAllPost($startRow, $pageSize){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage
		FROM `".$this->postTable."`, `".$this->userTable."`
		WHERE posts.UserId = users.UserId  ORDER BY posts.PostDate DESC limit ? , ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('ii', $startRow, $pageSize);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	
	public function getPostBySearch($query, $startRow, $pageSize){	
		$sqlQuery = "
		(SELECT DISTINCT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView , posts.UserId
		FROM `".$this->postTable."` INNER JOIN `".$this->userTable."` on posts.UserId = users.UserId
		WHERE posts.PostTitle LIKE '%".$query."%' OR users.UserName LIKE '%".$query."%' ORDER BY posts.PostDate DESC limit  ".$startRow.", ".$pageSize.")
		UNION
		(SELECT DISTINCT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId
		FROM `".$this->postTable."` , `".$this->postCategoryTable."` INNER JOIN categories ON post_category.CategoryId = categories.CategoryId
		WHERE posts.PostId = post_category.PostId AND categories.CategoryName LIKE '%".$query."%' ORDER BY posts.PostDate DESC limit  ".$startRow.", ".$pageSize.");";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostBySearchRowCount($query){	
		$sqlQuery = "
		(SELECT DISTINCT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView , users.UserId
		FROM `".$this->postTable."` INNER JOIN `".$this->userTable."` on posts.UserId = users.UserId
		WHERE posts.PostTitle LIKE '%".$query."%' OR users.UserName LIKE '%".$query."%' ORDER BY posts.PostDate DESC)
		UNION
		(SELECT DISTINCT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId
		FROM `".$this->postTable."` , `".$this->postCategoryTable."` INNER JOIN categories ON post_category.CategoryId = categories.CategoryId
		WHERE posts.PostId = post_category.PostId AND categories.CategoryName LIKE '%".$query."%' ORDER BY posts.PostDate DESC);";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result->num_rows;			
	}
	public function getAllPostRowCount(){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage
		FROM `".$this->postTable."`, `".$this->userTable."`
		WHERE posts.UserId = users.UserId  ORDER BY posts.PostDate ;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result->num_rows;			
	}
	public function getUserFollow($id, $startRow, $pageSize){	
		$sqlQuery = "
		SELECT * FROM `user_follow` WHERE user_follow.FollowerId = ? limit  ?, ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('sii', $id, $startRow, $pageSize);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostUserFollowing($id){	
		$sqlQuery = "
		SELECT posts.PostId, posts.UserId, posts.PostTitle, posts.PostThumbnail, posts.UserId, users.UserName, users.UserImage 
		FROM `".$this->postTable."`, `".$this->userTable."`
		WHERE users.UserId = posts.UserId  AND posts.UserId = ? ORDER BY posts.PostDate DESC LIMIT 0, 3;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getCategoryName($id){	
		$sqlQuery = "
		SELECT CategoryName, CategoryContent
		FROM `".$this->categoryTable."`
		WHERE CategoryId = ? ;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;
	}
	public function getPostId($id){	
		$sqlQuery = "
        SELECT posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostContent 
		FROM `".$this->postTable."`
		WHERE posts.PostId = ".$id.";";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostCategory($id){	
		$sqlQuery = "
		SELECT post_category.PostId, categories.CategoryId, categories.CategoryName 
		FROM `".$this->categoryTable."`, `".$this->postCategoryTable."` 
		WHERE post_category.CategoryId = categories.CategoryId AND post_category.PostId = ".$id.";";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostByCategory($id, $startRow, $pageSize){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage
		FROM `".$this->postTable."`, `".$this->userTable."` , `".$this->postCategoryTable."`
		WHERE posts.UserId = users.UserId AND posts.PostId = post_category.PostId AND post_category.CategoryId = ? ORDER BY posts.PostDate DESC limit ? , ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('sii', $id, $startRow, $pageSize);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostByCategoryRowCount($id){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage
		FROM `".$this->postTable."`, `".$this->userTable."` , `".$this->postCategoryTable."`
		WHERE posts.UserId = users.UserId AND posts.PostId = post_category.PostId AND post_category.CategoryId = ? ORDER BY posts.PostDate DESC;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result->num_rows;			
	}
	public function getPostLike($user, $post){	
		$sqlQuery = "
		SELECT * FROM `".$this->post_likeTable."` WHERE UserId = ? AND PostId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('ss', $user, $post);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function checkUserFollow($user, $followedUser){	
		$sqlQuery = "
		SELECT * FROM `".$this->user_followTable."` WHERE FollowerId = ? AND FollowedUserId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('si', $user, $followedUser);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostByUserLike($id, $startRow, $pageSize){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage
		FROM `".$this->postTable."`, `".$this->userTable."` , `".$this->post_likeTable."`
		WHERE posts.UserId = users.UserId AND posts.PostId = post_like.PostId AND post_like.UserId = ? ORDER BY posts.PostDate DESC limit ? , ?; ";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('sii', $id, $startRow, $pageSize);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostByUserLikeRowCount($id){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage
		FROM `".$this->postTable."`, `".$this->userTable."` , `".$this->post_likeTable."`
		WHERE posts.UserId = users.UserId AND posts.PostId = post_like.PostId AND post_like.UserId = ? ORDER BY posts.PostDate DESC";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result->num_rows;		
	}
	public function getPostSame1Categories($id, $cate1){	
		$sqlQuery = "
		SELECT DISTINCT post_category.PostId, posts.PostTitle, posts.PostThumbnail, post_category.CategoryId
		FROM `".$this->postTable."` INNER JOIN `".$this->postCategoryTable."` on posts.PostId = post_category.PostId 
		WHERE post_category.CategoryId LIKE ?  AND post_category.PostId NOT LIKE ?
		GROUP BY post_category.PostId ORDER BY RAND() LIMIT 10;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('ss', $cate1, $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;		
	}
	public function getPostSame2Categories($id, $cate1, $cate2){	
		$sqlQuery = "
		SELECT DISTINCT post_category.PostId, posts.PostTitle, posts.PostThumbnail, post_category.CategoryId
		FROM `".$this->postTable."` INNER JOIN `".$this->postCategoryTable."` on posts.PostId = post_category.PostId 
		WHERE (post_category.CategoryId LIKE ? OR post_category.CategoryId LIKE ?)  AND post_category.PostId NOT LIKE ?
		GROUP BY post_category.PostId ORDER BY RAND() LIMIT 10;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('sss', $cate1, $cate2, $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;		
	}
	public function getPostSame3Categories($id, $cate1, $cate2, $cate3){	
		$sqlQuery = "
		SELECT DISTINCT post_category.PostId, posts.PostTitle, posts.PostThumbnail, post_category.CategoryId
		FROM `".$this->postTable."` INNER JOIN `".$this->postCategoryTable."` on posts.PostId = post_category.PostId 
		WHERE (post_category.CategoryId LIKE ? OR post_category.CategoryId LIKE ? OR post_category.CategoryId LIKE ?) AND post_category.PostId NOT LIKE ?
		GROUP BY post_category.PostId ORDER BY RAND() LIMIT 10;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('ssss', $cate1, $cate2, $cate3, $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result;		
	}
	public function CountPostLike($id){	
		$sqlQuery = "
		SELECT COUNT(UserId) FROM `".$this->post_likeTable."` WHERE PostId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function CountPostComment($id){	
		$sqlQuery = "
		SELECT COUNT(CommentId) FROM `comments` WHERE PostId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function CountUserPost($id){	
		$sqlQuery = "
		select COUNT(UserId) AS UserPost FROM `".$this->postTable."` WHERE UserId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		$countPost = $result->fetch_assoc();
		return $countPost['UserPost'];			
	}
	public function CountUserFollowers($id){	
		$sqlQuery = "
		select COUNT(FollowerId) AS UserFollower FROM `".$this->user_followTable."` WHERE FollowedUserId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		$countFollowers = $result->fetch_assoc();
		return $countFollowers['UserFollower'];			
	}
	public function CountUserFollowing($id){	
		$sqlQuery = "
		select COUNT(FollowedUserId) AS UserFollowing FROM `".$this->user_followTable."` WHERE FollowerId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();	
		$countFollowing = $result->fetch_assoc();
		return $countFollowing['UserFollowing'];			
	}
	public function getUserFollowRowCount($id){	
		$sqlQuery = "
		SELECT * FROM `".$this->user_followTable."` WHERE user_follow.FollowerId = ? ;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s', $id);
		$stmt->execute();			 
		$result = $stmt->get_result();		
		return $result->num_rows;		
	}
	public function getPostContent($id){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostDescription, posts.PostThumbnail, posts.PostDate, posts.PostView, posts.UserId, users.UserName, users.UserImage, posts.PostContent, (SELECT COUNT(post_like.PostId) AS PostLike FROM `post_like`) AS PostLike, (SELECT COUNT(Comments.CommentId) AS PostComment FROM `comments`) AS PostComment
		FROM `".$this->postTable."`, `".$this->userTable."`
		WHERE posts.UserId = users.UserId AND posts.PostId = ?";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getProfile($id){	
		$sqlQuery = "
		SELECT UserId, UserName, UserBio, UserImage FROM `".$this->userTable."` WHERE UserId = ?;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function getPostProfile($id){	
		$sqlQuery = "
		SELECT * FROM `".$this->postTable."` WHERE UserId = ? ORDER BY PostDate DESC;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function CountTopView(){	
		$sqlQuery = "
		SELECT posts.PostId, posts.PostTitle, posts.PostThumbnail, posts.PostDate, posts.PostView As View
		FROM posts
		ORDER BY View DESC LIMIT 0,5;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
	public function CountHotView(){	
		$sqlQuery = "
		SELECT day_view.PostId, posts.PostTitle, posts.PostThumbnail, posts.PostDate, SUM(day_view.DayViewCount) AS View
		FROM day_view , posts
		WHERE day_view.Day BETWEEN NOW() - INTERVAL 5 DAY AND NOW() + INTERVAL 1 Day AND posts.PostId = day_view.PostId
		GROUP BY day_view.PostId ORDER BY View DESC LIMIT 0,5 ;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}

	public function fetchNumNotification($id){	
		$sqlQuery = "
		SELECT COUNT(NotificationId) AS Total 
		FROM `".$this->notificationsTable."` 
		WHERE UserId = ? AND NotificationStatus = 0;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		$countNotifi = $result->fetch_assoc();
		return $countNotifi['Total'];
	}
	public function updateNotification($id){	
		$sqlQuery = "UPDATE `notifications` SET `NotificationStatus`='1' WHERE NotificationId = ?";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}				
	}

	public function loadNotification($id){	
		$sqlQuery = "
		SELECT users.UserImage , notifications.NotificationId, notifications.UserId, notifications.PostId, notifications.FromUserId, notifications.NotificationText, notifications.NotificationTime, notifications.NotificationStatus 
		FROM `".$this->notificationsTable."`, `".$this->userTable."`
		WHERE notifications.FromUserId = users.UserId  AND notifications.UserId = ? ORDER BY notifications.NotificationTime DESC;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('s',$id);
		$stmt->execute();			
		$result = $stmt->get_result();
		$totalRow = mysqli_num_rows($result); 
		$output = '';
		if($totalRow > 0){
			foreach($result as $row){
				if($row['NotificationStatus'] == 0){
					$output .= '
					<div class="notify_item" data-userid = "'.$row['UserId'].'" data-notiId = "'.$row['NotificationId'].'">
							<div class="notify_img avatar" style="height: auto;">
								<a style="all: unset; cursor: pointer;" href ="'.ROOT_URL.'profile.php?id='.$row['FromUserId'].'"><img src="'.ROOT_URL.'assets/images/avatar/'.$row['UserImage'].'" alt="profile_pic" style="height: 100%"></a>
							</div>
							<div class="notify_info">
								<p>'.$row['NotificationText'].'</p>
								<span class="notify_time">'.$row['NotificationTime'].'</span>
							</div>
					</div>';
				}else {
					$output .= '
					<div class="notify_item seen" data-userid = "'.$row['UserId'].'" data-notiId = "'.$row['NotificationId'].'">
							<div class="notify_img avatar" style="height: auto;">
								<a style="all: unset; cursor: pointer;" href ="'.ROOT_URL.'profile.php?id='.$row['FromUserId'].'"><img src="'.ROOT_URL.'assets/images/avatar/'.$row['UserImage'].'" alt="profile_pic" style="height: 100%"></a>
							</div>
							<div class="notify_info">
								<p>'.$row['NotificationText'].'</p>
								<span class="notify_time">'.$row['NotificationTime'].'</span>
							</div>
					</div>';
				}
			}
		}else {
			$output = '<h2 style="color: var(--bg-carolina-blue); margin-left: 1.5rem;"> Bạn không có thông báo nào </h2>';
		}
		return $output;
	}

	public function CountNotification($id){	
			$total = $this->fetchNumNotification($id);
			$output = array(			
				"success"	=> 	1,
				'total' => $total
			);
			echo json_encode($output);
	}					
	public function editPost($id){	
		if($this->postTitle && $this->postDes && $this->postThumbnail && $this->postContent && $this->postCategory) {
			$sqlEditPost = "
			UPDATE ".$this->postTable."
			SET PostTitle = ?, PostDescription = ?, PostThumbnail = ?, PostContent = ?
			WHERE PostId = ".$id." ";
			$stmt = $this->conn->prepare($sqlEditPost);	
			$stmt->bind_param('ssss', $this->postTitle, $this->postDes, $this->postThumbnail, $this->postContent);
			$stmt->execute();
			
			$sqlQuery = "
			SELECT * FROM `".$this->postCategoryTable."`
			WHERE PostId = ?";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param('i', $id);
			$stmt->execute();			
			$result = $stmt->get_result();	
			$postCategory = [];
			foreach($result as $row){
				$postCategory[] = $row['CategoryId'];
			}		
			// insert new category data	
			foreach ($this->postCategory as $inputValues){
				if(!in_array($inputValues, $postCategory)){
					$sqlInsertCategory = "
					INSERT INTO ".$this->postCategoryTable."(`PostId`, `CategoryId`) 
					VALUES (?, ?)";
					$stmt = $this->conn->prepare($sqlInsertCategory);	
					$stmt->bind_param('ii', $id, $inputValues);
					$stmt->execute();
				}
			}
			// delete new category data
			foreach ($postCategory as $fetchedRow){
				if(!in_array($fetchedRow, $this->postCategory)){
					$sqlDeleteCategory = "
					DELETE FROM `".$this->postCategoryTable."`
					WHERE PostId = ? AND CategoryId = ? ";
					$stmt = $this->conn->prepare($sqlDeleteCategory);	
					$stmt->bind_param('ii', $id, $fetchedRow);
					$stmt->execute();
				}
			}
			return 1;
		}else {
			return "nodata";
		}
	}
	public function deletePost($id){		
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->postTable." 
			WHERE PostId = ?");
		$id = htmlspecialchars(strip_tags($id));
		$stmt->bind_param("s", $id);
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
	public function likePost($postId, $userId){		
		$stmt = $this->conn->prepare("
			INSERT INTO `".$this->post_likeTable."`(`UserId`, `PostId`) 
			VALUES (?,?)");
		$post = htmlspecialchars(strip_tags($postId));
		$user = htmlspecialchars(strip_tags($userId));
		$stmt->bind_param("ii", $user, $post);
		// Get Name who send notifi
		$getUserName = "
		SELECT UserName FROM `".$this->userTable."`
		WHERE UserId = ?";
		$stmt1 = $this->conn->prepare($getUserName);
		$stmt1->bind_param('i', $userId);
		$stmt1->execute();		
		$result = $stmt1->get_result();
		foreach($result as $row){
			$userName = $row['UserName'];
		}

		// Get owner of the post
		$getOwnerId = "
		SELECT posts.UserId FROM `".$this->postTable."` 
		WHERE posts.PostId = ?;";
		$stmt1 = $this->conn->prepare($getOwnerId);
		$stmt1->bind_param('i', $post);
		$stmt1->execute();		
		$result = $stmt1->get_result();
		foreach($result as $row){
			$postOwnerId = $row['UserId'];
		}

		$notifiType = "Like";
		if(intval($postOwnerId) !=  intval($userId)){
			$notificationText ='<a style="all: unset; cursor: pointer;" href="'.ROOT_URL.'post.php?id='.$postId.'"> <b>'.$userName.'</b> đã thích bài viết của bạn </a>';
			$insertNoti = "
			INSERT INTO `notifications`
			(`UserId`, `PostId`, `FromUserId`, `NotificationType`, `NotificationText`) 
			VALUES (?, ?, ?, ?, ?)";
	
			$stmt3 = $this->conn->prepare($insertNoti);	
			$stmt3->bind_param('iiiss', $postOwnerId, $postId, $userId, $notifiType, $notificationText);
			$stmt3->execute();
		}
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
	public function unLikePost($postId, $userId){		
		$stmt = $this->conn->prepare("
			DELETE FROM `".$this->post_likeTable."`
			WHERE UserId = ? AND PostId = ?");
		$post = htmlspecialchars(strip_tags($postId));
		$user = htmlspecialchars(strip_tags($userId));
		$stmt->bind_param("ii", $user, $post);

		// Get owner of the post
		$getOwnerId = "
		SELECT posts.UserId FROM `".$this->postTable."` 
		WHERE posts.PostId = ?;";
		$stmt1 = $this->conn->prepare($getOwnerId);
		$stmt1->bind_param('i', $post);
		$stmt1->execute();		
		$result = $stmt1->get_result();
		foreach($result as $row){
			$postOwnerId = $row['UserId'];
		}

		$notifiType = "Like";
		$deleteNotifi = "
		DELETE FROM `notifications`
		WHERE UserId = ? AND FromUserId = ? AND PostId = ? AND NotificationType = ?";
		
		$stmt3 = $this->conn->prepare($deleteNotifi);	
		$stmt3->bind_param('iiis', $postOwnerId, $userId, $postId, $notifiType);
		$stmt3->execute();
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
	public function followUser($followerId, $userId){		
		$stmt = $this->conn->prepare("
			INSERT INTO `".$this->user_followTable."`(`FollowerId`, `FollowedUserId`)
			VALUES (?,?)");
		$idFollow = htmlspecialchars(strip_tags($followerId));
		$idFollowed = htmlspecialchars(strip_tags($userId));
		$stmt->bind_param("ii", $idFollow, $idFollowed);
		// Get Name who send notifi
		$getUserName = "
		SELECT UserName FROM `".$this->userTable."`
		WHERE UserId = ?";
		$stmt1 = $this->conn->prepare($getUserName);
		$stmt1->bind_param('i', $followerId);
		$stmt1->execute();		
		$result = $stmt1->get_result();
		foreach($result as $row){
			$userName = $row['UserName'];
		}

		$notifiType = "Follow";
		$notificationText ='<a style="all: unset; cursor: pointer;" href="'.ROOT_URL.'profile.php?id='.$followerId.'"> <b>'.$userName.'</b> đã bắt đầu theo dõi bạn </a>';
		$insertNoti = "
		INSERT INTO `notifications`
		(`UserId`, `FromUserId`, `NotificationType` ,`NotificationText`) 
		VALUES (?, ?, ?, ?)";

		$stmt3 = $this->conn->prepare($insertNoti);	
		$stmt3->bind_param('iiss', $idFollowed, $followerId, $notifiType ,$notificationText);
		$stmt3->execute();
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
	public function unFollowUser($followerId, $userId){		
		$stmt = $this->conn->prepare("
			DELETE FROM `".$this->user_followTable."`
			WHERE FollowerId = ? AND FollowedUserId = ?");
		$idFollow = htmlspecialchars(strip_tags($followerId));
		$idFollowed = htmlspecialchars(strip_tags($userId));
		$stmt->bind_param("ii", $idFollow, $idFollowed);
		
		$notifiType = "Follow";
		$deleteNotifi = "
		DELETE FROM `notifications`
		WHERE UserId = ? AND FromUserId = ? AND NotificationType = ?";

		$stmt3 = $this->conn->prepare($deleteNotifi);	
		$stmt3->bind_param('iis', $idFollowed, $followerId, $notifiType);
		$stmt3->execute();
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
	public function commentPost($postId, $userId, $value, $parentIdPostComment, $replyId)
	{
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$postedAt = Date('Y-m-d H:i:s');

		if (empty($parentIdPostComment)) {
			$sqlInsertPost = "
			INSERT INTO `comments` (`PostId`, `UserId`, `CommentText`, `CommentCreateAt`) 
			VALUES (?, ?, ?, ?)";

			$stmt = $this->conn->prepare($sqlInsertPost);
			$stmt->bind_param('iiss', $postId, $userId, $value, $postedAt);
			$stmt->execute();

			// Get Name who send notifi
			$getUserName = "
			SELECT UserName FROM `".$this->userTable."`
			WHERE UserId = ?";
			$stmt1 = $this->conn->prepare($getUserName);
			$stmt1->bind_param('i', $userId);
			$stmt1->execute();		
			$result = $stmt1->get_result();
			foreach($result as $row){
				$userName = $row['UserName'];
			}
			
			$notifiType = "Comment";
			$checkPost = "SELECT posts.UserId FROM posts WHERE posts.PostId = $postId;";
			$stmt1 = $this->conn->prepare($checkPost);	
			$stmt1->execute();
			$result = $stmt1->get_result();		
			$checkUserId = $result->fetch_assoc();
			if($checkUserId['UserId'] != $userId)	{
				$notificationText ='<a style="all: unset; cursor: pointer;" href="'.ROOT_URL.'post.php?id='.$postId.'"> <b>'.$userName.'</b> đã bình luận bài viết của bạn </a>';
				$insertNoti = "
				INSERT INTO `notifications`
				(`UserId`, `PostId`, `FromUserId`, `NotificationType`, `NotificationText`) 
				VALUES (?, ?, ?, ?, ?)";
	
				$stmt2= $this->conn->prepare($insertNoti);	
				$stmt2->bind_param('iiiss', $checkUserId['UserId'], $postId, $userId, $notifiType, $notificationText);
				$stmt2->execute();
			}
			echo json_encode(['error' => null, 'success' => 1]);
			exit;
		}

		$sqlSelectUser = "SELECT comments.UserId FROM `comments` WHERE comments.CommentId = ?;";
		$stmt = $this->conn->prepare($sqlSelectUser);
		$stmt->bind_param('i', $replyId);
		$stmt->execute();
		$result = $stmt->get_result();
		$repTo = $result->fetch_assoc();
		
		// Get Name who send notifi
		$getUserName = "
		SELECT UserName FROM `".$this->userTable."`
		WHERE UserId = ?";
		$stmt1 = $this->conn->prepare($getUserName);
		$stmt1->bind_param('i', $userId);
		$stmt1->execute();		
		$result = $stmt1->get_result();
		foreach($result as $row){
			$userName = $row['UserName'];
		}
		
		// Notifi Rep
		$notifiType = "Reply";
		$notificationText ='<a style="all: unset; cursor: pointer;" href="'.ROOT_URL.'post.php?id='.$postId.'"> <b>'.$userName.'</b> trả lời bình luận của bạn </a>';
		$insertNoti = "
		INSERT INTO `notifications`
		(`UserId`, `PostId`, `FromUserId`, `NotificationType`, `NotificationText`) 
		VALUES (?, ?, ?, ?, ?)";

		$stmt2= $this->conn->prepare($insertNoti);	
		$stmt2->bind_param('iiiss', $repTo['UserId'], $postId, $userId, $notifiType, $notificationText);
		$stmt2->execute();
	
		//Notifi Comment
		$notifiType1 = "Comment";
		$checkPost = "SELECT posts.UserId FROM posts WHERE posts.PostId = $postId;";
		$stmt1 = $this->conn->prepare($checkPost);	
		$stmt1->execute();
		$result = $stmt1->get_result();		
		$checkUserId = $result->fetch_assoc();
		if($checkUserId['UserId'] != $userId)	{
			$notificationText ='<a style="all: unset; cursor: pointer;" href="'.ROOT_URL.'post.php?id='.$postId.'"> <b>'.$userName.'</b> đã bình luận bài viết của bạn </a>';
			$insertNoti = "
			INSERT INTO `notifications`
			(`UserId`, `PostId`, `FromUserId`, `NotificationType`, `NotificationText`) 
			VALUES (?, ?, ?, ?, ?)";

			$stmt2= $this->conn->prepare($insertNoti);	
			$stmt2->bind_param('iiiss', $checkUserId['UserId'], $postId, $userId, $notifiType1, $notificationText);
			$stmt2->execute();
		}

		$sqlInsertPost = "
			INSERT INTO `comments` (`PostId`, `UserId`, `CommentText`, `ReplyToUserId`, `CommentParentId`, `CommentCreateAt`) 
			VALUES (?, ?, ?, ?, ?, ?)";

		$stmt = $this->conn->prepare($sqlInsertPost);
		$stmt->bind_param('iisiis', $postId, $userId, $value, $repTo['UserId'], $parentIdPostComment, $postedAt);
		$stmt->execute();
		echo json_encode(['error' => null, 'success' => 1]);
	}


	function getData($table, $sql_input = "")
	{
		$sql = "SELECT * FROM $table";

		if (!empty($sql_input)) {
			$sql = $sql_input;
		}

		$stmt = $this->conn->prepare($sql);
		$data = array();

		$stmt->execute();
		$result = $stmt->get_result();

		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		return $data;
	}

	public function getComment($postId)
	{
		$sql = "SELECT CM.*, U.UserName, U.UserImage, U.UserRole  FROM `comments` CM JOIN `users` U ON CM.UserId=U.UserId AND CommentParentId = 0 AND  PostId=$postId ORDER BY CommentCreateAt DESC";
		$commentParent = $this->getData('', $sql);

		foreach ($commentParent as $key => $value) {
			$sql = "
			SELECT CM.CommentId, CM.PostId, CM.UserId, CM.CommentText, CM.CommentParentId, CM.CommentCreateAt, U.UserName, U.UserImage  , CM.ReplyToUserId as Id , (SELECT users.UserName FROM users WHERE UserId = Id) as Owner  
			FROM `comments` CM JOIN `users` U ON CM.UserId=U.UserId AND CommentParentId = " . $value['CommentId'] . " AND PostId= $postId ORDER BY CommentCreateAt ASC;";
			$commentChildren = $this->getData('', $sql);

			if (count($commentChildren) > 0 || !empty($commentChildren)) {
				$commentParent[$key]['children']  = $commentChildren;
			} else {
				$commentParent[$key]['children']  = [];
			}
		}
		echo json_encode(array('success' => 1, 'comments' => $commentParent));
	}

	function queryData($sql)
	{
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->fetch_assoc();
	}

	public function deleteComment($commentId)
	{
		$checkIsParent = $this->queryData('SELECT * FROM `comments` WHERE CommentId=' . $commentId);

		if (empty($checkIsParent)) {
			echo json_encode(['error' => 'Không tìm thấy comment', 'success' => 0]);
			exit;
		}

		if ($checkIsParent['CommentParentId'] == null) {
			# delete parent and children
			$sqlParent = "DELETE FROM `comments` WHERE CommentId = ?";
			$stmt = $this->conn->prepare($sqlParent);
			$stmt->bind_param('i', $commentId);

			if ($stmt->execute()) {
				$sqlChildren = "DELETE FROM `comments` WHERE CommentParentId = ?";
				$stmt = $this->conn->prepare($sqlChildren);
				$stmt->bind_param('i', $commentId);

				if ($stmt->execute()) {
					echo json_encode(['error' => 'Xóa comment thành công', 'success' => 1]);
				}

				exit;
			}
		}

		# delete parent and children
		$sqlParent = "DELETE FROM `comments` WHERE CommentId = ?";
		$stmt = $this->conn->prepare($sqlParent);
		$stmt->bind_param('i', $commentId);

		if ($stmt->execute()) {
			echo json_encode(['error' => 'Xóa comment thành công', 'success' => 1]);
			exit;
		}
	}

	public function editComment($commentId,  $value)
	{
		$sqlQuery = "UPDATE `comments` SET CommentText = ? WHERE CommentId = ? ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('si', $value, $commentId);
		$stmt->execute();

		echo json_encode(['error' => null, 'success' => 1]);
		exit;
	}
}
