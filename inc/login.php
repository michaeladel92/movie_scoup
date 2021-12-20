<section  class="login_form active">
   <form id="loginForm">
      <!--token  -->
      <input type="hidden" name="token" value="<?= isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>">
      <!-- email -->
      <div class="control">
      <label for="email">email</label>
      <input type="text" name="email" placeholder="email">
    </div>
    <!-- password -->
    <div class="control">
      <label for="password">password</label>
      <input type="password" name="password"  placeholder="password">
    </div>
    <!-- <a class="link forget-pass" href="#">forget password?</a> -->
    <input type="submit" name="login" class="btn btn_submit" value="login"> 
    <small id="nofitication_login" class="message notifications"></small>
    </form>
</section>