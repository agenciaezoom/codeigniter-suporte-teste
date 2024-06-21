<form action="<?php echo site_url($current_module->slug .'/'. (isset($item) && !isset($copy) ? 'edit/'.$id : 'add'))?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
    <?php if (isset($item)){ ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" id="inputId" />
    <?php } ?>

    <?php if($this->auth->data('id') == '1'){ ?>
        <div class="form-group col-xs-12">
            <label class="col-xs-12 control-label">Ativo: </label>
            <div class="make-switch">
                <?php if (!isset($item) || $item->status == '1') { ?>
                    <div class="button-switch button-on">Sim</div>
                    <input type="checkbox" name="status" checked="checked" id="inputStatus">
                <?php } else { ?>
                    <div class="button-switch button-off">Não</div>
                    <input type="checkbox" name="status" id="inputStatus">
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div class="col-xs-12 col-sm-6 form-group">
        <label class="control-label" for="inputLabel"><?php echo T_('Título:'); ?> <span class="info-routes"><?php echo T_('(Utilize para descrever a rota. Apenas para organizacao.)'); ?></span></label>
        <input type="text" <?php echo $this->auth->data('id') != '1' ? 'disabled' : ''; ?> placeholder="<?php echo T_('Título'); ?>" class="form-control" name="label" value="<?php echo isset($item) ? $item->label : ''; ?>" id="inputLabel" required />
    </div>

    <?php if($this->auth->data('id') == '1'){ ?>
        <div class="col-xs-12 col-sm-6 form-group">
            <label class="control-label" for="inputURL"><?php echo T_('Url:'); ?> <span class="info-routes"><?php echo T_('(Complemento da url para parametros nao-estaticos. Ex.: (:num)/(:any))'); ?></span></label>
            <input type="text" placeholder="Url" class="form-control" name="url_complement" value="<?php echo isset($item) ? $item->url_complement : ''; ?>" id="inputURL" />
        </div>

        <div class="col-xs-12 col-sm-6 form-group">
            <label class="control-label" for="inputKey"><?php echo T_('Key:'); ?> <span class="info-routes"><?php echo T_('(Chave de array para imprimir os links nas views.)'); ?></span></label>
            <input type="text" placeholder="Chave" class="form-control" name="key" value="<?php echo isset($item) ? $item->key : ''; ?>" id="inputKey" required />
        </div>

        <div class="col-xs-12 col-sm-6 form-group">
            <label class="control-label" for="inputMethod"><?php echo T_('Método:'); ?> <span class="info-routes"><?php echo T_('(Método completo relativo a url. Ex.: produtos/detalhes/$1/$2)'); ?></span></label>
            <input type="text" placeholder="Método" class="form-control" name="method" value="<?php echo isset($item) ? $item->method : ''; ?>" id="inputMethod" required />
        </div>
    <?php } ?>

    <div class="tab-pane fade active in" id="tab1">

        <?php echo $this->load->view('comum/nav-lang', array('tabname' => 'tablang')); ?>
        <div class="tab-content">
            <?php foreach ($languages as $key => $language) { ?>
                <div class="tab-pane<?php echo ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?php echo $key; ?>">
                    <div class="col-xs-12 col-sm-6 form-group">
                        <label for="inputURL<?php echo $key; ?>" class="control-label"><?php echo T_('URL'); ?></label>
                        <input type="text" <?php echo $this->auth->data('id') != '1' ? 'disabled' : ''; ?> class="form-control" name="value[<?php echo $language->id; ?>][url]" id="inputURL<?php echo $key; ?>" placeholder="<?php echo T_('url'); ?>" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->url : ''; ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6 form-group">
                        <label for="inputTitle<?php echo $key; ?>" class="control-label"><?php echo T_('Título'); ?></label>
                        <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][seo_title]" id="inputTitle<?php echo $key; ?>" placeholder="<?php echo T_('Título'); ?>" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->seo_title : ''; ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6 form-group">
                        <label for="inputDescription<?php echo $key; ?>" class="col-xs-4 col-sm-3 col-lg-3 control-label"><?php echo T_('Description'); ?></label>
                        <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][seo_description]" id="inputDescription<?php echo $key; ?>" placeholder="<?php echo T_('Description'); ?>" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->seo_description : ''; ?>">
                    </div>
                    <div class="col-xs-12 <?php echo $this->auth->data('id') != '1' ? 'col-sm-12' : 'col-sm-6'; ?> form-group">
                        <label for="inputKeywords<?php echo $key; ?>" class="col-xs-4 col-sm-3 col-lg-3 control-label"><?php echo T_('Keywords'); ?></label>
                        <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][seo_keywords]" id="inputKeywords<?php echo $key; ?>" placeholder="<?php echo T_('Keywords'); ?>" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->seo_keywords : ''; ?>">
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

    <div class="col-sm-12 text-center">
        <a href="<?php echo site_url($current_module->slug); ?>" class="btn btn-default">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?php echo isset($item) ? 'Salvar' : 'Cadastrar'; ?></button>
    </div>
</form>
