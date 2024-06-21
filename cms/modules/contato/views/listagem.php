<?php $this->load->view('comum/busca');
if ($items) { ?>

  <div class="no-padleft table-list col-sm-12">
    <div class="separator"></div>
    <table class="table checkboxs">
      <thead class="bg-gray">
        <tr>
          <th class="text-center" width="30px">
            <a href="<?php echo order_url('false'); ?>"></a>
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
          <th>
            <a href="<?php echo order_url('name'); ?>">
              <?php echo T_('Remetente') . ' ' . order_ico('name', $order_by); ?>
            </a>
          </th>
          <th>
            <a href="<?php echo order_url('email'); ?>">
              <?php echo T_('E-mail') . ' ' . order_ico('email', $order_by); ?>
            </a>
          </th>
          <th>
            <a href="<?php echo order_url('subject'); ?>">
              <?php echo T_('Assunto') . ' ' . order_ico('subject', $order_by); ?>
            </a>
          </th>
          <th>
            <a href="<?php echo order_url('created'); ?>">
              <?php echo T_('Data') . ' ' . order_ico('created', $order_by); ?>
            </a>
          </th>
          <th class="text-center action-column"><?php echo T_('Ações'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($items as $key => $item) {
        ?>
          <tr data-id="<?php echo $item->id; ?>" id="item-<?php echo $item->id; ?>">
            <td>
              <?php
              if ($item->status === 'Não enviada') {
              ?><i class="fa fa-exclamation-circle text-warning" aria-hidden="true" title="Mensagem não enviada"></i><?php
                                                                                                                    } else if ($item->status === 'Respondida') {
                                                                                                                      ?><i class="fa fa-check text-success" aria-hidden="true" title="Mensagem respondida"></i><?php
                                                                                                                                                                                                              } else {
                                                                                                                                                                                                                ?><i class="fa fa-envelope-o <?php echo ($item->status == 'Lida') ? 'lida' : 'text-primary' ?>" <?php echo ($item->status == 'Lida') ? ' title="Mensagem lida" ' : ' title="Nova mensagem" '; ?>></i><?php
                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                      ?>

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
            <td class="text-middle"><?php echo $item->name; ?></td>
            <td class="text-middle"><a class="responder" title="Responder" href="mailto: <?php echo $item->email; ?>"><?php echo $item->email; ?></a></td>
            <td class="text-middle"><?php echo $item->subject; ?></td>
            <td class="text-middle"><?php echo $item->date . ' às ' . $item->hour; ?></td>
            <td class="text-right action-column">
              <?php if ($item->status === 'Não enviada') : ?>
                <a class="btn btn-default reenviar" href="#" title="Reenviar"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>
              <?php endif ?>
              <span href="#responsive" data-toggle="modal" class="chamaModal btn btn-default" title="Visualizar"><i class="fa fa-eye"></i></span>
              <?php if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
                <a title="Excluir" href="<?php echo site_url($current_module->slug . '/delete/' . $item->id); ?>" class="delete-button"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
              <?php } ?>
            </td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  </div>
<?php
} else {
?>
  <div class="no-padleft col-sm-12 text-center">
    <div class="separator-horizontal col-sm-12"></div>
    Nenhum dado cadastrado.
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