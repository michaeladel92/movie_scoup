<?php
require_once("connect.php");
require_once("functions.php");
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $notifications = [];

    // make sure the admin permission is updated
    $session_user_id = intval($_SESSION['id']);
    $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
    $is_still_admin = intval(countQuery($query)); //function return count sql
    if($is_still_admin !== 1){
      $notifications = ['error' => "Oops, Something went wrong☹️"];
      exit(json_encode($notifications));
    }

    // add category
    if($_POST['trigger'] === 'add_category'){
        $name =  removeExtraSpace($_POST['category']);  
        $name =  strtolower($name);  
        $name =  strip_tags($name);  
        $name =  stripslashes($name);  
        $name =  htmlspecialchars($name);  
      
        // validate
        if(empty($name) || $name === ''){
          $notifications = ['error' =>'Category Can not be empty!🙁'];
          exit(json_encode($notifications));
        }elseif(strlen($name) > 20){
          $notifications = ['error' =>'maximum length for category is 20 char!🙁'];
          exit(json_encode($notifications));
        }elseif(strlen($name) < 3){
          $notifications = ['error' =>'minimum length for category is 3 char !🙁'];
          exit(json_encode($notifications));
        }else{
          // check if exist in database[not allowed duplicate] 
          $count = countRows('category_name','categories',$name);
          if($count > 0){
            $notifications = ['error' => 'Oops, the category you entered already exist😅'];
            exit(json_encode($notifications));            
          }else{
            
            $query = "INSERT INTO categories(`category_name`)VALUES(?)";
            $stmt  = $con->prepare($query);
            $stmt->bindParam(1,$name);
            $stmt->execute();
            if($stmt){
              $id = $con->lastInsertId();
              $notifications = ['success' => "hurray! category added successfully 🥳",'id' => $id,'name' => $name];
              exit(json_encode($notifications));
            }else{
              $notifications = ['error' => "Oops, Something went wrong☹️"];
              exit(json_encode($notifications));
            }
          }
        }  
    }
    // edit | delete category [check if category exist in our db] 
    elseif($_POST['trigger'] === "cat_processing"){
      $id             = intval($_POST['id']);
      $triggerBtnName = strtolower($_POST['name']);

       // get rowCount PARAM[column | table | value]
      $count = countRows('id','categories',$id);
      
      if($count > 0){
       // get GET ALL PARAM[table | column | value]
         $row = getAllQuery('categories','id',$id);
        
         $notifications = ['success' => json_encode($row),'btnTrigger' => $triggerBtnName];
         exit(json_encode($notifications));
      }else{
        $notifications = ['error' => 'Oops, something went wrong!🙁'];
        exit(json_encode($notifications));
      }
    
    }
    // edit category
    elseif($_POST['trigger'] === 'edit_category'){

      $id   = intval($_POST['id']);
      $name = removeExtraSpace($_POST['category']);
      $name = strtolower($name);  
      $name = strip_tags($name);  
      $name = stripslashes($name);  
      $name = htmlspecialchars($name);


      // for reviewing purpose [categories id]
    $forReviews = [20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44];
    if(in_array($id,$forReviews)){
      $notifications = ['error' =>'This category is for review purpose and its not allowed to be edited!🙁'];
      exit(json_encode($notifications));
    }


      // validate
      if(empty($name) || $name === ''){
        $notifications = ['error' =>'Category Can not be empty!🙁'];
        exit(json_encode($notifications));
      }elseif(strlen($name) > 20){
        $notifications = ['error' =>'maximum length for category is 20 char!🙁'];
        exit(json_encode($notifications));
      }elseif(strlen($name) < 3){
        $notifications = ['error' =>'minimum length for category is 3 char !🙁'];
        exit(json_encode($notifications));
      }else{
          // get rowCount PARAM[column | table | value]
          $count = countRows('id','categories',$id);
          if($count > 0){
            // not allowed duplicate
            $query = "SELECT `category_name`
                        FROM
                          `categories` 
                        WHERE 
                        (`category_name` = :cat AND `id` != :id) LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':cat',$name);              
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            $count = $stmt->rowCount(); 
              if($count === 0 ){
                // update query
                  $query = "UPDATE `categories` SET `category_name` = :cat WHERE `id` = :id";
                  $stmt = $con->prepare($query);
                  $stmt->bindParam(':cat',$name);
                  $stmt->bindParam(':id',$id);
                  $stmt->execute();
                  if($stmt){
                    $notifications = ['success' => "category \"$name\" updated successfully! 😄"];
                    exit(json_encode($notifications));
                  }else{
                    $notifications = ['error' => 'Oops, something went wrong!🙁'];
                    exit(json_encode($notifications));
                  }
              }else{
                $notifications = ['error' => "category $name is already exist in our database!😅"];
                exit(json_encode($notifications));
              }           
          }else{
            $notifications = ['error' => 'Oops, something went wrong!🙁'];
            exit(json_encode($notifications));
          }
      }
    }
    //delete category
    elseif($_POST['trigger'] === 'delete_category'){
      $id = intval($_POST['id']);
      
      // for reviewing purpose [categories id]
      $forReviews = [20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44];
      if(in_array($id,$forReviews)){
        $notifications = ['error' =>'This category is for review purpose and its not allowed to be deleted!🙁'];
        exit(json_encode($notifications));
      }


      // get rowCount PARAM[column | table | value]
      $count = countRows('id','categories',$id);

      if($count === 1){
        // delete query
        $query = "DELETE FROM `categories` WHERE `id` = :id LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        if($stmt){
          $notifications = ['success' => "category successfully deleted!😄"];
          exit(json_encode($notifications));
        }else{
          $notifications = ['error' => 'Oops, something went wrong!🙁'];
          exit(json_encode($notifications));
        }

      }else{
        $notifications = ['error' => 'Oops, something went wrong!🙁'];
        exit(json_encode($notifications));
      }

    }
    //search category
    elseif($_POST['trigger'] === 'search_category'){
      $name = removeExtraSpace($_POST['name']);  
      $name = strtolower($name);  
      $name = strip_tags($name);  
      $name = stripslashes($name);  
      $name = htmlspecialchars($name);  
      $search = '%'.$name.'%';
      $query = "SELECT * FROM `categories` 
                                    WHERE `category_name`
                                    LIKE :keywords  ORDER BY `category_name` DESC";
      $stmt = $con->prepare($query);
      $stmt->bindParam(':keywords',$search);
      $stmt->execute();
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
          $notifications = ['error' => 'no query found!🙁'];
          exit(json_encode($notifications));
        }
   
      }else{
        $notifications = ['error' => 'Oops, something went wrong!🙁'];
        exit(json_encode($notifications));
      }

    }
  }
?>
      <h1 class="title">Categories</h1>
      <!-- search query category -->
      <div class="search_query">
          <input id="search_category" type="text" placeholder="search category">
          <i class="fas fa-search"></i>
          <!-- trigger the popup for add category -->
          <button id="add_category" class="btn add_btn"><i class="fas fa-plus"></i> Add category</button>
      </div>
      <table id="category_table" class='table'>
          <thead>
              <tr>
                <th>Category</th>
                <th colspan="2">options</th>
              </tr>
          </thead>
          <tbody></tbody>
      </table>
      <!-- notice -->
      <div class="dashboard_Notice"><h3></h3></div>