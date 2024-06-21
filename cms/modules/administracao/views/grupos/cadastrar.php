<form action="<?php echo site_url('administracao/grupos/add')?>" id="validateSubmitForm" class="form-horizontal" role="form" method="post">
    <h3 class="col-sm-12"><?php echo T_('Informações do Grupo'); ?></h3>

    <div class="col-xs-12 form-group">
        <label for="inputName" class="col-xs-12 control-label"><?php echo T_('Ativo:'); ?> </label>
        <div class="make-switch">
            <div class="button-switch button-on"><?php echo T_('Sim'); ?></div>
            <input type="checkbox" name="status" value="1" checked="checked" id="inputStatus">
        </div>
    </div>
    <div class="col-xs-12 form-group">
        <label for="inputName" class="control-label"><?php echo T_('Nome:'); ?> </label>
        <input type="text" class="form-control" name="name" id="inputName" placeholder="Nome do Grupo">
    </div>
    <!--div class="col-xs-12 col-sm-8">
        <div class="form-group">
            <label for="flags" class="control-label">Corretores: </label>
            <div class="col-xs-8 col-lg-10">
                <label for="flags" class="checkbox-custom">
                    <input type="checkbox" name="flags[agents]" value="1" id="flags">
                    <i class="far fa-fw fa-square"></i>
                </label>
            </div>
        </div>
    </div-->

    <h3 class="col-sm-12"><?php echo T_('Permissões'); ?></h3>
    <div class="col-sm-12 permission-wrapper">
        <?php $this->load->view('administracao/usuarios/permissoes', array(
            'modules' => $modules,
            'permissions' => array()
        )); ?>
    </div>
    <div class="col-separator-h"></div>
    <div class="form-group">
        <div class="col-sm-12 text-center">
            <a href="javascript:history.back(-1);" class="btn btn-default"><?php echo T_('Cancelar'); ?></a>
            <?php if (in_array('cadastrar', $session_permissions[$current_module->id])){ ?>
            <button type="submit" class="btn btn-primary"><?php echo T_('Cadastrar'); ?></button>
            <?php } ?>
        </div>
    </div>
</form>