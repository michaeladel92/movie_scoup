<?php
$title = "| Login";
require_once("inc/init.php");
/*
users not allowed to access here
PARAMETER[type(success | error) , message, redirect]
*/ 
visitorAccessOnly('error','Access Denied! 🙁','index.php'); 
// token generate for Login & register 
$_SESSION['token'] = token_generator();
include_once("inc/nav.php");
?>
    <main class="credentials">
        <div class="options">
          <h2 class="login active">Login</h2> 
          <span></span>
          <h2 class="register">Register</h2>
        </div>
        <!-- loading -->
        <div class="loading">
            <span></span>
        </div>
        <!--Register  -->
        <?php include_once("inc/register.php"); ?>
        <!-- login -->
        <?php include_once("inc/login.php"); ?>
    </main>
<?php
include_once("inc/footers.php");