<?php
require_once("inc/functions.php");

  if(session_status() === PHP_SESSION_NONE){

    ob_start();
    session_start();
/*
  visitors not allowed to access here
  PARAMETER[type(success | error) , message, redirect]
*/
    usersAccessOnly('error','acces denied! 🙁','index.php');
    unset($_SESSION['id']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['email']);
    unset($_SESSION['gender']);
    unset($_SESSION['image']);
    unset($_SESSION['about']);
    // session_destroy();
/*
  Accept PARAMETER[type(success | error) , message]
*/ 
    setMessage('success','good bye..you will be missed! 😄');
    redirectHeader("index.php"); //Accept PARAMETER[location]
    
  }
  else{

/*
  visitors not allowed to access here
  PARAMETER[type(success | error) , message, redirect]
*/
usersAccessOnly('error','acces denied! 🙁','index.php');
  /*
  Accept PARAMETER[type(success | error) , message]
  */ 
    unset($_SESSION['id']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['email']);
    unset($_SESSION['gender']);
    unset($_SESSION['image']);
    unset($_SESSION['about']);
    // session_destroy();
 
    setMessage('success','good bye..you will be missed! 😄');
    redirectHeader("index.php");//Accept PARAMETER[location]
  
  }