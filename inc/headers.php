<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- SEO -->
  <meta name="description" content="movies and series reviews opinion">
  <meta name="keywords" content="movie, series ,watch,reviews, film, movie scoop">
  <meta name="robots" content="index, follow" />
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
  <link rel="manifest" href="img/favicon/site.webmanifest">
  <link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#0a1d37">
  <link rel="shortcut icon" href="img/favicon/favicon.ico">
  <meta name="msapplication-TileColor" content="#0a1d37">
  <meta name="msapplication-config" content="img/favicon/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">
  <!-- FontAwsome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
  <!-- CSS -->
  <link rel="stylesheet" href="css/style.css">
  <?php if(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('Login_reg.Php')):?>
  <link rel="stylesheet" href="css/login_reg.css">
  <?php elseif(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('dashboard.Php')):?>
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- tinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <?php elseif(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('profile.Php')):?>
    <link rel="stylesheet" href="css/profile.css">
  <?php elseif(strtolower(basename($_SERVER['SCRIPT_NAME'])) == strtolower('index.Php')):?>
    <link rel="stylesheet" href="css/home.css">
    <?php endif; ?>
  <title>Movie Scoup  <?php echo isset($title) ? $title : ''; ?></title>
</head>
<body>