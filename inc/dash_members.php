<?php
require_once("connect.php");
require_once("functions.php");
    // GET ALL members 
    $query = "SELECT `id`,
                    `email`,
                    `image`,
                    `permission_id`,
                    `status_id`,
                    `trust_lvl`
                  FROM 
                      `users`";
    $stmt  = $con->query($query);
    $count = $stmt->rowCount();
    //count approved members
    $query = "SELECT COUNT(`id`) FROM `users` WHERE `status_id` = 2";
    $count_users_approved = countQuery($query); //Pram accept Sql Query Count()
    //count pending members 
    $query = "SELECT COUNT(`id`) FROM `users` WHERE `status_id` != 2";
    $count_users_pending = countQuery($query); //Pram accept Sql Query Count()


    if($_SERVER['REQUEST_METHOD'] === "POST"){

      $notifications = [];
      $session_user_id = intval($_SESSION['id']);
      $session_permission_id = intval($_SESSION['permission_id']);
      // make sure the admin permission is updated
      $query = "SELECT COUNT(id) FROM `users` WHERE `id` = $session_user_id AND `permission_id` = 1 LIMIT 1"; 
      $is_still_admin = intval(countQuery($query));
      // admin access only
      if($session_permission_id === 1 && $is_still_admin === 1){
          // permission update
          if($_POST['trigger'] === 'permission'){

            $user_id = intval($_POST['id']);
            //get current permission
            $query = "SELECT `permission_id`,`email` FROM `users` WHERE `id` = :id LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':id',$user_id); 
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            $count_user_sql = $stmt->rowCount();
            if($count_user_sql > 0){
              $result_permission = intval($row['permission_id']);
              $result_email = strtolower($row['email']);
              // admin are not allowed to undergrade its self
              if(intval($user_id) === $session_user_id){
                $notifications = ['error' => 'You are not allowed to undergrade yourself!🙁'];
                exit(json_encode($notifications));
              }else{
                // admin not allowed to undergrade the super admin
                if($result_email === 'michaeladel1992@gmail.com' || $result_email === 'admin@admin.com'){
                  setMessage('error','Access denied! 🙁');
                  $notifications = ['error' => 'redirect'];
                  exit(json_encode($notifications));
                }else{
                  // toggle permission depend on current [1]admin[2]writer
                  $new_permission = ($result_permission === 1 ? 2 : 
                                    ($result_permission === 2 ? 1 : 2));
                  $query = "UPDATE `users` SET `permission_id` = :per_id WHERE `id` = :user_id_";
                  $stmt = $con->prepare($query);
                  $stmt->bindParam(':per_id',$new_permission);
                  $stmt->bindParam(':user_id_',$user_id);
                  $stmt->execute();
                  if($stmt){
                    $notifications = ['success' => "permission updated successfully!😄"];
                    exit(json_encode($notifications));
                  }else{
                    $notifications = ['error' => 'Oops, something went wrong!🙁'];
                    exit(json_encode($notifications));
                  }
                }
              }
              
            }else{
              $notifications = ['error' => 'Oops, something went wrong!🙁'];
              exit(json_encode($notifications));
            } 
          }
          // status update
          elseif($_POST['trigger'] === 'status'){
              $user_id = intval($_POST['id']);
              //get current status
              $query = "SELECT `status_id`,`email` FROM `users` WHERE `id` = :id LIMIT 1";
              $stmt = $con->prepare($query);
              $stmt->bindParam(':id',$user_id); 
              $stmt->execute();
              $row   = $stmt->fetch(PDO::FETCH_ASSOC);
              $count_user_sql = $stmt->rowCount();
              if($count_user_sql > 0){
                $result_status = intval($row['status_id']);
                $result_email = strtolower($row['email']);
                // admin are not allowed to undergrade its self
                if(intval($user_id) === $session_user_id){
                  $notifications = ['error' => 'You are not allowed to undergrade yourself!🙁'];
                  exit(json_encode($notifications));
                }else{
                  // admin not allowed to undergrade the super admin
                  if($result_email === 'michaeladel1992@gmail.com'){
                    setMessage('error','Access denied! 🙁');
                    $notifications = ['error' => 'redirect'];
                    exit(json_encode($notifications));
                  }else{
                    // toggle status depend on current [1]pending[2]approved[3]blocked
                    $new_status = ($result_status === 1 ? 2 : 
                                      ($result_status === 2 ? 3 : 2));
                    $status_txt = ($new_status === 1 ? 'pending' :
                                  ($new_status === 2 ? 'approved' : 'blocked'));

                    $query = "UPDATE `users` SET `status_id` = :status_id WHERE `id` = :user_id_";
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':status_id',$new_status);
                    $stmt->bindParam(':user_id_',$user_id);
                    $stmt->execute();
                    if($stmt){
                      $notifications = ['success' => "status updated successfully!😄", 'status_txt' => $status_txt];
                      exit(json_encode($notifications));
                    }else{
                      $notifications = ['error' => 'Oops, something went wrong!🙁'];
                      exit(json_encode($notifications));
                    }  
                  }
                }
              }
              else{
                $notifications = ['error' => 'Oops, something went wrong!🙁'];
                exit(json_encode($notifications));
              } 
          }
          // trust lvl
          elseif($_POST['trigger'] === 'trust_lvl'){
            $user_id = intval($_POST['id']);
            //get current status
            $query = "SELECT `trust_lvl`,`email` FROM `users` WHERE `id` = :id LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':id',$user_id); 
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            $count_user_sql = $stmt->rowCount();
            if($count_user_sql > 0){
              $result_trust = intval($row['trust_lvl']);
              $result_email = strtolower($row['email']);
              // admin are not allowed to undergrade its self
              if(intval($user_id) === $session_user_id){
                $notifications = ['error' => 'You are not allowed to undergrade yourself!🙁'];
                exit(json_encode($notifications));
              }else{
                // admin not allowed to undergrade the super admin
                if($result_email === 'michaeladel1992@gmail.com'){
                  setMessage('error','Access denied! 🙁');
                  $notifications = ['error' => 'redirect'];
                  exit(json_encode($notifications));
                }else{
                  // toggle status depend on current [1]pending[2]approved[3]blocked
                  $new_trust = ($result_trust === 0 ? 1 : 0);

                  $query = "UPDATE `users` SET `trust_lvl` = :trust_lvl WHERE `id` = :user_id_";
                  $stmt = $con->prepare($query);
                  $stmt->bindParam(':trust_lvl',$new_trust);
                  $stmt->bindParam(':user_id_',$user_id);
                  $stmt->execute();
                  if($stmt){
                    $notifications = ['success' => "trust lvl updated successfully!😄"];
                    exit(json_encode($notifications));
                  }else{
                    $notifications = ['error' => 'Oops, something went wrong!🙁'];
                    exit(json_encode($notifications));
                  }  
                }
              }
            }else{
              $notifications = ['error' => 'Oops, something went wrong!🙁'];
              exit(json_encode($notifications));
            } 
          }
          // delete check id
          elseif($_POST['trigger'] === 'delete_check'){
            $user_id = intval($_POST['id']);
            //get current user
            $query = "SELECT `id`,`email` FROM `users` WHERE `id` = :id LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':id',$user_id); 
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            $count_user_sql = $stmt->rowCount();
            if($count_user_sql > 0){
              $result_user = intval($row['id']);
              $result_email = strtolower($row['email']);
              // admin are not allowed to delete its self
              if(intval($user_id) === $session_user_id){
                $notifications = ['error' => 'You are not allowed to delete your account!🙁'];
                exit(json_encode($notifications));
              }else{
                // admin not allowed to delete the super admin
                if($result_email === 'michaeladel1992@gmail.com'){
                  setMessage('error','Access denied! 🙁');
                  $notifications = ['error' => 'redirect'];
                  exit(json_encode($notifications));
                }else{
                  //id is valid 
                  $notifications = ['success' => ['id' => $result_user,'email' => $result_email]];
                  exit(json_encode($notifications));
                }
              }
            }else{
              $notifications = ['error' => 'Oops, something went wrong!🙁'];
              exit(json_encode($notifications));
            } 
          }
          // delete user
          elseif($_POST['trigger'] === 'delete_user'){
            $user_id = intval($_POST['id']);
            //get current user
            $query = "SELECT `id`,`email` FROM `users` WHERE `id` = :id LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':id',$user_id); 
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            $count_user_sql = $stmt->rowCount();
            if($count_user_sql > 0){
              $result_user = intval($row['id']);
              $result_email = strtolower($row['email']);
              // admin are not allowed to delete its self
              if(intval($user_id) === $session_user_id){
                $notifications = ['error' => 'You are not allowed to delete your account!🙁'];
                exit(json_encode($notifications));
              }else{
                // admin not allowed to delete the super admin
                if($result_email === 'michaeladel1992@gmail.com' || 
                $result_email === 'admin@admin.com'){
                  setMessage('error','Access denied! 🙁');
                  $notifications = ['error' => 'redirect'];
                  exit(json_encode($notifications));
                }else{
                  //id is valid 
                  $query = "DELETE FROM `users` WHERE `id` = :id";
                  $stmt = $con->prepare($query);
                  $stmt->bindParam(':id',$user_id);
                  $stmt->execute();

                  if($stmt){
                    $notifications = ['success' => "$count_user_sql row deleted successfully!😄"];
                    exit(json_encode($notifications));
                  }else{
                    $notifications = ['error' => 'Oops, something went wrong!🙁'];
                    exit(json_encode($notifications));
                  }


                }
              }
            }else{
              $notifications = ['error' => 'Oops, something went wrong!🙁'];
              exit(json_encode($notifications));
            } 
          }
            

          }else{
                  setMessage('error','Access denied! 🙁');
                  $notifications = ['error' => 'redirect'];
                  exit(json_encode($notifications));
          }
       
    }

?>
<section class="insight-list">
          <!-- box -->
          <div id="total_users_count" class="insight-box">
            <div class="boxes">
              <h4>users</h4>
              <h2><?=$count;?></h2>
            </div>
          </div>
          <!-- box -->
          <div id="approved_users_count" class="insight-box">
            <div class="boxes">
              <h4>approved</h4>
              <h2><?= $count_users_approved;?></h2>
            </div>
          </div>
          <!-- box -->
          <div id="pending_users_count" class="insight-box">
            <div class="boxes">
              <h4>pending</h4>
              <h2><?= $count_users_pending;?></h2>
            </div>
          </div>
      </section> 

      <h2 class="title">Members</h2>
<!-- table -->
      <?php if($count > 0):?>
      <table id="member_table" class='table <?php echo $count > 0 ? 'show': '' ?>'>
          <thead>
              <tr>
                <th>image</th>
                <th>email</th>
                <th colspan="4">options</th>
              </tr>
          </thead>
          <tbody>
            <?php 
                  while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                  $permission_id  = intval($row['permission_id']);//[1]admin[2]writer
                  $status_id  = intval($row['status_id']); // [1]pending[2]approved[3]blocked 
                  $trust_id  = intval($row['trust_lvl']); //{0]normal[1]trusted
                    
                  $permission_txt = ($permission_id === 1 ? 'admin' : 'writer');
                  $permission_class = ($permission_id === 1 ? 'admin_btn' : 'writer_btn');    
                  $status_txt     = ($status_id === 1 ? 'pending' : 
                                ($status_id === 2 ? 'approved' : 'blocked'));
                  $status_class   = ($status_id === 1 ? 'pending_btn' : 
                  ($status_id === 2 ? 'approved_btn' : 'pending_btn'));             
                                
                  $trust_lvl  = ($trust_id === 0 ? 'trust_normal' : 'trust_ok');
            ?>
                <tr class="target_<?=$row['id'];?>">
                <td>
                  <figure class="poster-img">
                    <img src="./<?= $row['image']; ?>" alt="">
                  </figure>
                </td>
                <td><a class="profile_page_table" target="_blank" href="./profile.php?profile=<?=$row['id'];?>"><?= $row['email']; ?></a></td>
                <td><a data-id="<?= $row['id'];?>" class="permission_users <?= $permission_class; ?>" href="#"><?= $permission_txt; ?></a></td>
                <td><a data-id="<?= $row['id'];?>" class="status_users <?= $status_class; ?>" href="#"><?= $status_txt; ?></a></td>
                <td><a data-id="<?= $row['id'];?>" href="#"><i class="trust_lvl fas fa-check-circle <?= $trust_lvl; ?>"></i></td>
                <td><a data-id="<?= $row['id'];?>" href="#"><i class="delete_btn fas fa-trash-alt"></i></a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
      </table>
      <?php else: ?>
      <!-- notice -->
      <div class="dashboard_Notice <?php echo $count === 0 ? 'show': '' ?>"><h3>No Users found</h3></div>  
      <?php endif;