<?php
require_once("connect.php");
require_once("functions.php");
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $notifications = [];
  
    //update status [approved | pending] movie
    if($_POST['trigger'] === 'update_status'){
      $status   = removeExtraSpace($_POST['status']);
      $movie_id = intval($_POST['id']);

      // status value must be approved or pending
      if($status !== 'approved' && $status !== 'pending'){
        $notifications = ['error' => 'Oops, something went wrong!🙁'];
        exit(json_encode($notifications));
      }else{
        /*
        i used in js current status as a class if(btn clicked approved i get data approved)
        approved  ==switch ==> pending
        pending  ==switch ==> approved
        @else ====> it will be pending
        */ 
        $update_to = ($status === 'approved' ? 'pending' :($status === 'pending' ? 'approved' : 'pending'));
        /*
        get the id depend of status
        id [1] ==> pending
        id [2] ==> approved
        id [3] ==> blocked(will not use as its a training purpose and dont want to spend more times)
        */
        $status_id = ( $update_to === 'approved' ? 2 : ($update_to === 'pending' ? 1 : 1));
        
         // make sure the admin permission is updated
         $session_user_id = intval($_SESSION['id']);
        $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
        $is_still_admin = intval(countQuery($query)); //function return count sql

        //only admin can access   
        if(isset($_SESSION['id']) && intval($_SESSION['permission_id']) === 1 && $is_still_admin === 1){

          $query = "UPDATE `movies` SET 
                                      `status_id` = :s ,
                                      `action_users_id` = :u
                                    WHERE
                                       `id` = :m       
                                      ";
          $session_id = intval($_SESSION['id']);
          $stmt = $con->prepare($query);
          $stmt->bindParam(':s',$status_id);                            
          $stmt->bindParam(':u',$session_id);                            
          $stmt->bindParam(':m',$movie_id);
          $stmt->execute();
          $count = $stmt->rowCount();
          
          if($stmt){
            if($count > 0){
              $notifications = ['success' => "$count row updated successfully!😄"];
              exit(json_encode($notifications));
            }else{
                $notifications = ['error' => 'Oops, something went wrong!🙁'];
                exit(json_encode($notifications));
            }
          }else{
            $notifications = ['error' => 'Oops, something went wrong!🙁'];
            exit(json_encode($notifications));
          }
        }else{
          $notifications = ['error' => 'Oops, something went wrong!🙁'];
          exit(json_encode($notifications));
        }
      }      
    }
    //Delete movie [check if movie exist + user has permission to delete or not]
    elseif($_POST['trigger'] === 'movie_check_process'){
      $movie_id = intval($_POST['movie_id']);

      // check if the movie belong to the user
      $session_user_id = intval($_SESSION['id']);
      $session_permission_id = intval($_SESSION['permission_id']);

       // make sure the admin permission is updated
       $session_user_id = intval($_SESSION['id']);
       $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
       $is_still_admin = intval(countQuery($query)); //function return count sql


      $sql_condition = $session_permission_id === 1 && $is_still_admin == 1 ? "" : " AND `movies`.`user_id` =   $session_user_id";
      $query = "SELECT id FROM `movies` WHERE `id` = {$movie_id} $sql_condition";
      $stmt  = $con->query($query);
      $count = $stmt->rowCount(); 

      if($stmt && $count > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $notifications = ['success' => json_encode(['id' => $row['id']])];
        exit(json_encode($notifications));

      }else{
         $notifications = ['error' => 'Oops, something went wrong!🙁'];
         exit(json_encode($notifications));
      }
    }
    //delete movie process
    elseif($_POST['trigger'] === 'delete_movie'){
      $movie_id = intval($_POST['id']);

      $forReviews = [29,30,31,32,33,34,35,36,37,38,39,40];
      if(in_array($movie_id,$forReviews)){
        $notifications = ['error' => 'This Post is for review purpose and its not allowed to be deleted!🙁'];
        exit(json_encode($notifications));
      }

      // check if the movie belong to the user
      $session_user_id = intval($_SESSION['id']);
      $session_permission_id = intval($_SESSION['permission_id']);
      
      // make sure the admin permission is updated
      $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
      $is_still_admin = intval(countQuery($query)); //function return count sql
      

      $sql_condition = $session_permission_id === 1 && $is_still_admin == 1 ? "" : " AND `movies`.`user_id` =   $session_user_id";
      $query = "SELECT count(`id`) FROM `movies` WHERE `id` = {$movie_id} $sql_condition";
      // Accept Sql Query that has count()
      $count = countQuery($query);

      if($count > 0){
        // get email and poster to unlink
        $query = "SELECT `poster` FROM `movies` WHERE `id` = $movie_id LIMIT 1";
        $stmt = $con->query($query);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        unlink('../'.$row['poster']);
        // delete query
        $query = "DELETE FROM `movies` WHERE `id` = ? LIMIT 1";
        $stmt  = $con->prepare($query);
        $stmt->bindParam(1,$movie_id);
        $stmt->execute();
        if($stmt){
           $notifications = ['success' => "$count row updated successfully!😄"];
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
  }

  if($_SERVER['REQUEST_METHOD'] === 'GET'):
    if(!isset($_GET['edit_movie'])):
?>
<!--======== 
  INSIGHT - HOME
=========-->
      <section class="insight-list">
          <!-- box -->
          <div id="view_count" class="insight-box">
            <div class="boxes">
              <h4>view</h4>
              <h2>-</h2>
            </div>
          </div>
          <!-- box -->
          <div id="publish_count" class="insight-box">
            <div class="boxes">
              <h4>published</h4>
              <h2>-</h2>
            </div>
          </div>
          <!-- box -->
          <div id="comment_count" class="insight-box">
            <div class="boxes">
              <h4>comment</h4>
              <h2>-</h2>
            </div>
          </div>
          <?php if(intval($_SESSION['permission_id']) === 1): ?>
          <!-- box -->
          <div id="user_count" class="insight-box">
            <div class="boxes">
              <h4>user</h4>
              <h2>-</h2>
            </div>
          </div>
                 <!-- box -->
          <div id="pending_article_count" class="insight-box">
            <div class="boxes">
              <h4>pending</h4>
              <h2>-</h2>
              <i class="fas fa-long-arrow-alt-up"></i>
            </div>
          </div>
                 <!-- box -->
          <div id="pending_comment_count" class="insight-box">
            <div class="boxes">
              <h4>pending</h4>
              <h2>-</h2>
              <i class="fas fa-long-arrow-alt-up"></i>
            </div>
          </div>
          <?php endif;?>
          
      </section> 

<!--======== 
  MOVIES LIST
=========-->
      <h2 class="title">movie lists</h2>
      <!-- search movie category -->
      <!-- <div class="search_query">
          <input id="search_movie" type="text" placeholder="search movie">
          <i class="fas fa-search"></i> -->
          <!-- trigger the popup for add category -->
          <!-- <button id="add_category" class="btn add_btn"><i class="fas fa-plus"></i> Add category</button>
      </div> -->
      <table id="movie_table" class='table'>
          <thead>
              <tr>
                <th>poster</th>
                <th>title</th>
                <th>type</th>
                <th>year</th>
                <th>category</th>
                <?php if(intval($_SESSION['permission_id']) === 1): ?>
                <th>user</th>
                <?php endif; ?>
                <th colspan="2">likes</th>
                <th>views </th>
                <th>status_id </th>
                <th colspan="2">options</th>
              </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <figure class="poster-img">
                   <img src="./img/male.png" alt="">
                </figure>
              </td>
              <td><a href="">avengers: the vs wars</a></td>
              <td>movie</td>
              <td>2015</td>
              <td>super hero</td>
              <td>michael adel</td>
              <td><i class="fas fa-thumbs-up"></i>12</td>
              <td><i class="fas fa-thumbs-down"></i>3351</td>
              <td>9000</td>
              <td>approved</td>
              <td><a href=""><i class="fas fa-edit"></i></a></td>
              <td><a href=""><i class="fas fa-trash-alt"></i></a></td>
            </tr>
          </tbody>
      </table>
      <!-- notice -->
      <div class="dashboard_Notice"><h3></h3></div>   
      
<?php
elseif(isset($_GET['edit_movie'])):
  $movie_id = intval($_GET['edit_movie']);
  if($movie_id === '' || empty($movie_id) || !is_numeric($movie_id)){
   //Param: location
    redirectHeader('dashboard.php');
  }else{
    // check if movie exist param[col | tablename | value] 
    $count = countRows('id','movies',$movie_id);
    if($count === 0){
      //Param: location
      redirectHeader('dashboard.php');
    }else{
      // check if the movie belong to the user
      $session_user_id = intval($_SESSION['id']);
      $session_permission_id = intval($_SESSION['permission_id']);

      // make sure the admin permission is updated
      $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
      $is_still_admin = intval(countQuery($query)); //function return count sql

      $sql_condition = $session_permission_id === 1 && $is_still_admin == 1 ? "" : " AND `user_id` = $session_user_id";

      $query = "SELECT * FROM `movies` WHERE `id` = $movie_id $sql_condition";
      $stmt = $con->query($query);
      $stmt->execute();
      $count = $stmt->rowCount();
      if($count === 0){
        //Param: location
        redirectHeader('dashboard.php');
      }else{
        // get data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
      }
    }
  }
?>
<!--======== 
  INSIGHT - EDIT MOVIE PAGE
=========-->
<h1 class="title">update: "<?= isset($row['movie_name']) ? $row['movie_name'] : ''; ?>"</h1>
<form class="edit_movie" id="edit_movie" enctype="multipart/form-data">
    <div class="form-container">
        <!--token  -->
        <input type="hidden" name="token" value="<?= isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>">
         <!--id  -->
        <input type="hidden" name="movie_id" value="<?= isset($row['id']) ? $row['id'] : ''; ?>">
      <!-- movie_name	 -->
      <div class="control">
        <label for="movie_name">movie/series name</label>
        <input id="movie_name" type="text" name="movie_name" placeholder="title of the movie / series" value="<?= isset($row['movie_name']) ? $row['movie_name'] : ''; ?>">
      </div>
      <!-- movie_or_series-->
      <div class="control">
        <label for="movie_series">type</label>
        <select id="movie_series" name="movie_series">
          <option value="">choose type</option>
          <option value="movie" <?= isset($row['movie_or_series']) && $row['movie_or_series'] === 'movie' ? 'selected' : ''; ?> >movie</option>
          <option value="series" <?= isset($row['movie_or_series']) && $row['movie_or_series'] === 'series' ? 'selected' : ''; ?>>series</option>
        </select>
      </div>
      <!-- year	 -->
      <div class="control">
              <label for="year">published year</label>
              <select id="year" name="year">
                <?= yearOptions(isset($row['year']) ? $row['year'] : '');?>
              </select>
        </div>
      <!-- category_id 	 -->
      <div class="control">
              <label for="category">category</label>
              <select id="category" name="category">
                <?php echo categoriesSelectionForm(isset($row['category_id']) ? $row['category_id'] : ''); ?>
              </select>
      </div>
      <!-- description-->
      <div class="control">
        <label for="movie_description">review</label>
        <textarea id="movie_description" name="movie_description" placeholder="Great thoughts come from the heart">
        <?= isset($row['description']) ? htmlspecialchars_decode($row['description']) : '';
        ?>
        </textarea>
      </div>  
      <!-- poster-->
      <div class="control">
          <label for="image">poster</label>
          <input id="image" type="file" name="image" placeholder="image">
      </div>
      <!-- submit -->
      <div class="control">
        <input type="submit" class="btn btn_edit" name="submit" value="update">
      </div>

    </div>
</form>
<?php
  endif;
endif;