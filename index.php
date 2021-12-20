<?php
$title = '| Home';
include_once("inc/init.php");
include_once("inc/nav.php"); 

    $target = isset($_GET['target']) ? $_GET['target'] : '';

        switch($target){
          case 'movies':
            include_once('inc/details.php');
            break;
          default:
            include_once('inc/home.php');    
        }
    ?>
 <!--notifications-->
 <small class="notifications_global_api"></small>
<?php 
include_once("inc/footers.php");