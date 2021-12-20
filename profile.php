<?php
$title = '| profile';
include_once("inc/init.php");
include_once("inc/nav.php");

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  // session profile set
  if(isset($_GET['profile'])){

    $id =  $_GET['profile'];
    // if empty will get the session | redirect to index
    if(empty($id) || $id === ''){
        //GET from session id 
        if(isset($_SESSION['id'])){$id = base64_encode($_SESSION['id']);}
        else{
          setMessage('error','404 - not found! 🙁');
          redirectHeader('index.php');
        }
    }else{
      if(is_numeric($_GET['profile'])){
      $id =  base64_encode($_GET['profile']);
      }
    }
  }else{
    
    //GET from session id 
    if(isset($_SESSION['id'])){$id = base64_encode($_SESSION['id']);}
    else{
      setMessage('error','404 - not found! 🙁');
      redirectHeader('index.php');
    }
  }
  // getting the result final id
  $user_id = base64_decode($id);
  $user_id = intval($user_id);
  // query get user info
  $query = "SELECT `id`,
                   `user_name`,
                   `email`,
                   `gender`,
                   `image`,
                   `about`,
                   `permission_id`,
                   `reg_date`,
                   `status_id`,
                   `trust_lvl`
              FROM 
                  `users` WHERE `id` = :id LIMIT 1";
  $stmt = $con->prepare($query);
  $stmt->bindParam(':id',$user_id);
  $stmt->execute();
  $count = $stmt->rowCount();

  if($stmt){
      if($count !== 1){
        setMessage('error','404 - not found! 🙁');
        redirectHeader('index.php');
      }else{
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $id = intval($row['id']);
          $user_name =  $row['user_name'];
          $email  = $row['email'];
          $image  = $row['image'];
          $gender = $row['gender'];
          $about  = $row['about'];
          //[1]admin[2]writer
          $permission_id =  intval($row['permission_id']);
          $reg_date  = $row['reg_date'];
          //[1]pending[2]approved[3]blocked
          $status_id = intval($row['status_id']);
          $trust_lvl = intval($row['trust_lvl']);
        }
        $permission  = ($permission_id === 1 ? 'administrator' : 'user');
        $status      = ($status_id === 1 ? 'pending' : ($status_id === 2 ? 'active' : 'blocked'));
        $trust_badge = ($trust_lvl === 1 ? "<i class='fas fa-check-circle trust_ok'></i>": "");
        
        //GET Movies if found
        $sql_condition = ( isset($_SESSION['id']) && $id === intval($_SESSION['id']) ? "" : "AND `status_id` = 2");
        $query = "SELECT `status_id`,
                         `movie_name`,
                         `poster`,
                         `id`,
                         `views`
                     FROM `movies` WHERE `user_id` = $id $sql_condition";
        $stmt_movie = $con->query($query);
        $count_movies =  $stmt_movie->rowCount();

      }
  }else{
    setMessage('error','404 - not found! 🙁');
    redirectHeader('index.php');
  }
}
?>
<!-- navbar -->
  <main class="profile">
    <!-- loading -->
    <div class="loading show"><span></span></div>
    <?php if(isset($_SESSION['id']) && intval($_SESSION['id']) === $id && !isset($_GET['edit'])):?>
    <a class="edit_btn" href="profile.php?edit=<?=base64_encode($_SESSION['id'])?>"><i class="fas fa-edit"></i></a>
    <?php endif; ?>
    <h2 class="title">Personal Information</h2>
    <?php
    // edit
    if($_SERVER['REQUEST_METHOD'] === 'GET'):
      if(
         isset($_GET['edit']) &&
         $_GET['edit'] !== '' &&
         !empty($_GET['edit']) &&
         isset($_SESSION['id']) &&
         intval($_SESSION['id']) === $id
         ):
    ?>
    <form id="edit-user" class="edit_user" enctype="multipart/form-data">
            <!--token  -->
            <input type="hidden" name="token" value="<?= isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>">
            <!-- name -->
            <div class="control">
              <label for="user_name">name</label>
              <input type="text" name="user_name" placeholder="name" value="<?=$user_name?>">
            </div>
            <!-- email -->
            <div class="control">
              <label for="email">email</label>
              <input type="text" class="edit_email" value="<?=$email?>"  placeholder="email" disabled>
            </div>
            <!-- password -->
            <div class="control">
              <label for="password">password</label>
              <input type="password" name="password" placeholder="new password">
            </div>
            <!-- confirm password -->
            <div class="control">
              <label for="con_password">confirm password</label>
              <input type="password" name="con_password" placeholder="confirm password">
            </div>
            <!-- gender -->
            <div class="control">
              <label for="gender">gender</label>
              <select name="gender">
                <option value="">choose gender</option>
                <option value="male" <?= $gender === 'male' ? 'selected':'' ?> >male</option>
                <option value="female" <?= $gender === 'female' ? 'selected':'' ?> >female</option>
              </select>
            </div>
            <!-- Profile -->
            <div class="control">
              <label for="image">Profile Picture</label>
              <input type="file" name="image">
            </div>
            <!-- about -->
            <div class="control">
              <label for="about">about me</label>
              <textarea  name="about" placeholder="brief about yourself"><?= (empty($about) || $about === '' ? '':trim($about)) ?></textarea>
            </div>
            <input type="submit" name="edit_user" class="btn btn_submit" value="update">
    </form>
    <!--notifications-->
    <small class="notifications_global_api"></small>
    <?php else: ?>
    <section class="personal-info">
      <figure class="profile-image">
          <img src="<?=$image?>" alt="<?=$user_name?>">
      </figure>
      <div class="profile-content">
        <h3 class="user_name"><?=$user_name?> <span><?=$trust_badge?></span></h4>
        <ul class="profile_list">
          <li><h6>email:</h6> <p><?=$email?></p> </li>
          <li><h6>gender:</h6> <p>
            <?php if($gender === 'male'): ?>
            <i class="fas fa-mars"></i>
            <?php elseif($gender === 'female'): ?>
            <i class="fas fa-venus"></i>
            <?php endif;?>
          </p></li>
          <li><h6>Account status:</h6> <p><?=$status?></p></li>
          <li><h6>permission:</h6> <p><?=$permission?></p></li>
          <li><h6>registered date:</h6> <p><?=$reg_date?></p></li>
          <li><h6>about:</h6> 
              <div>
               <?php 
                    if($about === '' || empty($about)){echo '-';}
                    else{echo $about;}
               ?> 
              </div>
          </li>
        </ul>
      </div>
    </section>

    <h2 class="title">posts</h2>
    <section class="published-posts">
      <?php if($count_movies > 0): ?>
      <!-- box -->
      <?php while($rows = $stmt_movie->fetch(PDO::FETCH_ASSOC)): ?>
       <div class="boxes">
         <figure class="poster-img">
           <img src="<?=$rows['poster']?>" alt="<?=$rows['movie_name']?>">
         </figure>
         <a target="_blank" href="./index.php?target=movies&id=<?= base64_encode($rows['id']);?>">
           <figcaption class="poster-content">
            <?php if($rows['status_id'] == 2): ?> 
            <h4>View: <?=$rows['views'];?></h4>
            <?php else: ?> 
            <h4>status: <?php echo $status_movie = ($rows['status_id'] == 1 ?'pending' : 'pending' );?></h4>
             <?php endif;?>
             <h4><?=$rows['movie_name']?></h4>
            </figcaption>
          </a>
       </div> 
       <?php endwhile;?>
      <?php else:?>
        <h3 class="notification">there are no published posts yet. 
          <?php if(isset($_SESSION['id']) && intval($_SESSION['id']) === $id ): ?>  
          <a href="./dashboard.php?trigger=add" target="_blank">new post</a>
          <?php endif; ?>  
        </h3> 
      <?php endif; ?>    
    </section>
    <?php
     endif;
    endif;
    ?>
  </main>
<?php 
include_once("inc/footers.php");