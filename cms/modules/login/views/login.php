<div id="login">
  <h4 class="text-center">CMS</h4>
  <div class="separator col-xs-12"></div>

  <div class="col-sm-6 col-sm-offset-3" id="login-form">
    <div id="login-lock"><img src="<?php echo base_img('logo.png'); ?>" height="60" /></div>
    <form role="form" id="validateSubmitForm1" method="POST">
      <div class="form-group">
        <label for="exampleInputEmail1"><?php echo T_('Usuário/E-mail'); ?></label>
        <input type="text" name="username" class="form-control" id="exampleInputEmail1" autofocus>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1"><?php echo T_('Senha'); ?></label>
        <input type="password" name="password" class="form-control" id="exampleInputPassword1">
      </div>
      <button type="submit" class="btn btn-primary btn-block"><?php echo T_('Entrar'); ?></button>
    </form>
  </div>
</div>