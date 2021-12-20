<?php
require_once("connect.php");
require_once("functions.php");

if($_SERVER['REQUEST_METHOD'] === 'GET'){
/*============================
GET MOVE ID  
============================*/ 
  if(isset($_GET['id'])){

    if(!is_numeric($_GET['id'])){
      $movie_id = base64_decode($_GET['id']);
      $movie_id = intval($movie_id);
    }else{
      $movie_id = intval($_GET['id']);
    }

    
    $query = "SELECT 
                    `movies`.`id`,
                    `movies`.`movie_or_series`,
                    `movies`.`movie_name`,
                    `movies`.`description`,
                    `movies`.`poster`,
                    `movies`.`year`,
                    `movies`.`published_date`,
                    `movies`.`likes`,
                    `movies`.`views`,
                    `users`.`id` AS user_id_,
                    `users`.`user_name`,
                    `users`.`trust_lvl`,
                    `categories`.`id` AS cat_id,
                    `categories`.`category_name`
                  FROM `movies`
                  INNER JOIN `users`
                  ON
                    `movies`.`user_id` = `users`.`id`
                  INNER JOIN `categories`
                  ON
                    `movies`.`category_id` = `categories`.`id`  
                  WHERE
                    `movies`.`id` = :movie_id 
                  AND `movies`.`status_id` = 2  
                  LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':movie_id',$movie_id);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($stmt){
      if($count !== 1){
        setMessage('error','404 - not found!');
        redirectHeader('index.php');
      }else{

      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $id               = intval($row['id']);
        $movie_or_series  = $row['movie_or_series'];
        $movie_name       = $row['movie_name'];
        $description      = htmlspecialchars_decode($row['description']);
        $poster           = $row['poster'];
        $year             = $row['year']; 
        $published_date   = $row['published_date'];
        $likes            =  json_decode(str_replace("'", "", $row['likes']));
        $views            = intval($row['views']);
        $user_id_         = intval($row['user_id_']);
        $user_name        = $row['user_name'];
        $trust_lvl        = intval($row['trust_lvl']);
        $cat_id           = intval($row['cat_id']);
        $category_name    = $row['category_name'];
       }

       //likes
       $likesArr = [];
       foreach($likes as $key => $val){$likesArr[$key] = $val;}
       $arr = [];
       foreach($likesArr['memberLike'] as $val){$arr[] = $val;}
       $likesArr['memberLike'] = $arr;
       
      $status = 'no-vote';
     
      if(isset($_SESSION['id'])){
        $session_user_id = intval($_SESSION['id']);
        if(in_array($session_user_id,$likesArr['memberLike'])){
          $status = 'liked';
        }elseif(in_array($session_user_id,$likesArr['memberDislike'])){
          $status = 'disliked';
        }
      }
/*============================
Update view count QUERY  
============================*/ 
       $query = "UPDATE `movies` SET `views` = `views` + 1 WHERE `id` = $id";
       $stmt  = $con->query($query);

      }
    }else{
      setMessage('error','oops, something went wrong!');
      redirectHeader('index.php');
    }                
  
  }else{
    setMessage('error','404 - not found!');
    redirectHeader('index.php');
  }
}
?>
<main class="movie_details">
  <section class="movie_container">
      <!-- content -->
      <div class="movie_content">
        <h1 class="title"><?=$movie_name?></h1>
         <ul class="list">
           <li><span>author:</span> 
           <a target="_blank" href="./profile.php?profile=<?=base64_encode($user_id_);?>">
           <?=$user_name?></a>
           <?php if($trust_lvl === 1): ?>
           <i class="fas fa-check-circle trust_ok"></i> </li>
           <?php endif; ?>
           <li><span><?=$movie_or_series?>:</span> <?=$category_name?> </li>
           <li><span>year:</span> <?=$year?> </li>
           <li><span>view:</span> <?=$views + 1?> </li>
         </ul>
         <div class="publisher_content" ><?=$description?></div>
      </div>
      <!-- image -->
      <div class="poster">
        <figure>
          <img src="./<?=$poster?>" alt="<?=$movie_name?>">
        </figure>
        <small class="publish_date"><span>published:</span> <?= date("D, d M Y", strtotime($published_date))?></small>
        <figcaption class="actions">
             <p><a data-movie="<?=base64_encode($id)?>" class="<?=$status === 'liked' ? 'like':'default_like'?>" href=""><i class="fas fa-thumbs-up"></i></a>
            <span><?=intval($likesArr['like'])?></span>
            </p>
             <p><a data-movie="<?=base64_encode($id)?>" class="<?=$status === 'disliked' ? 'dislike':'default_dislike'?>" href=""><i class="fas fa-thumbs-down"></i></a>
             <span><?=intval($likesArr['dislike'])?></span></p>
        </figcaption>
      </div>
  </section>
</main>