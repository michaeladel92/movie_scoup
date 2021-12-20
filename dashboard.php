<?php
$title = '| dashboard';
include_once("inc/init.php");
/*
visitors not allowed to access here
PARAMETER[type(success | error) , message, redirect]
*/ 
usersAccessOnly('error','you need to login to access your dashboard! 🙁','login_reg.php');

?>
<!-- navbar -->
<?php include_once("inc/nav.php");?>

   <!--dashboard  -->
   <main class="dashboard">
       <section class="container">
       
            <!-- side -->
            <?php require_once('inc/dashboard_side.php');?>
            <!-- content -->
            <div class="table-of-content">
                <div class="dashboard-container">
                    <!-- loading -->
                    <div class="loading show"><span></span></div>
               <?php
               $target = isset($_GET['trigger']) ? $_GET['trigger'] : '';
               
               switch ($target) {
                case 'insight':
                    include_once('inc/dash_insight.php');
                    break;
                case 'add':
                    include_once('inc/dash_add.php');
                    break;
                case 'comment':
                            /*
                    Admin only has the access to this topic
                    PARAM[type(error/success) | message | location]
                    [permision id not equal 1 will be redirected]
                    */   
                    permissionIdAdminOnlycanAccess('error','Access Denied! 🙁','index.php'); 
                    echo "<h1 class='title'>comment</h1>"; 
                    echo "<h3>coding for comments are not available at the moment :(</h3>"; 
                    break;
                case 'favorit':
                    echo "<h1 class='title'>favorit</h1>";
                    break;
                case 'category':
                    /*
                    Admin only has the access to this topic
                    PARAM[type(error/success) | message | location]
                    [permision id not equal 1 will be redirected]
                    */   
                    permissionIdAdminOnlycanAccess('error','Access Denied! 🙁','index.php');  
                    include_once('inc/dash_cat.php');
                    break;
                case 'member':
                    /*
                    Admin only has the access to this topic
                    PARAM[type(error/success) | message | location]
                    [permision id not equal 1 will be redirected]
                    */   
                    permissionIdAdminOnlycanAccess('error','Access Denied! 🙁','index.php'); 
                    include_once('inc/dash_members.php');
                    break;
                default:
                    include_once('inc/dash_insight.php');
              
            }
               ?>
                </div>
            </div>
       </section>
   </main> 
   <!-- popup container -->
    <div class="popup">
        <div class="popup-container">
        </div>
    </div>        
    <!--notifications-->
    <small class="notifications_global_api"></small>
<?php 
include_once("inc/footers.php");