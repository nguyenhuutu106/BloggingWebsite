<?php
class Admin {	
   
	private $userTable = 'users';	
    private $postTable = 'posts';	
    private $categoryTable = 'categories';	
    private $postCategoryTable = 'post_category';		
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function loggedIn (){
		if(isset($_SESSION["userAccount"])) {
			return 1;
		} else {
			return 0;
		}
	}
    public function countUsers (){
		if(isset($_SESSION["userAccount"])) {
			$sqlQuery = " SELECT COUNT(UserId) as total FROM users; ";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();			
			$result = $stmt->get_result();	
            $followingdata = $result->fetch_assoc();
			return $followingdata['total'];
		}else {
            return "nodata";
        }
    }
    public function countPosts (){
		if(isset($_SESSION["userAccount"])) {
			$sqlQuery = " SELECT COUNT(PostId) as total FROM posts; ";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();			
			$result = $stmt->get_result();	
            $followingdata = $result->fetch_assoc();
			return $followingdata['total'];
		}else {
            return "nodata";
        }
    }
    public function countCategories (){
		if(isset($_SESSION["userAccount"])) {
			$sqlQuery = " SELECT COUNT(CategoryId) as total FROM categories; ";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();			
			$result = $stmt->get_result();	
            $followingdata = $result->fetch_assoc();
			return $followingdata['total'];
		}else {
            return "nodata";
        }
    }
    public function countViews (){
		if(isset($_SESSION["userAccount"])) {
			$sqlQuery = "SELECT SUM(PostView) AS total FROM posts;";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();			
			$result = $stmt->get_result();	
            $followingdata = $result->fetch_assoc();
			return $followingdata['total'];
		}else {
            return "nodata";
        }
    }
    public function getAllUser($userId){	
		if(isset($userId)) {
			$sqlQuery = "
				SELECT *
				FROM ".$this->userTable." 
				WHERE NOT UserId = '".$userId."'
				ORDER BY UserRole DESC";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();			
			$result = $stmt->get_result();		
			return $result;			
		}else{
            return "nodata";
        }
	}
    public function getAllPost(){	
		$sqlQuery = "
        SELECT DISTINCT posts.PostId, posts.PostTitle, users.UserName, posts.PostDate, posts.PostThumbnail 
        FROM `".$this->postTable."`, `".$this->userTable."` 
		WHERE users.UserId = posts.UserId ORDER BY posts.PostDate DESC;";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
	}
    public function getAllCategory(){	
		$sqlQuery = "
        SELECT * 
        FROM `".$this->categoryTable."`";
		$stmt = $this->conn->prepare($sqlQuery);
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
				$notificationText ='<a style="all: unset;" href="'.ROOT_URL.'post.php?id='.$postId.'"> <br>' .$userName.'</b> đã đăng tải bài viết mới </a>';

				$insertNoti = "
				INSERT INTO `notifications`
				(`UserId`, `PostId`, `FromUserId`, `NotificationType` , `NotificationText`) 
				VALUES (?, ?, ?, ?, ?)";

				$stmt3 = $this->conn->prepare($insertNoti);	
				$stmt3->bind_param('iiiss', $notificationRow['FollowerId'], $postId, $this->userId, "Post" , $notificationText);
				$stmt3->execute();
			}
			return 1;
		}else {
			return "nodata";
		}
	}
	public function createUser() {
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
				$sqlQuery = "
				INSERT INTO ".$this->userTable."(`UserAccount`, `UserName`, `UserEmail`, `UserPassword`, `UserRole`) 
				VALUES (?, ?, ?, ?, ?)";
				$stmt = $this->conn->prepare($sqlQuery);
				$password = md5($this->password);	
				$stmt->bind_param('sssss', $this->account, $this->name, $this->email, $password, $this->role);
				$stmt->execute();
				return 1;
			}
		}
		else {
			return "nodata";
		}
    }
	public function createCategory() {
		if($this->name && $this->image) {	
			$sqlCheckName = "SELECT * FROM ".$this->categoryTable." WHERE CategoryName = ? ";	
			$stmt = $this->conn->prepare($sqlCheckName);
			$stmt->bind_param('s', $this->name);
			$stmt->execute();
			$name = $stmt->get_result();
			if($name->num_rows > 0) {
				return "name";
			}else {
				$sqlQuery = "
				INSERT INTO ".$this->categoryTable."(`CategoryName`, `CategoryContent`, `CategoryImage`) 
				VALUES (?, ?, ?)";
				$stmt = $this->conn->prepare($sqlQuery);
				$stmt->bind_param('sss', $this->name, $this->content, $this->image);
				$stmt->execute();
				return 1;
			}
		}
		else {
			return "nodata";
		}
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
	public function getCategoryId($id){	
		$sqlQuery = "
        SELECT categories.CategoryName, categories.CategoryContent, categories.CategoryImage
		FROM `".$this->categoryTable."`
		WHERE categories.CategoryId = ".$id.";";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;			
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
	public function editUser($id){
		if($this->name && $this->image) {
			$sqlEditUser = "
			UPDATE ".$this->userTable."
			SET UserName = ?, UserImage = ?, UserBio = ?, UserRole = ?
			WHERE UserId = ".$id." ";
			$stmt = $this->conn->prepare($sqlEditUser);	
			$stmt->bind_param('ssss', $this->name, $this->image, $this->bio, $this->role);
			$stmt->execute();
			return 1;
		}else {
			return "nodata";
		}
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
	public function editCategory($id){
		if($this->name && $this->image && $this->content) {
			$sqlCheckName = "
			SELECT * FROM ".$this->categoryTable." 
			WHERE CategoryName = ? AND CategoryId NOT LIKE ?";	
			$stmt = $this->conn->prepare($sqlCheckName);
			$stmt->bind_param('ss', $this->name, $id);
			$stmt->execute();
			$name = $stmt->get_result();
			if($name->num_rows > 0) {
				return "name";
			}else{
				$sqlEditCategory = "
				UPDATE ".$this->categoryTable."
				SET CategoryName = ?, CategoryContent = ?, CategoryImage = ?
				WHERE CategoryId = ".$id." ";
				$stmt = $this->conn->prepare($sqlEditCategory);	
				$stmt->bind_param('sss', $this->name, $this->content, $this->image);
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
	public function deleteCategory($id){		
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->categoryTable." 
			WHERE CategoryId = ?");
		$id = htmlspecialchars(strip_tags($id));
		$stmt->bind_param("s", $id);
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
	public function deleteUser($id){		
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->userTable." 
			WHERE UserId = ?");
		$id = htmlspecialchars(strip_tags($id));
		$stmt->bind_param("s", $id);
		if($stmt->execute()){
			$output = array(			
				"success"	=> 	1
			);
			echo json_encode($output);
		}
	}
}