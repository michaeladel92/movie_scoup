<?php
try{
  define('HOST','localhost');
  define('USER','root');
  define('PASS','');
  define('DB','movies_app');

  $con = new PDO("mysql:host=".HOST.";dbname=".DB."",USER,PASS);
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}