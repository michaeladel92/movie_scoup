<?php
require_once("connect.php");
require_once("functions.php");
$notifications = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $token        = $_POST['token'];
    $movie_name   = removeExtraSpace($_POST['movie_name']);
    $movie_name   = htmlspecialchars($movie_name);

    $movie_series = $_POST['movie_series']; 
    $year         = $_POST['year'];
    $category_id  = intval($_POST['category']);
    // htmlspecialchars_decode()
    $movie_description = trim($_POST['movie_description']);
    $movie_description = htmlspecialchars($movie_description);
    $image     = $_FILES['image'];
    $date      = date("Y/m/d");

    $email         = $_SESSION['email'];
    $permission_id = intval($_SESSION['permission_id']);

    //[user trust => 0 Default Post pending | 1 means trustable and post will be published ASAP ]
    // $trust_lvl = intval($_SESSION['trust_lvl']);
    $user_id   = intval($_SESSION['id']);
     // make sure that the user still exist[if user still log in but admin deleted user account]
     $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $user_id LIMIT 1"; 
     $user_exist = intval(countQuery($query)); //function return count sql
     if($user_exist === 0){
      $notifications = ['error' => 'oops,Something went wrong, Please try again.🙁'];
      exit(json_encode($notifications));
     } 
    // make sure the admin permission is updated
    $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $user_id AND `permission_id` = 1 LIMIT 1"; 
    $is_still_admin = intval(countQuery($query)); //function return count sql

    // make sure the admin permission is updated
    $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $user_id AND `trust_lvl` = 1 LIMIT 1"; 
    $trust_lvl = intval(countQuery($query)); //function return count sql

    //[user account status 1 => pending | 2 => approved | 3 => blocked]
    $status_id = intval($_SESSION['status_id']); 
    $status_in_db = ($trust_lvl == 1 ? 2 : 1); //if trust lvl 1 means post will be approved
    $status_in_db = ($permission_id === 1 && $is_still_admin == 1 ?  2 : $status_in_db); // if admin it will be approved
    
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
                      }
                    }
                }
              }else{
              $notifications = ['error' => 'uploading image is mandatory🙁'];
              exit(json_encode($notifications));
              }
            //means user is blocked and not allowed to POST
            if(intval($status_id) !== 3){ 
             //INSERT TO DB
             $query = "INSERT INTO `movies`(
                                            `movie_or_series`,
                                            `movie_name`,
                                            `description`,
                                            `poster`,
                                            `year`,
                                            `category_id`,
                                            `user_id`,
                                            `published_date`,
                                            `status_id`)
                                    VALUES(
                                            :movie_series,
                                            :movie_name,
                                            :movie_description,
                                            :img_db,
                                            :yearN,
                                            :category_id,
                                            :user_,
                                            :date_,
                                            :status_in_db
                                    )";
              $stmt = $con->prepare($query);
              $stmt->bindParam(':movie_series',$movie_series);
              $stmt->bindParam(':movie_name',$movie_name);
              $stmt->bindParam(':movie_description',$movie_description);
              $stmt->bindParam(':img_db',$img_db);
              $stmt->bindParam(':yearN',$year);
              $stmt->bindParam(':category_id',$category_id);
              $stmt->bindParam(':user_',$user_id);
              $stmt->bindParam(':date_',$date);
              $stmt->bindParam(':status_in_db',$status_in_db);
              $stmt->execute();
              if($stmt){
                setMessage('success',"post submited successfully 😄");
                $notifications = ['success' => ''];
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
?>
<h1 class="title">Add</h1>
<h4 class="sub-title">You can publish your opinion about movie/series</h4> 

<form class="add_movie" id="add_movie" enctype="multipart/form-data">
    <div class="form-container">
        <!--token  -->
        <input type="hidden" name="token" value="<?= isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>">
      <!-- movie_name	 -->
      <div class="control">
        <label for="movie_name">movie/series name</label>
        <input id="movie_name" type="text" name="movie_name" placeholder="title of the movie / series">
      </div>
      <!-- movie_or_series-->
      <div class="control">
        <label for="movie_series">type</label>
        <select id="movie_series" name="movie_series">
          <option value="">choose type</option>
          <option value="movie">movie</option>
          <option value="series">series</option>
        </select>
      </div>
      <!-- year	 -->
      <div class="control">
              <label for="year">published year</label>
              <select id="year" name="year">
                <?= yearOptions();?>
              </select>
        </div>
      <!-- category_id 	 -->
      <div class="control">
              <label for="category">category</label>
              <select id="category" name="category">
                <?php echo categoriesSelectionForm(); ?>
              </select>
      </div>
      <!-- description-->
      <div class="control">
        <label for="movie_description">review</label>
        <textarea id="movie_description" name="movie_description" placeholder="Great thoughts come from the heart"></textarea>
      </div>  
      <!-- poster-->
      <div class="control">
          <label for="image">poster</label>
          <input id="image" type="file" name="image" placeholder="image">
      </div>
      <!-- submit -->
      <div class="control">
        <input type="submit" class="btn btn_add" name="submit" value="submit">
      </div>

    </div>
</form>