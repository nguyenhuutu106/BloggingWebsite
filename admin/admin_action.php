<?php
require '../config/database.php';
require 'class/admin.php';

$database = new Database();
$db = $database->getConnection();

$admin = new Admin($db);

if(!empty($_POST['action']) && $_POST['action'] == 'deletePost') {
	$id = $_POST["PostId"];	
	$admin->deletePost($id);
}
if(!empty($_POST['action']) && $_POST['action'] == 'deleteCategory') {
	$id = $_POST["CategoryId"];	
	$admin->deleteCategory($id);
}
if(!empty($_POST['action']) && $_POST['action'] == 'deleteUser') {
	$id = $_POST["UserId"];	
	$admin->deleteUser($id);
}
?>