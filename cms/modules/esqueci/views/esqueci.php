<div id="forgot">
    <h4 class="text-center"><i class="fa fa-question"></i> <?php echo T_('Esqueci minha Senha'); ?></h4>
    <div id="forgot-pass"><i class="fa fa-question"></i></div>
    <div class="col-sm-6 col-sm-offset-3" id="forgot-form">
        <?php if (!isset($hash)){ ?>
        <form role="form" id="validateSubmitForm1" method="POST">
            <div class="form-group">
                <label for="username"><?php echo T_('E-mail/Usuário'); ?></label>
                <input type="text" name="username" class="form-control" id="username" placeholder="<?php echo T_('Digite seu E-mail ou seu nome de Usuário'); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block"><?php echo T_('Enviar'); ?></button>
            <div class="login-link">
                <a href="<?php echo site_url(); ?>"><?php echo T_('Voltar'); ?></a>
            </div>
            <p class="login-explain"><?php echo T_('Informe seu nome de usuário ou seu e-mail de cadastro e lhe enviaremos um link para que possa alterar a sua senha.'); ?></p>
        </form>
        <?php }else{ ?>
        <form role="form" id="validateSubmitForm2" method="POST">
            <div class="form-group">
                <label for="password"><?php echo T_('Nova Senha'); ?></label>
                <input type="password" name="password" class="form-control" id="password" placeholder="<?php echo T_('Insira sua Nova Senha'); ?>">
            </div>
            <div class="form-group">
                <label for="password2"><?php echo T_('Repetir Senha'); ?></label>
                <input type="password" name="password2" class="form-control" id="password2" placeholder="<?php echo T_('Repita sua Nova Senha'); ?>">
            </div>
            <input type="hidden" name="token" class="form-control" id="token" value="<?php echo $hash; ?>">
            <button type="submit" class="btn btn-primary btn-block"><?php echo T_('Enviar'); ?></button>
            <div class="login-link">
                <a href="<?php echo site_url(); ?>"><?php echo T_('Voltar'); ?></a>
            </div>
        </form>
        <?php } ?>
    </div>
</div>