<form action="<?php echo site_url($current_module->slug . '/' . (isset($user) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" data-toggle="tab"><i></i> <?php echo T_('Dados Gerais'); ?></a></li>
    <?php if (!isset($user) || $this->auth->data('id') != $user->id || $this->auth->data('id') == 1) { ?>
      <li><a href="#tab2" data-toggle="tab"><i></i> <?php echo T_('Permissões'); ?></a></li>
    <?php } ?>
  </ul>

  <div class="tab-content col-sm-12">

    <!-- Tab (Dados Gerais) -->
    <div class="tab-pane fade active in" id="tab1">
      <?php if (isset($user)) { ?>
        <input type="hidden" name="id" value="<?php echo $user->id; ?>" id="inputId" />
      <?php } ?>

      <div class="col-xs-12 col-md-6 form-group">
        <label for="inputName" class="control-label"><?php echo T_('Nome:'); ?> </label>
        <input type="text" class="form-control" name="name" id="inputName" placeholder="<?php echo T_('Nome'); ?>" value="<?php echo isset($user) ? $user->name : ''; ?>">
      </div>
      <div class="col-xs-12 col-md-6 form-group">
        <label for="inputUsername" class="control-label"><?php echo T_('Login:'); ?> </label>
        <input type="text" class="form-control" name="login" id="inputUsername" placeholder="Nome de usuário" value="<?php echo isset($user) ? $user->login : ''; ?>">
      </div>
      <div class="col-xs-12 col-md-6 form-group">
        <label for="inputPhone" class="control-label"><?php echo T_('Telefone:'); ?> </label>
        <input type="text" class="form-control" name="phone" id="inputPhone" placeholder="<?php echo T_('Telefone'); ?>" value="<?php echo isset($user) ? $user->phone : ''; ?>">
      </div>
      <div class="col-xs-12 col-md-6 form-group">
        <label for="inputEmail" class="control-label"><?php echo T_('E-mail:'); ?> </label>
        <input type="text" class="form-control" name="email" id="inputEmail" placeholder="<?php echo T_('E-mail'); ?>" value="<?php echo isset($user) ? $user->email : ''; ?>">
      </div>

      <?php
      $userCompanies = !empty($user) ? explode(',', $user->companies) : array();
      ?>
      <div class="col-xs-12 col-md-6 form-group">
        <label for="inputCompanies" class="control-label"><?php echo T_('Empresas Disponíveis:'); ?> </label>
        <select required class="form-control select2 not-hide" name="companies[]" id="inputCompanies" data-width="100%" data-placeholder="<?php echo T_('Selecione as empresas'); ?>" multiple>
          <option value=""></option>
          <?php foreach ($companies as $key => $value) {
            if (!$this->auth->data('admin') == 1 && !in_array($value->id, $this->auth->data('companies')) && $value->id != $this->auth->data('companies'))
              continue;
          ?>
            <option <?php echo (isset($user) && in_array($value->id, $userCompanies)) ? ' selected ' : ''; ?> value="<?php echo $value->id; ?>"><?php echo $value->fantasy_name; ?></option>
          <?php } ?>
        </select>
      </div>
      <?php

      if (!isset($user)) { ?>
        <div class="col-xs-12 col-md-6 form-group">
          <label for="inputPassword" class="control-label"><?php echo T_('Senha:'); ?> </label>
          <input type="password" class="form-control" name="password" id="inputPassword" placeholder="<?php echo T_('Senha'); ?>">
        </div>
        <div class="col-xs-12 col-md-6 form-group">
          <label for="inputPassword2" class="control-label"><?php echo T_('Repetir Senha:'); ?> </label>
          <input type="password" class="form-control" name="password2" id="inputPassword2" placeholder="<?php echo T_('Repetir Senha'); ?>">
        </div>
      <?php }

      if (!isset($user) || $this->auth->data('id') != $user->id) { ?>
        <div class="col-xs-12 col-md-6 form-group">
          <label for="inputName" class="col-xs-12 control-label"><?php echo T_('Ativo:'); ?> </label>
          <div class="make-switch">
            <?php if (!isset($user) || $user->status == '1') { ?>
              <div class="button-switch button-on"><?php echo T_('Sim'); ?></div>
              <input type="checkbox" name="status" checked="checked" id="inputStatus">
            <?php } else { ?>
              <div class="button-switch button-off"><?php echo T_('Não'); ?></div>
              <input type="checkbox" name="status" id="inputStatus">
            <?php } ?>
          </div>
        </div>
      <?php }

      if ($this->auth->data('admin') == 1) { ?>
        <div class="col-xs-12 col-md-6 form-group">
          <label for="inputAdmin" class="col-xs-12 control-label"><?php echo T_('Administrador:'); ?> </label>
          <div class="make-switch">
            <?php if (!isset($user) || $user->admin == '1') { ?>
              <div class="button-switch button-on"><?php echo T_('Sim'); ?></div>
              <input type="checkbox" name="admin" checked="checked" id="inputAdmin">
            <?php } else { ?>
              <div class="button-switch button-off"><?php echo T_('Não'); ?></div>
              <input type="checkbox" name="admin" id="inputAdmin">
            <?php } ?>
          </div>
        </div>
      <?php }

      $this->load->view('gallery/single-file', array(
        'label' => 'Avatar',
        'typeupload' => null,
        'module' => '../cms/userfiles/avatar',
        'file' => (isset($user->avatar) && $user->avatar) ? $user->avatar : null,
        'dimensions' => array(
          'w' => 200,
          'h' => 200
        ),
        'id' => 'fileuploadImage-primary',
        'name' => 'avatar',
        'key' => 1,
        'upload' => site_url('gallery/upload/image'),
        'ext' => 'jpg|png'
      )); ?>
    </div>
    <div class="tab-pane fade " id="tab2">
      <?php if (!isset($user) || $this->auth->data('id') != $user->id || $this->auth->data('id') == 1) { ?>
        <div class="col-sm-12 permission-wrapper form-group">
          <div class="col-xs-12">
            <label for="inputGroup" class="control-label"><?php echo T_('Grupo:'); ?> </label>
            <select name="id_group" id="inputGroup" class="form-control selectpicker">
              <option value=""><?php echo T_('Selecione o grupo do usuário'); ?></option>
              <?php foreach ($groups as $key => $group) { ?>
                <option value="<?php echo $group->id; ?>" <?php echo (isset($user) && $group->id == $user->id_group) ? ' selected' : ''; ?>><?php echo $group->name; ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="col-xs-1">
            <img src="<?php echo base_img('loading.gif'); ?>" class="loading" />
          </div>

          <div class="col-xs-12 separator"></div>

          <div class="col-xs-12 permission-data">
            <?php echo isset($modules) ? $this->load->view('usuarios/permissoes', array('modules' => $modules), TRUE) : '' ?>
          </div>
        </div>
      <?php } ?>
    </div>


    <div class="clearfix"></div>
  </div>

  <div class="col-sm-12 text-center">
    <a href="<?php echo site_url($current_module->slug); ?>" class="btn btn-default">Cancelar</a>
    <button type="submit" class="btn btn-primary"><?php echo isset($user) ? 'Salvar' : 'Cadastrar'; ?></button>
  </div>
</form>