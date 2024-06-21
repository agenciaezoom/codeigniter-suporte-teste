<?php $this->load->view('comum/busca');
if ($items) { ?>

  <div class="no-padleft table-list col-sm-12">
    <table class="table checkboxs">
      <thead class="bg-gray">
        <tr>
          <th class="text-center" width="50px">
            <a href="<?= order_url('false'); ?>"><i class="fa fa-sort"></i></a>
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

          <th>Imagem</th>

          <th class="title-column">
            <a href="<?= order_url('title'); ?>">
              <?= T_('Título') . ' ' . order_ico('title', $order_by); ?>
            </a>
          </th>
          <th>
            <a href="<?= order_url('status'); ?>">
              <?= T_('Ativo') . ' ' . order_ico('status', $order_by); ?>
            </a>
          </th>
          <th class="text-center action-column"><?= T_('Ações'); ?></th>
        </tr>
      </thead>

      <tbody id="sortable">
        <?php foreach ($items as $key => $item) { ?>
          <tr data-id="<?= $item->id; ?>" id="item-<?= $item->id; ?>">

            <td class="text-left <?= $order_by || $search ? '' : ' moveSortable '; ?>">
              <i class="fa fa-arrows-alt"></i>
              <span><?= $item->order_by; ?></span>
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

            <td><img src="<?= site_url('../image/resize_canvas?width=250&height=250&src=userfiles/atributos/' . $item->image) ?>" alt="<?= $item->title ?>"></td>
            <td class="text-middle"><?= $item->title; ?></td>

            <td>
              <?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <div class="make-switch">
                  <?php if ($item->status == '1') { ?>
                    <div class="button-switch button-on"><?= T_('Sim'); ?></div>
                    <input type="checkbox" name="status" checked="checked" id="inputStatus">
                  <?php } else { ?>
                    <div class="button-switch button-off"><?= T_('Não'); ?></div>
                    <input type="checkbox" name="status" id="inputStatus">

                  <?php } ?>
                </div>
              <?php } else {
                echo $item->status ? 'Sim' : 'Não';
              } ?>
            </td>

            <td class="text-right nowrap">
              <?php if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <a href="<?= site_url($current_module->slug . '/editar/' . $item->id); ?>"><button class="btn btn-default"><i class="fa fa-pencil"></i> <?= T_('Editar'); ?></button></a>
              <?php }
              if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
                <a href="<?= site_url($current_module->slug . '/delete/' . $item->id); ?>" class="delete-button"><button class="btn btn-default"><i class="fa fa-trash-o"></i> <?= T_('Excluir'); ?></button></a>
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
    <?= T_('Nenhum dado cadastrado.'); ?>
  </div>
<?php }

if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
  <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
    <form class="hide delete-all-form" action="<?= site_url($current_module->slug . '/delete-multiple'); ?>" method="POST">
      <input type="hidden" name="id" value="">
      <button class="btn btn-primary btn-stroke"><i class="fa fa-trash-o"></i> <?= T_('Excluir selecionados'); ?></button>
    </form>
    <div class="clearfix"></div>
  </div>
<?php } ?>

<div class="col-sm-9 text-right pagination-wrapper">
  <?= $paginacao; ?>
</div>