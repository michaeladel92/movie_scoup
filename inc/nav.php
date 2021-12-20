<nav class="navbar">
        <figure class="logo-container">
          <img src="img/logo.png" alt="movie scoup logo" loading="lazy">
          <figcaption >
            <h6 class="logo-text">Movie Scoup</h6>
          </figcaption>
        </figure>
        
        <ul class="menu">
        
          <li><a class="<?php echo strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) === strtolower('/Movie_scoup/Index.Php') ? 'active': '';?>" href="./index.php">home</a></li>
          <!-- <li><a href="#">test</a></li>
          <li><a href="#">test</a></li> -->
          <li id="sort-list" class="sort-list">
            <a href="#"><?= (isset($_SESSION['name']) ? 
                            (strlen($_SESSION['name']) > 10 ? substr($_SESSION['name'],0,10).'.' : $_SESSION['name']): 'more'); ?> <i class="fas fa-sort-down"></i></a>
            <ul class="inner-menu">
              <?php if(isset($_SESSION['id'])) : ?>
                <li><a href="./profile.php?profile=<?=base64_encode($_SESSION['id']);?>">profile</a></li>
                <li><a href="./dashboard.php">dashboard</a></li>
                <li><a href="./logout.php">logout</a></li>
              <?php else: ?>
                <li><a href="./login_reg.php">login</a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
          
         <?php
        //  global notifications display it then unset the session
         if(isset($_SESSION['message'])){displayMessage();}
         ?>
    </nav>