<?php
require 'config/database.php';
require 'class/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!empty($_POST['action']) && $_POST['action'] == 'deletePost') {
	$id = $_POST["PostId"];	
	$user->deletePost($id);
}
if(!empty($_POST['action']) && $_POST['action'] == 'likePost') {
	$postId = $_POST["PostId"];	
	$userId = $_POST["UserId"];	
	$user->likePost($postId, $userId);
}
if(!empty($_POST['action']) && $_POST['action'] == 'unlikePost') {
	$postId = $_POST["PostId"];	
	$userId = $_POST["UserId"];	
	$user->unLikePost($postId, $userId);
}
if(!empty($_POST['action']) && $_POST['action'] == 'followUser') {
	$userId = $_POST["UserId"];	
	$followerId = $_POST["FollowerId"];	
	$user->followUser($followerId, $userId);
}
if(!empty($_POST['action']) && $_POST['action'] == 'unFollowUser') {
	$userId = $_POST["UserId"];	
	$followerId = $_POST["FollowerId"];	
	$user->unFollowUser($followerId, $userId);
}

if (!empty($_POST['action']) && $_POST['action'] == 'commentPost') {
	$postId = $_POST["postId"];
	$userId = $_POST["userId"];
	$value = $_POST["value"];
	$parentIdPostComment = $_POST["parentIdPostComment"];
	$replyId = $_POST['replyId'];

	return $user->commentPost($postId, $userId, $value, $parentIdPostComment, $replyId);
}

if (!empty($_GET['action']) && $_GET['action'] == 'getComment') {
	$postId = $_GET["postId"];
	return $user->getComment($postId);
}

if (!empty($_POST['action']) && $_POST['action'] == 'deleteComment') {
	$commentId = $_POST["commentId"];

	return $user->deleteComment($commentId);
}

if (!empty($_POST['action']) && $_POST['action'] == 'editComment') {
	$commentId = $_POST["commentId"];
	$value = $_POST["value"];

	return $user->editComment($commentId, $value);
}

if (!empty($_POST['action']) && $_POST['action'] == 'fetchNotification') {
	$id = $_POST["UserId"];	
	return $user->CountNotification($id);
}
if (!empty($_POST['action']) && $_POST['action'] == 'updateNotification') {
	$notiId = $_POST['NotiId'];

	return $user->updateNotification($notiId);
}


?>