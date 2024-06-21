<?php $this->load->view('comum/busca');

if ($items) { ?>
  <div class="no-padleft table-list col-sm-12">
    <table class="table checkboxs">
      <thead class="bg-gray">
        <tr>
          <th class="text-center" width="50px">
            <a href="<?php echo order_url('false'); ?>"><i class="fa fa-sort"></i></a>
          </th>
          <?php if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
            <th data-field="id" class="text-center">
              <div class="checkbox checkbox-single">
                <label class="checkbox-custom">
                  <i class="far fa-fw fa-square"></i>
                  <input type="checkbox">
                </label>
              </div>
            </th>
          <?php } ?>
          <th class="title-column">
            <a href="<?php echo order_url('label'); ?>">
              <?php echo T_('Título') . ' ' . order_ico('label', $order_by); ?>
            </a>
          </th>
          <th>
            <a href="<?php echo order_url('status'); ?>">
              <?php echo T_('Ativo') . ' ' . order_ico('status', $order_by); ?>
            </a>
          </th>
          <th class="text-center action-column"><?php echo T_('Ações'); ?></th>
        </tr>
      </thead>
      <tbody id="sortable">
        <?php foreach ($items as $key => $item) { ?>
          <tr data-id="<?php echo $item->id; ?>" id="item-<?php echo $item->id; ?>">
            <td class="text-left <?php echo $order_by || $search ? '' : ' moveSortable '; ?>">
              <i class="fa fa-arrows-alt"></i>
              <span><?php echo $item->order_by; ?></span>
            </td>
            <?php if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
              <td class="text-center text-middle" width="70px">
                <div class="checkbox checkbox-single">
                  <label class="checkbox-custom">
                    <i class="far fa-fw fa-square"></i>
                    <input type="checkbox">
                  </label>
                </div>
              </td>
            <?php } ?>
            <td class="text-middle"><?php echo $item->label; ?></td>
            <td>
              <?php if (in_array('editar', $session_permissions[$current_module->id]) && $this->auth->data('id') == '1') { ?>
                <div class="make-switch">
                  <?php if ($item->status == '1') { ?>
                    <div class="button-switch button-on"><?php echo T_('Sim'); ?></div>
                    <input type="checkbox" name="status" checked="checked" id="inputStatus">
                  <?php } else { ?>
                    <div class="button-switch button-off"><?php echo T_('Não'); ?></div>
                    <input type="checkbox" name="status" id="inputStatus">

                  <?php } ?>
                </div>
              <?php } else {
                echo $item->status ? 'Sim' : 'Não';
              } ?>
            </td>
            <td class="text-right nowrap">
              <?php if ($this->auth->data('admin') == 1 && in_array('sql', $session_permissions[$current_module->id])) { ?>
                <span href="#responsive" data-toggle="modal" class="chamaModal btn btn-default" title="<?php echo T_('Exportar SQL'); ?>"><i class="fa fa-code"></i> <?php echo T_('SQL'); ?></span>
              <?php }
              if ($this->auth->data('admin') == 1 && in_array('copiar', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url($current_module->slug . '/cadastrar/' . $item->id); ?>"> <button class="btn btn-default"><i class="fa fa-copy"></i> <?php echo T_('Copiar'); ?></button></a>
              <?php }
              if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url($current_module->slug . '/editar/' . $item->id); ?>"> <button class="btn btn-default"> <?php echo T_('Editar'); ?></button></a>
              <?php }
              if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url($current_module->slug . '/delete/' . $item->id); ?>" class="delete-button"> <button class="btn btn-default"> <?php echo T_('Excluir'); ?></button></a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <?php if ($this->auth->data('admin') == 1 && in_array('sql', $session_permissions[$current_module->id])) { ?>
    <div class="listing-header text-right">
      <div class="no-padding outerB-2x col-xs-12">
        <a href="<?php echo site_url($current_module->slug . '/dump'); ?>" target="_blank">
          <button class="btn btn-primary btn-cadastro"><i class="fa fa-code"></i> <?php echo T_('Exportar SQL'); ?></button>
        </a>
      </div>
    </div>
  <?php }
} else { ?>
  <div class="no-padleft col-sm-12 text-center">
    <div class="separator-horizontal col-sm-12"></div>
    <?php echo T_('Nenhum dado cadastrado.'); ?>
  </div>
<?php }

if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
  <div class="col-sm-3">
    <form class="hide delete-all-form" action="<?php echo site_url($current_module->slug . '/delete-multiple'); ?>" method="POST">
      <input type="hidden" name="id" value="">
      <button class="btn btn-primary btn-stroke"> <?php echo T_('Excluir selecionados'); ?></button>
    </form>
  </div>
<?php } ?>

<div class="col-sm-9 text-right pagination-wrapper">
  <?php echo $paginacao; ?>
</div>