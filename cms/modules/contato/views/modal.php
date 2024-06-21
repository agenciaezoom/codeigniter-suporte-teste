  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo T_('E-mail recebido em'); ?>: <?php echo $item->date.' às '.$item->hour; ?></h4>
      </div>
      <div class="modal-body">
        <dl class="list-contact">
            <?php if (isset($item->type) &&  $item->type) { ?>
            <dt><?php echo T_('Tipo'); ?></dt> <dd><?php echo $item->type ?></dd>

            <?php } if (isset($item->area) &&  $item->area) { ?>
            <dt><?php echo T_('Área'); ?></dt> <dd><?php echo $item->area ?></dd>

            <?php } if (isset($item->name) &&  $item->name) { ?>
            <dt><?php echo T_('Nome'); ?></dt> <dd><?php echo $item->name ?></dd>

            <?php } if (isset($item->email) &&  $item->email) { ?>
            <dt><?php echo T_('E-mail'); ?></dt> <dd><?php echo $item->email ?></dd>

            <?php } if (isset($item->phone) &&  $item->phone) { ?>
            <dt><?php echo T_('Telefone'); ?></dt> <dd><?php echo $item->phone ?></dd>

            <?php } if (isset($item->mobile) &&  $item->mobile) { ?>
            <dt><?php echo T_('Celular'); ?></dt> <dd><?php echo $item->mobile ?></dd>

            <?php } if (isset($item->city) &&  $item->city) { ?>
            <dt><?php echo T_('Cidade'); ?></dt> <dd><?php echo $item->city ?></dd>

            <?php } if (isset($item->state) &&  $item->state) { ?>
            <dt><?php echo T_('Estado'); ?></dt> <dd><?php echo $item->state ?></dd>

            <?php } if (isset($item->subject) &&  $item->subject) { ?>
            <dt><?php echo T_('Assunto'); ?></dt> <dd><?php echo $item->subject ?></dd>

            <?php } if (isset($item->store) &&  $item->store) { ?>
            <dt><?php echo T_('Loja'); ?></dt> <dd><?php echo $item->store ?></dd>

            <?php } if (isset($item->project) &&  $item->project) { ?>
            <dt><?php echo T_('Projeto'); ?></dt> <dd><?php echo $item->project ?></dd>

            <?php } if (isset($item->message) &&  $item->message){ ?>
            <dt><?php echo T_('Mensagem'); ?></dt> <dd><?php echo $item->message ?></dd>

            <?php } ?>
        </dl>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
