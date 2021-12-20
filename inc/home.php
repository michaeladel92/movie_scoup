<?php
ob_start();
require_once("connect.php");
require_once("functions.php");

/*=============================
GET SEARCH QUERY 
=============================*/ 
$notifications = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  
  if($_POST['trigger'] === 'searchQuery'){
    $search = trim($_POST['search']);
    $search = strip_tags($search);
    $search = '%'.$search.'%';
    $query = "SELECT 
                    `movies`.`id`,
                    `movies`.`movie_name`,
                    `movies`.`poster`,
                    `movies`.`views`,
                    `users`.`id` AS user_id_,
                    `users`.`user_name`,
                    `users`.`trust_lvl`
                  FROM `movies`
                  INNER JOIN `users`
                  ON
                    `movies`.`user_id` = `users`.`id`
                  WHERE
                    (`movies`.`status_id` = 2)
                  AND
                    (`movies`.`movie_name` LIKE :search OR 
                    `users`.`user_name` LIKE :search)  
                  ORDER BY 
                    `movies`.`id` 
                  DESC ";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':search',$search);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($stmt){
      if($count > 0){
        $arr =[];
        while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
          $arr[] = $rows;
        }
        $notifications = ['success'=> $arr];
        exit(json_encode($notifications));
      }else{
        $notifications = ['error' => 'no query found!'];
        exit(json_encode($notifications));
      }

    }else{
      $notifications = ['error' => 'Oops, something went wrong!'];
      exit(json_encode($notifications));
    }

  }
}



/*=============================
GET ALL APPROVED MOVIES/SERIES 
=============================*/ 
  $query = "SELECT 
                `movies`.`id`,
                `movies`.`movie_name`,
                `movies`.`poster`,
                `movies`.`views`,
                `users`.`id` AS user_id_,
                `users`.`user_name`,
                `users`.`trust_lvl`
              FROM `movies`
              INNER JOIN `users`
              ON
              `movies`.`user_id` = `users`.`id`
            WHERE
              `movies`.`status_id` = 2  
  ORDER BY `movies`.`id` DESC";

  $stmt = $con->query($query);
  $stmt->execute();
  $count = $stmt->rowCount();
?>

<main class="home_page">
    <!-- filter -->
      <div class="filter">
        <input type="text" id="search_movies" placeholder="search by: Movie/Series Name | Author Name">
      </div>
      <!-- movie lists -->
      <section class="movie_lists">
        <?php 
            if($count > 0):
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
        ?>
          <div class="boxes">
              <figure>
                <a target="_blank" href="./index.php?target=movies&id=<?=base64_encode($row['id'])?>">
                  <img src="./<?=$row['poster']?>" alt="<?=$row['movie_name']?>">
                </a>
              </figure>
              <figcaption>
                <small>view: <?=$row['views']?></small>
                  <h3><a target="_blank" href="./index.php?target=movies&id=<?=base64_encode($row['id'])?>"><?=$row['movie_name']?></a></h3>
                  <h4>by: <a target="_blank" href="./profile.php?profile=<?=base64_encode($row['user_id_'])?>"><?=$row['user_name']?></a> 
                  <?php if(intval($row['trust_lvl']) === 1): ?>
                  <i class="fas fa-check-circle trust_ok"></i>
                  <?php endif; ?>  
                </h4>
              </figcaption>
          </div>
          <?php endwhile;
                else:
          ?>
          <h1>no publish posts available</h1>
          <?php endif; ?>
      </section>
</main>