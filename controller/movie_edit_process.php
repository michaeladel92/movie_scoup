<?php
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $token        = $_POST['token'];
    $movie_name   = removeExtraSpace($_POST['movie_name']);
    $movie_name   = htmlspecialchars($movie_name);
    $movie_id     = intval($_POST['movie_id']);
    $movie_series = $_POST['movie_series']; 
    $year         = $_POST['year'];
    $category_id  = intval($_POST['category']);
    // htmlspecialchars_decode()
    $movie_description = trim($_POST['movie_description']);
    $movie_description = htmlspecialchars($movie_description);
    $image     = $_FILES['image'];
    $date      = date("Y/m/d");


    // for reviewing purpose
    $forReviews = [29,30,31,32,33,34,35,36,37,38,39,40];
    if(in_array($movie_id,$forReviews)){
      setMessage('error',"This Post is for review purpose and its not allowed to be edited!🙁");
      $notifications = ['error' => 'redirect'];
      exit(json_encode($notifications));
    }




    // check if the movie belong to the user
    $session_user_id = intval($_SESSION['id']);
    $session_permission_id = intval($_SESSION['permission_id']);
    // make sure the admin permission is updated
    $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
    $is_still_admin = intval(countQuery($query)); //function return count sql

    $sql_condition = $session_permission_id === 1 && $is_still_admin == 1 ? "" : " AND `movies`.`user_id` = $session_user_id";

    $query = "SELECT `movies`.`poster`,`users`.`email` FROM `movies`
                                 INNER JOIN `users`
                                 ON `users`.`id` = `movies`.`user_id` 
                                  WHERE `movies`.`id` = $movie_id $sql_condition";
    $stmt = $con->query($query);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($count === 0){
      //it will redirect thru JS
      setMessage('error',"oops,Something went wrong, Please try again.🙁");
      $notifications = ['error' => 'redirect'];
      exit(json_encode($notifications));
    }else{
      // get data
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      //added the email on db as if admin did any update wont change dir for image 
      $email     = $row['email'];
      //[user trust => 0 Default Post pending | 1 means trustable and post will be published ASAP ]
      // make sure the user trust lvl is updated
      $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `trust_lvl` = 1 LIMIT 1"; 
      $trust_lvl = intval(countQuery($query)); //function return count sql

      //[user account status 1 => pending | 2 => approved | 3 => blocked]
      $status_id = intval($_SESSION['status_id']); 
      $status_in_db = ($trust_lvl == 1 ? 2 : 1); //if trust lvl 1 means post will be approved
      $status_in_db = ($session_permission_id === 1 && $is_still_admin == 1 ?  2 : $status_in_db); // if admin it will be approved

      // CHECK if TOKEN same 
      if(isset($_SESSION['token']) && $_SESSION['token'] === $token){
        // Validate moviename
        if(empty($movie_name)  || $movie_name === ''){
          $notifications = ['error' => 'movie title Can not be empty 🥺'];
          exit(json_encode($notifications));
        }
        elseif(strlen($movie_name) <  3){
          $notifications = ['error' => "movie title must not be less than 3 Char 🥺"];
          exit(json_encode($notifications));
        }
        elseif(strlen($movie_name) > 80){
          $notifications = ['error' => "movie title must not be more than 80 Char 🥺"];
          exit(json_encode($notifications));
        }
        // Validate type 
        elseif(empty($movie_series)  || $movie_series === ''){
          $notifications = ['error' => 'Please choose Type 🥺'];
          exit(json_encode($notifications));
        }
        elseif($movie_series !== 'series'  && $movie_series !== 'movie'){
          $notifications = ['error' => 'oops,Something went wrong, Please try again.🙁'];
          exit(json_encode($notifications));
        }
        // Validate year empty
        elseif(empty($year)  || $year === ''){
          $notifications = ['error' => 'Please choose the released year 🥺'];
          exit(json_encode($notifications));
        }
        elseif(!in_array($year,validateYear())){
          $notifications = ['error' => 'oops,Something went wrong, Please try again.🙁'];
          exit(json_encode($notifications));
        }
        // Validate category empty
        elseif(empty($category_id)  || $category_id === ''){
          $notifications = ['error' => 'Please choose category 🥺'];
          exit(json_encode($notifications));
        }
        elseif(countRows('id','categories',$category_id) !== 1 ){
          $notifications = ['error' => 'oops,Something went wrong, Please try again.🙁'];
          exit(json_encode($notifications));
        }
        // Validate description
        elseif(empty($movie_description)  || $movie_description === ''){
          $notifications = ['error' => 'description can not be empty 🥺'];
          exit(json_encode($notifications));
        }
        elseif(strlen($movie_description) <  200){
          $notifications = ['error' => "description must not be less than 200 Char 🥺"];
          exit(json_encode($notifications));
        }
        elseif(strlen($movie_description) > 50000){
          $notifications = ['error' => "description must not be more than 50,000 Char 🥺"];
          exit(json_encode($notifications));
        }else{

              // Image process
              $image_result = '';
              if(!empty($_FILES['image']['name']) || $_FILES['image']['name'] !==  ''){

                $image     = $_FILES['image'];
                $img_name  = $image['name'];
                $img_tmp   = $image['tmp_name'];
                $img_error = $image['error'];
                $img_size  = $image['size'];
                
                $extension = explode('.',$img_name);
                $extension = strtolower(end($extension));
                $random    = md5(rand(1,1000000).uniqid(mt_rand(), true).time());
                $new_name  = strtolower($random.'.'.$extension);
                $img_dir   = "../img/upload/$email/".$new_name;
                $img_db    = "img/upload/$email/".$new_name;
                $allowed   = ['png','jpg','jpeg'];
                if(!in_array($extension,$allowed)){
                  
                  $notifications = ['error' => "Allowed only the following extensions ['png','jpg','jpeg']"];
                  exit(json_encode($notifications));
                  
                }else{
                    
                    if($img_error !== 0){
                      $notifications = ['error' => "oops,Something went wrong, Please try again."];
                      exit(json_encode($notifications));
                    }
                    elseif($img_size > 7000000){
                      $notifications = ['error' => "Please note that the maximum accepted size is 7mb"];
                      exit(json_encode($notifications));
                    }
                    else{
                      // Create folder for each user by email
                      if(!file_exists("../img/upload/$email/")) {
                          mkdir("../img/upload/$email/", 0777, true);
                      }

                      if(!move_uploaded_file($img_tmp,$img_dir)){			  
                        $notifications = ['error' => "An Error accrued while uploading image"];
                        exit(json_encode($notifications));	 
                      }else{
                        // get old image
                        $old_image = '../'.$row['poster'];
                        unlink($old_image);
                        $image_result = $img_db;
                      }
                      
                    }
                }
              }
            
              //means user is blocked and not allowed to POST
              if(intval($status_id) !== 3){ 
              //UPDATE DB
              $sql_img_to_upload = $image_result === '' ? '' : "`poster` = :img_db,";
              $query = "UPDATE `movies` SET
                                              `movie_or_series` = :movie_series,
                                              `movie_name` = :movie_name,
                                              `description` = :movie_description,
                                              $sql_img_to_upload
                                              `year` = :yearN,
                                              `category_id` = :category_id,
                                              `published_date` = :date_,
                                              `status_id` = :status_in_db,
                                              `action_users_id` = :action_taken
                                        WHERE
                                               `id` = :movie_id      
                                              ";

                $stmt = $con->prepare($query);
                $stmt->bindParam(':movie_series',$movie_series);
                $stmt->bindParam(':movie_name',$movie_name);
                $stmt->bindParam(':movie_description',$movie_description);
                if($image_result !== ''){
                  $stmt->bindParam(':img_db',$img_db);
                }
                $stmt->bindParam(':yearN',$year);
                $stmt->bindParam(':category_id',$category_id);
                $stmt->bindParam(':date_',$date);
                $stmt->bindParam(':status_in_db',$status_in_db);
                $stmt->bindParam(':action_taken',$session_user_id);
                $stmt->bindParam(':movie_id',$movie_id);
                $stmt->execute();
                if($stmt){
                  setMessage('success',"post updated successfully 😄");
                  $notifications = ['success' => $movie_description];
                  exit(json_encode($notifications));
                }else{
                  $notifications = ['error' => 'oops,Something went wrong, Please try again.🙁'];
                  exit(json_encode($notifications));
                }
              }else{
                $notifications = ['error' => 'access denied!, your account has been blocked by the admin at the moment 🙁'];
                exit(json_encode($notifications));
              }   
          }

    }else{
    $notifications = ['error' => 'oops,Something went wrong, Please try again.🙁'];
    exit(json_encode($notifications));
    }
    }

}