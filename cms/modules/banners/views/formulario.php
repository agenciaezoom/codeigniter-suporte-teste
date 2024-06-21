<form action="<?= site_url($current_module->slug . '/' . (isset($item) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?= $id; ?>" id="inputId" />
  <?php   } ?>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" data-toggle="tab"><i class="fa fa-file-alt"></i> <?= T_('Dados Gerais'); ?></a></li>
  </ul>

  <div class="tab-content col-sm-12">

    <!-- Tab (Dados Gerais) -->
    <div class="tab-pane fade active in" id="tab1">
      <div class="form-group col-xs-12 col-md-1">
        <label class="col-xs-12 control-label"><?= T_('Ativo'); ?>: </label>
        <div class="make-switch">
          <?php if (!isset($item) || $item->status == '1') { ?>
            <div class="button-switch button-on"><?= T_('Sim'); ?></div>
            <input type="checkbox" name="status" checked="checked" id="inputStatus">
          <?php } else { ?>
            <div class="button-switch button-off"><?= T_('Não'); ?></div>
            <input type="checkbox" name="status" id="inputStatus">
          <?php } ?>
        </div>
      </div>
      <div class="clearfix"></div>

      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <i class="fa fa-file-alt" aria-hidden="true"></i>
            <?= T_('Conteúdo'); ?>
          </div>
          <div class="panel-body">
            <?= $this->load->view('comum/nav-lang', array('tabname' => 'tablang')); ?>
            <div class="tab-content">
              <!-- Body da tab linguagem -->
              <?php foreach ($languages as $key => $language) { ?>
                <div class="tab-pane<?= ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?= $key; ?>">
                  <div class="col-xs-12 col-md-12 col-sm-12 form-group">
                    <label for="inputTitle<?= $key; ?>" class="control-label"><?= T_('Título'); ?>: </label>
                    <textarea type="text" class="form-control inputWithCK" name="value[<?= $language->id; ?>][title]" id="inputTitle<?= $key; ?>" placeholder="<?= T_('Título'); ?>"><?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->title : ''; ?></textarea>
                  </div>

                  <div class="col-xs-12 col-md-12 col-sm-12 form-group">
                    <label for="inputSubtitle<?= $key; ?>" class="control-label"><?= T_('Subtítulo'); ?>: </label>
                    <input type="text" class="form-control" name="value[<?= $language->id; ?>][subtitle]" id="inputSubtitle<?= $key; ?>" placeholder="<?= T_('Subtítulo'); ?>" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->subtitle : ''; ?>" />
                  </div>

                  <div class="col-xs-12 col-md-6 form-group">
                    <label for="inputLinkYoutube<?= $key; ?>" class="control-label"><?= T_('Link Youtube'); ?>: </label>
                    <input type="text" class="form-control" name="value[<?= $language->id; ?>][link_youtube]" id="inputLinkYoutube<?= $key; ?>" placeholder="<?= T_('Link Youtube'); ?>" value="<?= (isset($item->languages[$language->id]->link_youtube)) ? $item->languages[$language->id]->link_youtube : ''; ?>">
                  </div>

                  <div class="col-xs-12 col-md-6 form-group">
                    <label for="inputLinkYoutubeMobile<?= $key; ?>" class="control-label"><?= T_('Link Youtube - Mobile'); ?>: </label>
                    <input type="text" class="form-control" name="value[<?= $language->id; ?>][link_youtube_mobile]" id="inputLinkYoutubeMobile<?= $key; ?>" placeholder="<?= T_('Link Youtube - Mobile'); ?>" value="<?= (isset($item->languages[$language->id]->link_youtube_mobile)) ? $item->languages[$language->id]->link_youtube_mobile : ''; ?>">
                  </div>

                  <div class="col-xs-12 col-md-10 form-group">
                    <label for="inputLink<?= $key; ?>" class="control-label"><?= T_('Link'); ?>: </label>
                    <input type="text" class="form-control" name="value[<?= $language->id; ?>][link]" id="inputLink<?= $key; ?>" placeholder="<?= T_('Link'); ?>" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->link : ''; ?>">
                  </div>

                  <div class="col-xs-12 col-sm-5 col-md-2 form-group">
                    <label for="inputType<?= $key; ?>" class="control-label">Abrir Link</label>
                    <select name="value[<?= $language->id; ?>][target]" id="inputType<?= $key; ?>" class="form-control select2 required">
                      <option value="_self" <?= (!isset($item->languages[$language->id]) || $item->languages[$language->id]->target == '_self') ? 'selected' : ''; ?>>Mesma Janela</option>
                      <option value="_blank" <?= (isset($item->languages[$language->id]) && $item->languages[$language->id]->target == '_blank') ? 'selected' : ''; ?>>Nova Janela</option>
                    </select>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <i class="fa fa-images" aria-hidden="true"></i>
            <?= T_('Imagens'); ?>
          </div>
          <div class="panel-body">
            <?php
            $this->load->view('gallery/single-file', array(
              'label' => 'Imagem',
              'typeupload' => null,
              'module' => $current_module->slug,
              'file' => (isset($item->image) && $item->image) ? $item->image : null,
              'resize' => TRUE,
              'dimensions' => array(
                'w' => 1920,
                'h' => 1080
              ),
              'id' => 'fileuploadImage-primary',
              'name' => 'image',
              'key' => 1,
              // 'id_lang' => 1,
              'upload' => site_url('gallery/upload/image'),
              'ext' => 'jpge|jpg|png',
              'colSize' => 6
            ));
            ?>

            <?php
            $this->load->view('gallery/single-file', array(
              'label' => 'Imagem (mobile)',
              'typeupload' => null,
              'module' => $current_module->slug,
              'file' => (isset($item->image_mobile) && $item->image_mobile) ? $item->image_mobile : null,
              'resize' => TRUE,
              'dimensions' => array(
                'w' => 480,
                'h' => 720
              ),
              'id' => 'fileuploadImage-secondary',
              'name' => 'image_mobile',
              'key' => 2,
              // 'id_lang' => 1,
              'upload' => site_url('gallery/upload/image'),
              'ext' => 'jpge|jpg|png',
              'colSize' => 6
            ));
            ?>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="col-sm-12 text-center">
    <a href="<?= site_url($current_module->slug); ?>" class="btn btn-default"><?= T_('Cancelar'); ?></a>
    <button type="submit" class="btn btn-primary"><?= isset($item) ? T_('Salvar') : T_('Cadastrar'); ?></button>
  </div>
</form>