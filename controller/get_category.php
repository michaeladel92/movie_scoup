<?php
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];
if($_SERVER['REQUEST_METHOD'] === 'GET'){
  
  $query = "SELECT * FROM `categories` ORDER BY `category_name` DESC";
  $stmt  = $con->query($query);
  $count = $stmt->rowCount();
  if($stmt){
    if($count > 0){
      $arr = [];
      while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $arr[] = $rows;
      }
      $notifications = ['success'=> $arr];
      exit(json_encode($notifications));
  
    }else{
      $notifications = ['error'=> "No categories found!"];
      exit(json_encode($notifications));
    }
  }
}
