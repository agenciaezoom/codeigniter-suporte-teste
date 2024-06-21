<div class="listing-header">

  <?php if (in_array('cadastrar', $session_permissions[$current_module->id])) { ?>
    <div class="no-padleft col-xs-5 col-sm-4 col-md-2 col-lg-2">
      <a href="<?php echo site_url('administracao/grupos/cadastrar'); ?>">
        <button class="btn btn-primary btn-cadastro"><i class="fa fa-plus"></i> <?php echo T_('Cadastrar'); ?></button>
      </a>
    </div>
  <?php } ?>
  <form action="<?php echo site_url('administracao/grupos'); ?>" class="filter" method="POST">
    <div class="col-xs-7 col-sm-8 col-md-3 col-lg-2">
      <select id="filter-show" name="show" class="selectpicker vivisualizeItens" data-style="btn-primary" data-width="100%">
        <option title="<?php echo T_('Visualizar: 10'); ?>" value="10" <?php echo ($show == 10) ? ' selected="selected"' : ''; ?>>10</option>
        <option title="<?php echo T_('Visualizar: 25'); ?>" value="25" <?php echo ($show == 25) ? ' selected="selected"' : ''; ?>>25</option>
        <option title="<?php echo T_('Visualizar: 50'); ?>" value="50" <?php echo ($show == 50) ? ' selected="selected"' : ''; ?>>50</option>
        <option title="<?php echo T_('Visualizar: 100'); ?>" value="100" <?php echo ($show == 100) ? ' selected="selected"' : ''; ?>>100</option>
      </select>
    </div>
    <div class="no-padleft col-xs-12 col-sm-12 col-md-7 col-lg-8">
      <div class="input-group col-sm-12">
        <input name="search" type="text" class="form-control" placeholder="<?php echo T_('Buscar por...'); ?>" value="<?php echo ($search) ? $search : ''; ?>" id="filter-search">
        <div class="input-group-btn">
          <button class="btn btn-default rounded-right" type="submit">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
      <?php if ($search) { ?>
        <div class="col-xs-12">
          <div class="text-right innerT">
            <a href="<?php echo site_url('comum/limpar-busca'); ?>"><i class="fa fa-times"></i> <?php echo T_('Limpar Busca'); ?></a>
          </div>
        </div>
      <?php } ?>
    </div>
  </form>
</div>
<?php if (count($groups) > 0) { ?>
  <div class="no-padleft table-list col-sm-12">
    <div class="separator"></div>
    <table class="tableTools table table-striped checkboxs footable">
      <thead class="bg-gray">
        <tr>
          <th class="text-center">
            <div class="checkbox checkbox-single margin-none">
              <label class="checkbox-custom">
                <i class="far fa-fw fa-square"></i>
                <input type="checkbox">
              </label>
            </div>
          </th>
          <th><?php echo T_('Grupo'); ?>
          <th><?php echo T_('Ativo'); ?></th>
          <th data-hide="phone" class="text-center action-column"><?php echo T_('Ações'); ?></th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($groups as $key => $group) { ?>
          <tr data-id="<?php echo $group->id; ?>">
            <td class="text-center text-middle" width="50px">
              <div class="checkbox checkbox-single margin-none">
                <label class="checkbox-custom">
                  <i class="far fa-fw fa-square"></i>
                  <input type="checkbox">
                </label>
              </div>
            </td>
            <td class="text-middle"><?php echo $group->name; ?></td>
            <td>
              <?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <div class="form-group">
                  <div class="make-switch">
                    <?php if ($group->status == '1') { ?>
                      <div class="button-switch button-on"><?php echo T_('Sim'); ?></div>
                      <input type="checkbox" name="status" checked="checked" id="inputStatus">
                    <?php } else { ?>
                      <div class="button-switch button-off"><?php echo T_('Não'); ?></div>
                      <input type="checkbox" name="status" id="inputStatus">
                    <?php } ?>
                  </div>
                </div>
              <?php } else {
                echo $user->status ? T_('Sim') : T_('Não');
              } ?>
            </td>
            <td class="text-right">
              <?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url('administracao/grupos/editar/' . $group->id); ?>"><button class="btn btn-default"> <?php echo T_('Editar'); ?></button></a>
              <?php }
              if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url('administracao/grupos/delete/' . $group->id); ?>" class="delete-button"><button class="btn btn-default"> <?php echo T_('Excluir'); ?></button></a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php } else { ?>
  <div class="no-padleft col-sm-12 text-center">
    <div class="separator-horizontal col-sm-12"></div>
    <?php echo T_('Nenhum grupo cadastrado.'); ?>
  </div>
<?php } ?>

<?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
  <div class="col-sm-3">
    <form class="hide delete-all-form" action="<?php echo site_url('administracao/empresas/delete-multiple'); ?>" method="POST">
      <input type="hidden" name="id" value="">
      <button class="btn btn-primary btn-stroke"> <?php echo T_('Excluir selecionados'); ?></button>
    </form>
  </div>
<?php } ?>
<div class="col-sm-9 text-right pagination-wrapper">
  <?php echo $paginacao; ?>
</div>