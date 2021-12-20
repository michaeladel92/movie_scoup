<?php
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];

if($_SERVER['REQUEST_METHOD'] === 'GET'){

$session_user_id = intval($_SESSION['id']);
$session_permission_id = intval($_SESSION['permission_id']); //admin
// for admin will get all memebrs | users will add a condition to get by user id
$sql_condition = $session_permission_id === 1 ? "" : "WHERE `movies`.`user_id` = $session_user_id";
/*
===============================
Query Get All movies
===============================
*/
  $query = "SELECT 
                  `movies`.`id` AS movie_id,
                  `movies`.`movie_or_series`,
                  `movies`.`movie_name`,
                  `movies`.`poster`,
                  `movies`.`year`,
                  `movies`.`category_id`,
                  `movies`.`user_id`,
                  `movies`.`likes`,
                  `movies`.`views`,
                  `movies`.`status_id` AS movie_status,
                  `users`.`user_name`,
                  `categories`.`category_name`
                           FROM `movies` 
                                INNER JOIN `users` ON `users`.`id` = `movies`.`user_id` 
                                INNER JOIN `categories` ON `categories`.`id` = `movies`.`category_id`
                           $sql_condition
                           ORDER BY `movies`.`id` DESC      
              ";
  $stmt  = $con->query($query);
  $count = $stmt->rowCount();  

  if($stmt){
    if($count > 0){
      $total_movies_table_arr = []; //add SQL array rows into php array
      while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $total_movies_table_arr[] = $rows;
      }

      // update the array movies 
      for($i = 0; $i < count($total_movies_table_arr); $i++){
        //so that we wont have trouble in switching to json array in js
          // $total_movies_table_arr[$i]['likes'] = str_replace("'", "", $total_movies_table_arr[$i]['likes']);
        // shirk long strings
          $total_movies_table_arr[$i]['movie_name'] = (
            strlen($total_movies_table_arr[$i]['movie_name']) > 15 ? 
            substr($total_movies_table_arr[$i]['movie_name'],0,15).'.':
            $total_movies_table_arr[$i]['movie_name']
          ); 
        // give data = null if not admin
          $total_movies_table_arr[$i]['user_name'] = $session_permission_id === 1 ? (
            strlen($total_movies_table_arr[$i]['user_name']) > 15 ? 
            substr($total_movies_table_arr[$i]['user_name'],0,15).'.':
            $total_movies_table_arr[$i]['user_name']
          ) : null;
          
      }
/*
===============================
INSIGHT - BOXES Count
===============================
*/ 
      // total view 
      $conditionsQuery = $session_permission_id === 1 ? "":" AND `user_id` = $session_user_id";
      //published post [2] ==> approved 
      $publish_count = "SELECT COUNT(id) FROM `movies` WHERE `status_id` = 2 $conditionsQuery";
       //published comment [2] ==> approved 
      $comment_count = "SELECT COUNT(id) FROM `comments` WHERE `status_id` = 2 $conditionsQuery";
      /*
      published users All 
      NOTE[pending|approved] ==> for training purpose i did not add waiting to append or any other permissions as pending and active users are the same at the moment 
      */
      $user_count = "SELECT COUNT(id) FROM `users`";
      // pending movies
      $pending_article_count = "SELECT COUNT(id) FROM `movies` WHERE `status_id` = 1";
      //pending comments
      $pending_comment_count = "SELECT COUNT(id) FROM `comments` WHERE `status_id` = 1";



      //all queries result will be added inside this array
      $total_array = []; 
      //query get all movies result
      $total_array['get_all_movies'] = $total_movies_table_arr; 
      //string to determain the layout of user and admin
      $total_array['permission'] = $session_permission_id === 1 ? 'admin' : 'user';
      //function query that get all views with a condition of user id , if param empty it get total published views for all users 
      $total_array['view_count'] = totalViews($conditionsQuery);
      // Accept param full sql query and return count published
      $total_array['publish_count'] =  countQuery($publish_count);
       // Accept param full sql query and return count comment
      $total_array['comment_count'] =  countQuery($comment_count);
      // Accept param full sql query and return count comment 
      $total_array['user_count'] =  $session_permission_id === 1 ? countQuery($user_count) : null;
      // Accept param full sql query and return count pending movies 
      $total_array['pending_article_count'] =  $session_permission_id === 1 ? countQuery($pending_article_count) : null;
      // Accept param full sql query and return count pending comments
      $total_array['pending_comment_count'] =  $session_permission_id === 1 ? countQuery($pending_comment_count) : null;
      
      // send to javascript to handle
      $notifications = ['success'=> $total_array];
      exit(json_encode($notifications));
  
    }else{
      $notifications = ['error'=> "No movies found!"];
      exit(json_encode($notifications));
    }
  }else{
    $notifications = ['error'=> "No movies found!"];
    exit(json_encode($notifications));
  }
}