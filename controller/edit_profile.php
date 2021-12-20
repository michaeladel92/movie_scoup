<?php
ob_start();
session_start();
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];


if($_SERVER['REQUEST_METHOD'] === "POST"){

  if($_POST['trigger'] === 'edit_user'){
  
    if(isset($_SESSION['id'])){
      // check if exist in db to get the updated
      $count = countRows('id','users',$_SESSION['id']);
      if($count !== 1){
        setMessage('error','access denied!');
        $notifications = ['error',''];
        exit(json_encode($notifications));
      }
    }else{
        setMessage('error','access denied!');
        $notifications = ['error',''];
        exit(json_encode($notifications));
    }


     // validate length
  $nameMax     = 50;
  $nameMin     = 3;
  $emailMax    = 70;
  $emailMin    = 10;
  $passMax     = 50;
  $passMin     = 6;
  // variables
  $user_id      = intval($_SESSION['id']);
  $email        = $_SESSION['email'];
  $token        = $_POST['token'];
  
  // $name         = urlencode($_POST['user_name']);
  $name         = removeExtraSpace($_POST['user_name']);
  $name         = htmlspecialchars($name);  
  
  $password     = trim($_POST['password']);
  $con_password = trim($_POST['con_password']);
  
  $gender       = trim($_POST['gender']);

  $about        = removeExtraSpace($_POST['about']);
  $about        = htmlspecialchars($about);

  $image        = $_FILES['image'];
  $date         =  date("Y/m/d");

  // CHECK if TOKEN same 
  if(isset($_SESSION['token']) && $_SESSION['token'] === $token ){

    // Validate name
    if(empty($name)  || $name === ''){
      $notifications = ['error' => 'Name Can not be empty'];
      exit(json_encode($notifications));
    }
    elseif(strlen($name) < $nameMin){
      $notifications = ['error' => "Name must not be less than $nameMin Char"];
      exit(json_encode($notifications));
    }
    elseif(strlen($name) > $nameMax){
      $notifications = ['error' => "Name must not be more than $nameMax Char"];
      exit(json_encode($notifications));
    }
    elseif(preg_match("/[\[^\'$%^&*()}{@:\'#~?><>,;@\|\\-=\-_+\-\`\]]/",$name)){
      $notifications = ['error' => "Name must not contain symbols"];
      exit(json_encode($notifications));
    }
    elseif(is_numeric($name[0])){
      $notifications = ['error' => "Name can not start with a number"];
      exit(json_encode($notifications));
    }
    //validate gender
    elseif($gender !== 'male' && $gender !== 'female' ){
      $notifications = ['error' => "Please choose gender"];
      exit(json_encode($notifications));
    }
    //validate about
    elseif(strlen($about) > 500 ){
      $notifications = ['error' => "maximum length for about me exceeded 500 char"];
      exit(json_encode($notifications));
    }
    else{

      // check if user entered a password 
        if(!empty($password) || $password !== ""){
          //for review purpose cannot change password
          if($_SESSION['email'] === 'admin@admin.com'){
            $notifications = ['error' => "this account is for review purpose, not allowed to change password!"];
            exit(json_encode($notifications));
          }

          if(strlen($password) < $passMin){
            $notifications = ['error' => "Password must not be less than $passMin Char"];
            exit(json_encode($notifications));
          }
          elseif(strlen($password) > $passMax){
            $notifications = ['error' => "Password must not be more than $passMax Char"];
            exit(json_encode($notifications));
          }
          elseif($password !== $con_password){
            $notifications = ['error' => "Password Not Match"];
            exit(json_encode($notifications));
          }
        }
        $password_conclusion = (!empty($password) || $password !== "" ? $password : ''); 
    
        // Hash password
        if($password_conclusion !== '' || !empty($password_conclusion)){
          $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $imageUploadDataBase = '';
        // get old profile
        $query = "SELECT `image` FROM `users` WHERE `id` = $user_id";
        $stmt  = $con->query($query);
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        $old_image = $row['image'];
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
                // get old profile
                $query = "SELECT `image` FROM `users` WHERE `id` = $user_id";
                $stmt  = $con->query($query);
                $row   = $stmt->fetch(PDO::FETCH_ASSOC);
                $old_image = $row['image'];
                if($old_image !== 'img/male.png' || $old_image !== 'img/female.png'){
                    unlink('../'.$old_image);
                }
                // get the image name that will upload in database 
                $imageUploadDataBase = $img_db; 
              }
          }
        }else{

          if($old_image === 'img/male.png' || $old_image === 'img/female.png'){
            if($gender === 'male'){$imageUploadDataBase = 'img/male.png';}
            elseif( $gender == 'female'){$imageUploadDataBase = 'img/female.png';}
          }
        }

      // password SQL
      $password_sql = (
                        $password_conclusion !== '' ||
                        !empty($password_conclusion) ? 
                        "`password`  = :password_," : "");
      //IMAGE SQL 
      $image_sql = (
                    $imageUploadDataBase !== '' ||
                    !empty($imageUploadDataBase) ?
                    "`image`     = :image_,":"");
      // UPDATE USER INTO DATABASE
      $query = "UPDATE `users` SET 
                                  `user_name` = :user_name_,
                                  $password_sql 
                                  `gender`    = :gender_, 
                                  $image_sql
                                  `about`     = :about_ 
                              WHERE 
                                   `id` = :user_id_    
                                  ";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':user_name_',$name);                            
        $stmt->bindParam(':about_',$about);                            
        $stmt->bindParam(':gender_',$gender); 
        $stmt->bindParam(':user_id_',$user_id); 
        if($imageUploadDataBase !== '' || !empty($imageUploadDataBase)){
          $stmt->bindParam(':image_',$imageUploadDataBase); 
        }
        if($password_conclusion !== '' || !empty($password_conclusion)){
          $stmt->bindParam(':password_',$password);                            
        }      
        $stmt->execute();

        if($stmt){
          /*
          GET ALL VALUES IN DB accept parameter [table name | col to compare | value given]
          @return fetch()
          */ 
          $row = getAllQuery('users','id',$user_id);

          $_SESSION['name']   = $row['user_name'];
          $_SESSION['email']  = $row['email'];
          $_SESSION['gender'] = $row['gender'];
          $_SESSION['image']  = $row['image'];
          $_SESSION['about']  = $row['about'];
          $_SESSION['permission_id'] = $row['permission_id'];
          $_SESSION['reg_date']      = $row['reg_date'];
          $_SESSION['status_id']     = $row['status_id'];
          $_SESSION['trust_lvl']     = $row['trust_lvl'];
          setMessage('success',"personal info updated Successfully! 😄");
          $notifications = ['success' => ""];
          exit(json_encode($notifications));
        } else{
          $notifications = ['error' => "Someting went wrong!"];
          exit(json_encode($notifications));	
        }                          
    }
    
  }else{
    //Token ERROR
    $notifications = ['error' => 'oops,Something went wrong, Please try again.'];
    exit(json_encode($notifications));
  }

  }
}