<?php
ob_start();
session_start();
require_once("../inc/connect.php");
require_once("../inc/functions.php");
$notifications = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $token    = $_SESSION['token'];
  $email    = strtolower($_POST['email']);
  $email    = trim($email);
  $email    = filter_var($email, FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'];
  
  if(isset($_SESSION['token']) && $_SESSION['token'] === $token){
    // Check if email exist
  
      //Validate Email
      if(empty($email) || $email === ""){
        $notifications = ['error' => "Please Enter Email address!"];
        exit(json_encode($notifications));
      }
      elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $notifications = ['error' => "Please enter a valid email address!"];
        exit(json_encode($notifications));
      }else{
        //check if email exist function param[col|table|val] return rowCount
        $count = countRows('email','users',$email);
      
        if($count === 1){
          // get all data and verify password if correct [table | column | value]
          $row  = getAllQuery("users",'email',$email);
          if(password_verify($password,$row['password'])){
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
              $name = strlen($_SESSION['name']) > 10 ? substr($_SESSION['name'],0,10).'.':$_SESSION['name'];
              setMessage('success',"Welcome back!, $name 😄");
              $notifications = ['success' => ""];
              exit(json_encode($notifications));
          }else{
            // Wrong Password
            $entry_attempt = isset($_SESSION['entry_attempt']) ? $_SESSION['entry_attempt'] +=1 : $_SESSION['entry_attempt'] = 0; 
            if($entry_attempt > 5){
              $notifications = ['error' => "You've entered incorrect password multiple times, you will be redirected",'entry_attempt' => 'failed'];
              unset($_SESSION['entry_attempt']);
              exit(json_encode($notifications));
            }else{
              $notifications = ['error' => 'The password that you entered is incorrect🙁'];
              exit(json_encode($notifications));
            }
       
          }

        }else{
          $notifications = ['error' => "Email not exist in our database! 🙁"];
          exit(json_encode($notifications));
        }
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