  <div class="login">
    <h1><?php echo lang('login_heading');?></h1>
    <div id="infoMessage"><?php echo $message;?></div>
    <br>
    <?php echo form_open("auth/login");?>
      <p><?php echo form_input($identity, null, 'placeholder="Email/Username"');?></p>
      <p><?php echo form_input($password, null, 'placeholder="Password"');?></p>
      <p class="remember_me">
        <label>
          <input type="checkbox" name="remember_me" id="remember_me">
          Remember me on this computer
        </label>
      </p>
      <p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo form_submit('submit', lang('login_submit_btn'));?></p>
    <?php echo form_close();?>
  </div>

  <div class="login-help">
    <p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p>
  </div>