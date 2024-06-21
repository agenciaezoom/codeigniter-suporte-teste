<header class="hidden-print">
  <div id="menu-logo" class="hidden-xs">
    <a href="<?php echo site_url(); ?>" class="display-block-inline logo"><img src="<?php echo base_img('logo.png'); ?>" height="30" alt="Ezoom CMS"></a>
  </div>
  <!-- Abre Fecha Menu lateral -->
  <div class="menu-action pull-left">
    <button id="toggle-sidebar" title="Abrir/Fechar menu lateral" class="btn btn-primary"><i class="fa fa-bars fa-2x"></i></button>
  </div>
  <div class="menu-action pull-left">
    <a title="Ir para o Site" target="_blank" href="<?php echo site_url('../'); ?>" class="btn btn-primary"><i class="fa fa-home fa-2x"></i></a>
  </div>
  <!-- Botão Configurar e Logout -->
  <div id="menu-config" class="pull-right" data-toggle="menu-config-dropdown">
    <span class="pull-left">
      <img src="<?php echo site_url('image/resize_crop?src=' . $this->auth->data('avatar') . '&w=30&h=30&q=85'); ?>" alt="user" class="img-circle">
      <span class="caret"></span>
    </span>
    <div class="menu-config-dropdown pull-right">
      <ul>
        <li><a href="<?php echo site_url('administracao/usuarios/editar/' . $this->auth->data('id')); ?>"><?php echo T_('Configurações'); ?></a></li>
        <li><a href="<?php echo site_url('administracao/usuarios/modal/' . $this->auth->data('id')); ?>" class="change-password" title="<?php echo T_('Alterar Senha'); ?>"><?php echo T_('Alterar Senha'); ?></a>
        <li><a href="<?php echo site_url('logout'); ?>"><?php echo T_('Sair'); ?></a></li>
      </ul>
    </div>
  </div>
  <!-- Botão selecionar empresa -->
  <?php if ($this->auth->show_company_switch()) { ?>
    <div id="menu-config-company" class="pull-right" data-toggle="company-config-dropdown">
      <?php if (count($all_companies) > 1) { ?>
        <div class="pull-left"><span class="company"><?php echo $company->fantasy_name; ?></span><span class="caret"></span></div>
        <div class="company-config-dropdown pull-right">
          <ul>
            <?php
            foreach ($all_companies as $key => $value) {
              if ($this->auth->data('company') != $value->id) {
            ?>
                <li data-id="<?php echo $value->id; ?>">
                  <a href="#"><?php echo $value->fantasy_name; ?></a>
                </li>
            <?php
              }
            }
            ?>
          </ul>
        </div>
    </div>
  <?php } else { ?>
    <div class="pull-left"><span class="company"><?php echo $company->fantasy_name; ?></span></div>
<?php }
    } ?>
<div class="clearfix"></div>
</header>