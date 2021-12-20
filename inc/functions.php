<?php
if (session_status() === PHP_SESSION_NONE) {
  ob_start();
  session_start();
}
function token_generator(){
  //Generate a random string.
  $token = openssl_random_pseudo_bytes(16);
  //Convert the binary data into hexadecimal representation.
  $token = bin2hex($token);
  //Print it out for example purposes.
  return $token;
}

/*
Count specific parameter and return total numbers found 
*/ 
function countRows($col,$table,$val){
  global $con;
  $query = "SELECT `{$col}` FROM `{$table}` WHERE `{$col}` = ?";
  $stmt  = $con->prepare($query);
  $stmt->bindParam(1,$val);
  $stmt->execute();
  $count = $stmt->rowCount();
  return intval($count);
}

/*
GET * in specific table row PARAM[table name, column to compare, value that compare with] 
*/ 
function getAllQuery($table,$col,$value){
  global $con;
  $query = "SELECT * FROM `{$table}` WHERE `{$col}` = :val";
  $stmt = $con->prepare($query);
  $stmt->bindParam(':val',$value);
  $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);

}

// get total views from movies
function totalViews($condition = ''){
  global $con;
  $query = "SELECT `views` FROM `movies` WHERE `status_id` = 2 $condition";
  $stmt = $con->query($query);
  $stmt->execute();
  $view_count = 0;
  if($stmt){
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
      $view_count += intval($rows['views']);
    }
  }
  return $view_count; 
}
/*
count query stmt
example:
   $query = "SELECT COUNT(id) FROM `movies` WHERE `status_id` = 2";
*/
function countQuery($queryStmt){
  global $con;
  $query = $queryStmt;
  $stmt = $con->query($query);
  $stmt->execute();
  $count = intval($stmt->fetchColumn());
  return $count; 
}

/*
get all categories [form]
Default Param empty, can add value id for edit to be selected
*/ 
function categoriesSelectionForm($cat_value = ''){
  global $con;
  $query = "SELECT * FROM `categories` ORDER BY `category_name` ASC";
  $stmt = $con->query($query);
  $options = "<option value=''>category</option>";
  while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
    $id = $rows['id'];
    $category_name = $rows['category_name'];
    $selected = intval($cat_value) === intval($id) ? 'selected': '';
    $options .= "<option value='$id' $selected>$category_name</option>";
  }
  return $options;
}
/*
remove extra spaces and enters
*/ 

function removeExtraSpace($value){
  $name = trim(preg_replace('/\s\s+/', ' ', $value));
  return $name;
}
/*
Check if visitor is a member
  - parameters $type(error|success) | message | direction path
  - no have access to login | register | dashboard
*/
function usersAccessOnly($type,$message,$redirect){
  if(isset($_SESSION['id'])){return true;}
  else{
    setMessage($type,$message);
    header("location:$redirect");
    exit;
  }
}

/*
Check if visitor not a member
  - $type(success/error) | $message [set in function => setMessage()] | $redirect =>  path 
  - it will have access to login | Register
  - it will redirect to index.php
*/
function visitorAccessOnly($type,$message,$redirect){
  if(!isset($_SESSION['id'])){return true;}
  else{
    setMessage($type,$message);
    exit(header("Location:$redirect"));
  }
}
/*
Check If user has admin permission or redirect to certian place
PARAM(type[error/success],message,location page)
setMessage ==> is a function created locally 
redirectHeader ==> is a function created locally
Access to only permission that has id equal to 1
 
*/
function permissionIdAdminOnlycanAccess($type,$message,$location){
  if(isset($_SESSION['permission_id']) && intval($_SESSION['permission_id']) !== 1 ){
    setMessage($type,$message);
    redirectHeader($location);
  }
}

// Set Message Style
function setMessage($type,$message){
  $_SESSION['message'] = <<<DELIMETER
          <div class="global-notifications $type">
            <p>$message</p>
          </div>
  DELIMETER;
}

// Display Message Style
function displayMessage(){
  echo $_SESSION['message'];
  unset($_SESSION['message']);  
}
// 
// 
// redirect page
function redirectHeader($location){
header("location:$location");
exit;
}

/*
get year options selection
Param default is empty, can add year for form edit to select the specific year
*/ 
function yearOptions($movie_value = ''){
  $date = date("Y"); // 2021
    $i    = 1895; // start for the first movie created
    $options = '';
    while($i <= $date){
      $selected = intval($movie_value) === $date ? 'selected' : '';
      $options .= "<option value='{$date}' {$selected} >{$date}</option>";
      $date--;
    }
    return $options;
}

// validate year options in form
function validateYear(){
  $year = date('Y');
  $i = 1895;// start for the first movie created
  $arr = [];
  while($i <= $year){
    array_push($arr,$year);
    $year--;
  }
  return $arr;
}