<?php
// GET Trigger and add the active class
function getActive($trigger){
  $active =  (!isset($_GET['trigger']) ? '': ($_GET['trigger'] === $trigger ? 'active': '') );
  return $active;  
}
// GET Trigger NOT SET add default class
function defaultActive(){
    if(!isset($_GET['trigger'])){$default = 'active';}
    else{
       $arr = ['insight','add','comment','favorit','category','member'];
       if(!in_array($_GET['trigger'],$arr)){$default = 'active';}
       else{$default = '';}


    }
    return $default;
}
?>
<div class="side-menu">
    
                <figure class="logo-container">
                    <img src="img/logo.png" alt="movie scoup logo" loading="lazy">
                    <figcaption >
                        <h6 class="logo-text">Movie Scoup</h6>
                    </figcaption>
                </figure>
                <div class="side-list">
                    <figure class="profile-pic">
                            <img src="<?=$_SESSION['image']?>" alt="profile picture" loading="lazy">
                    </figure>
                    <ul class="list">
                        <li><a class="<?php echo defaultActive();echo getActive('insight'); ?>" href="?trigger=insight">insight</a></li>
                        <li><a class="<?= getActive('add')?>" href="?trigger=add">add</a></li>
                        <!-- 

                            <li><a class=" //getActive('favorit')" href="?trigger=favorit">favorits</a></li>
                         -->
                        <?php 
                        // Admin Access only
                        if(
                            isset($_SESSION['permission_id']) && 
                            intval($_SESSION['permission_id']) === 1):
                        ?>
                        
                        <li><a class="<?= getActive('comment')?>" href="?trigger=comment">comments</a></li>

                        <li><a class="<?= getActive('category')?>" href="?trigger=category">category</a></li>
                        <li><a class="<?= getActive('member')?>" href="?trigger=member">member</a></li>
                        <?php endif; ?>    
                    </ul>
                </div>
            </div>