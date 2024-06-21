<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo isset($itens) ? T_('Escolha uma categoria para vincular') : T_('Escolha uma categoria para desvincular'); ?></h4>
        </div>
        <div class="modal-body">
            <form action="<?php echo $slug; ?>" class="attach-modal" role="form" enctype="multipart/form-data" method="post">
                <?php if( isset($itens) ){ ?>
                <div class="col-sm-12">
                    <select name="select-category" id="inputCategory" class="form-control select2" data-placeholder="<?php echo T_('Selecione a categoria'); ?>">
                        <option></option>
                        <?php echo $this->load->view('modal-category-list', array('child' => $itens, 'indent' => '')); ?>
                    </select>
                </div>
                <?php } ?>
                <input type="hidden" name="id" id="inputId" value="<?php echo $id; ?>" />

                <div class="separator col-sm-12"></div>

                <div class="col-sm-12 text-center">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary"><?php echo T_('Cancelar'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo isset($item) ? T_('Vincular') : T_('Desvincular'); ?></button>
                </div>
            </form>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>