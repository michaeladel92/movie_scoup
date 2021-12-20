<?php
ob_start();
session_start();
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];
$session_user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : '';
if($_SERVER["REQUEST_METHOD"] === "POST"){

  if($_POST['trigger'] === 'liked'){
    // check if logged in
    if(!isset($_SESSION['id'])){
      $notifications = ['error' => 'You need to be a member to vote!'];
      exit(json_encode($notifications));
    }
    $movie_id = base64_decode($_POST['id']);
    $movie_id = intval($movie_id);
    //GET movie likes
    $query = "SELECT `likes` FROM `movies` WHERE `id` = :movie_id AND `status_id` = 2 LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':movie_id',$movie_id);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($stmt){
      if($count !== 1){
        $notifications = ['error' => 'Oops, something went wrong!'];
        exit(json_encode($notifications));        
      }else{
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $items = json_decode(str_replace("'","",$row['likes']));
        }
        $likes = [];
        foreach($items as $key => $val){$likes[$key] = $val;} 
        $arr = [];
        foreach($likes['memberLike'] as $val) {$arr[] = $val;}
        $likes['memberLike'] = $arr;
       
        if (($key = array_search($session_user_id, $likes['memberLike'])) !== false) {
          unset($likes['memberLike'][$key]);
        }else{
          array_push($likes['memberLike'],$session_user_id);
        }
        if (($key = array_search($session_user_id, $likes['memberDislike'])) !== false) {
          unset($likes['memberDislike'][$key]);
        }
        $likes['like'] = count($likes['memberLike']); 
        $likes['dislike'] = count($likes['memberDislike']); 
        $likes = json_encode($likes);
     
        $query = "UPDATE `movies` SET `likes` = '{$likes}' WHERE `id` = $movie_id";
        $stmt = $con->query($query);
        if($stmt){
          $notifications = ['success' => ''];
          exit(json_encode($notifications));
        }else{
          $notifications = ['error' => 'Oops, something went wrong!'];
          exit(json_encode($notifications));
        }
      }

    }else{
      $notifications = ['error' => 'Oops, something went wrong!'];
      exit(json_encode($notifications));
    } 
  }
  elseif($_POST['trigger'] === 'disLiked'){
    // array_map ---
       // check if logged in
       if(!isset($_SESSION['id'])){
        $notifications = ['error' => 'You need to be a member to vote!'];
        exit(json_encode($notifications));
      }
      $movie_id = base64_decode($_POST['id']);
      $movie_id = intval($movie_id);
      //GET movie likes
      $query = "SELECT `likes` FROM `movies` WHERE `id` = :movie_id AND `status_id` = 2 LIMIT 1";
      $stmt = $con->prepare($query);
      $stmt->bindParam(':movie_id',$movie_id);
      $stmt->execute();
      $count = $stmt->rowCount();
      if($stmt){
        if($count !== 1){
          $notifications = ['error' => 'Oops, something went wrong!'];
          exit(json_encode($notifications));        
        }else{
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $items = json_decode(str_replace("'","",$row['likes']));
          }
          $disLikes = [];
          foreach($items as $key => $val){$disLikes[$key] = $val;} 
          $arr = [];
          foreach($disLikes['memberDislike'] as $val) {$arr[] = $val;}
          $disLikes['memberDislike'] = $arr;
       
          if (($key = array_search($session_user_id, $disLikes['memberDislike'])) !== false) {
            unset($disLikes['memberDislike'][$key]);
          }else{
            array_push($disLikes['memberDislike'],$session_user_id);
          }
          if (($key = array_search($session_user_id, $disLikes['memberLike'])) !== false) {
            unset($disLikes['memberLike'][$key]);
          }
          $disLikes['like'] = count($disLikes['memberLike']); 
          $disLikes['dislike'] = count($disLikes['memberDislike']); 
          $disLikes = json_encode($disLikes);
       
          $query = "UPDATE `movies` SET `likes` = '{$disLikes}' WHERE `id` = $movie_id";
          $stmt = $con->query($query);
          if($stmt){
            $notifications = ['success' => ''];
            exit(json_encode($notifications));
          }else{
            $notifications = ['error' => 'Oops, something went wrong!'];
            exit(json_encode($notifications));
          }
        }
  
      }else{
        $notifications = ['error' => 'Oops, something went wrong!'];
        exit(json_encode($notifications));
      } 
  
  } 
}