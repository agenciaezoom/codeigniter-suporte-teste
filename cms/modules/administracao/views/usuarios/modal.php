<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center">Alterar Senha</h4>
        </div>
        <div class="modal-body">
            <form action="<?php echo site_url('administracao/usuarios/change-password/'.$id); ?>" class="change-password-modal" role="form" enctype="multipart/form-data" method="post">
                <div class="col-xs-12 col-sm-6 form-group">
                    <label for="inputPassword" class="control-label"><?php echo T_('Nova Senha:'); ?> </label>
                    <input type="password" class="form-control" name="password" id="inputPassword" placeholder="<?php echo T_('Senha'); ?>">
                </div>
                <div class="col-xs-12 col-sm-6 form-group">
                    <label for="inputPassword2" class="control-label"><?php echo T_('Repetir Senha:'); ?> </label>
                    <input type="password" class="form-control" name="password2" id="inputPassword2" placeholder="<?php echo T_('Repetir Senha'); ?>">
                </div>

                <div class="separator col-sm-12"></div>

                <div class="col-xs-12 form-group text-center">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary"><?php echo T_('Cancelar'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo T_('Alterar'); ?></button>
                </div>

            </form>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>