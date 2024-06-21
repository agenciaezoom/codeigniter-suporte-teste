<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo T_('Comentário recebido em:'); ?> <?php echo $item->date.' '.$item->hour; ?></h4>
        </div>
        <div class="modal-body">
            <div class="col-sm-6"><strong><?php echo T_('Nome:'); ?></strong> <?php echo $item->name; ?></div>
            <div class="col-sm-6"><strong><?php echo T_('E-mail:'); ?></strong> <?php echo $item->email; ?></div>
            <div class="col-sm-6"><strong><?php echo T_('Aprovado:'); ?></strong> <?php echo isset($item) && $item->approved == 1 ? T_('Sim') : T_('Não'); ?></div>
            <div class="col-sm-6"><strong><?php echo T_('IP:'); ?></strong> <?php echo $item->ip; ?></div>
            <div class="col-sm-12"><strong><?php echo T_('Comentado no site:'); ?></strong> <?php echo '';//$item->site; ?></div>
            <div class="col-sm-12"><strong><?php echo T_('Mensagem:'); ?></strong></div>
            <div class="col-sm-12"><?php echo $item->message; ?></div>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>