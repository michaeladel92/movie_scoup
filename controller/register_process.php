<?php
ob_start();
session_start();
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

  // validate length
  $nameMax     = 50;
  $nameMin     = 3;
  $emailMax    = 70;
  $emailMin    = 10;
  $passMax     = 50;
  $passMin     = 6;
  // variables
  $token        = $_POST['token'];
  
  // $name         = urlencode($_POST['user_name']);
  $name         = removeExtraSpace($_POST['user_name']);
  $name         = htmlspecialchars($name);  
    
  $email        = strtolower($_POST['email']);
  $email        = trim($email);
  $email        = filter_var($email, FILTER_SANITIZE_EMAIL);
  
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
    //Validate Email
    elseif(empty($email) || $email === ""){
      $notifications = ['error' => "Email Can not be empty"];
      exit(json_encode($notifications));
    }
    elseif(strlen($email) < $emailMin){
      $notifications = ['error' => "Email must not be less than $emailMin Char"];
      exit(json_encode($notifications));
    }
    elseif(strlen($email) > $emailMax){
      $notifications = ['error' => "Email must not be more than $emailMax Char"];
      exit(json_encode($notifications));
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $notifications = ['error' => "Email is not valid"];
      exit(json_encode($notifications));
    }
    // validate password
    elseif(empty($password) || $password === ""){
      $notifications = ['error' => "Password Can not be empty"];
      exit(json_encode($notifications));
    }
    elseif(strlen($password) < $passMin){
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

      //Avoid Duplicate Emails
      $count = countRows('email','users',$email); 
      if($count > 0 ){
        $notifications = ['error' => "email address already exist!"];
        exit(json_encode($notifications));
      
      }else{
          // Hash password
          $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
          $imageUploadDataBase = '';
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
                  // get the image name that will upload in database 
                  $imageUploadDataBase = $img_db; 
                }
            }
         }else{
          //  add the default image depend on [male or female]
          $imageUploadDataBase = ($gender ==='male' ? "img/male.png" : "img/female.png");
         }
      
        // INSERT USER INTO DATABASE
        $query = "INSERT INTO `users` (`user_name`,
                                        `email`,
                                        `password`,
                                        `gender`,
                                        `image`,
                                        `about`,
                                        `reg_date`) 
                                      VALUES (?,?,?,?,?,?,?)";
          $stmt = $con->prepare($query);
          $stmt->bindParam(1,$name);                            
          $stmt->bindParam(2,$email);                            
          $stmt->bindParam(3,$password);                            
          $stmt->bindParam(4,$gender);                            
          $stmt->bindParam(5,$imageUploadDataBase);                            
          $stmt->bindParam(6,$about);                            
          $stmt->bindParam(7,$date);
          $stmt->execute();
          if($stmt){
            $id = $con->lastInsertId();
            /*
            GET ALL VALUES IN DB accept parameter [table name | col to compare | value given]
            @return fetch()
            */ 
            $row = getAllQuery('users','id',$id);

            $_SESSION['id']     = $row['id'];
            $_SESSION['name']   = $row['user_name'];
            $_SESSION['email']  = $row['email'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['image']  = $row['image'];
            $_SESSION['about']  = $row['about'];
            $_SESSION['permission_id'] = $row['permission_id'];
            $_SESSION['reg_date']      = $row['reg_date'];
            $_SESSION['status_id']     = $row['status_id'];
            $_SESSION['trust_lvl']     = $row['trust_lvl'];
            $name_ = (strlen($_SESSION['name']) > 10 ? substr($_SESSION['name'],0,10).'.':$_SESSION['name'] );
            setMessage('success',"Register Successfully, Welcome $name_ 😄");
            $notifications = ['success' => ""];
            exit(json_encode($notifications));
          } else{
            $notifications = ['error' => "Someting went wrong"];
            exit(json_encode($notifications));	
          }                        
          
          
          
          
          
          
                                     

      }
      
      // $notifications = ['success' => $stmt];
      // exit(json_encode($notifications));
    
    }

    
  }else{
    //Token ERROR
    $notifications = ['error' => 'oops,Something went wrong, Please try again.'];
    exit(json_encode($notifications));
  }



}else{
  
  // IF METHOD NOT POST
  $notifications = ['error' => 'Denied Acess, Please try again later!'];
  exit(json_encode($notifications));
}