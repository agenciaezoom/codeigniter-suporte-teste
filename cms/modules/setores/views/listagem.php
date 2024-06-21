<?php $this->load->view('comum/busca');
if ($items) { ?>
  <div class="no-padleft col-sm-12">
    <div class="separator"></div>
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
                  <i class="fa fa-fw fa-square-o"></i>
                  <input type="checkbox">
                </label>
              </div>
            </th>
          <?php } ?>
          <th>
            <a href="<?php echo order_url('title'); ?>">
              <?php echo T_('Título'); ?> <?php echo order_ico('title', $order_by); ?>
            </a>
          </th>
          <th>
            <a href="<?php echo order_url('status'); ?>">
              <?php echo T_('Ativo'); ?> <?php echo order_ico('status', $order_by); ?>
            </a>
          </th>
          <th class="text-center action-column">Ações</th>
        </tr>
      </thead>
      <tbody id="sortable">
        <?php foreach ($items as $key => $item) { ?>
          <tr data-id="<?php echo $item->id; ?>" id="item-<?php echo $item->id; ?>">
            <td class="text-left <?php echo $order_by || $search ? '' : ' moveSortable '; ?>">
              <i class="fa fa-arrows"></i>
              <span><?php echo $item->order_by; ?></span>
            </td>
            <td class="text-center text-middle" width="70px">
              <div class="checkbox checkbox-single">
                <label class="checkbox-custom">
                  <i class="fa fa-fw fa-square-o"></i>
                  <input type="checkbox">
                </label>
              </div>
            </td>
            <td class="text-middle"><?php echo $item->title; ?></td>
            <td>
              <?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
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
                echo $item->status ? T_('Sim') : T_('Não');
              } ?>
            </td>
            <td class="text-right">
              <?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url($module . '/editar/' . $item->id); ?>"><button class="btn btn-default"><i class="fa fa-pencil"></i> Editar</button></a>
              <?php }
              if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
                <a href="<?php echo site_url($module . '/delete/' . $item->id); ?>" class="delete-button"><button class="btn btn-default"><i class="fa fa-trash-o"></i> Excluir</button></a>
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
    <?php echo T_('Nenhum dado cadastrado.'); ?>
  </div>
<?php } ?>
<div class="col-sm-3">
  <form class="hide delete-all-form" action="<?php echo site_url($module . '/delete-multiple'); ?>" method="POST">
    <input type="hidden" name="id" value="">
    <button class="btn btn-primary btn-stroke"><i class="fa fa-trash-o"></i> <?php echo T_('Excluir selecionados'); ?></button>
  </form>
</div>
<div class="col-sm-9 text-right pagination-wrapper">
  <?php echo $paginacao; ?>
</div>