<section class="registeration_form ">
          <form id="register_form"  enctype="multipart/form-data">
             <!--token  -->
             <input type="hidden" name="token" value="<?= isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>">
            <!-- name -->
            <div class="control">
              <label for="user_name">name</label>
              <input id="reg_name" data-names="name" type="text" name="user_name" placeholder="name">
              <small class="message"></small>
            </div>
            <!-- email -->
            <div class="control">
              <label for="email">email</label>
              <input id="reg_email" data-names="email" type="text" name="email" placeholder="email">
              <small class="message"></small>
            </div>
            <!-- password -->
            <div class="control">
              <label for="password">password</label>
              <input id="reg_password" data-names="password" type="password" name="password" placeholder="password">
              <small class="message"></small>
            </div>
            <!-- confirm password -->
            <div class="control">
              <label for="con_password">confirm password</label>
              <input id="reg_confirm_pass" data-names="con-password" type="password" name="con_password" placeholder="confirm password">
              <small class="message"></small>
            </div>
            <!-- gender -->
            <div class="control">
              <label for="gender">gender</label>
              <select id="reg_gender" data-names="gender" name="gender">
                <option value="">choose gender</option>
                <option value="male">male</option>
                <option value="female">female</option>
              </select>
              <small class="message"></small>
            </div>
            <!-- Profile -->
            <div class="control">
              <label for="image">Profile Picture</label>
              <input id="image" type="file" name="image" placeholder="image">
              <small class="message"></small>
            </div>
            <!-- about -->
            <div class="control">
              <label for="about">about me</label>
              <textarea id="reg_about" name="about" placeholder="brief about yourself"></textarea>
              <small class="max-length"> 0 / 500</small>
              <small class="message"></small>
            </div>
            <input type="submit" name="register" class="btn btn_submit" value="register">
            <small id="nofitication_register" class="message notifications"></small>
          </form>
        </section>