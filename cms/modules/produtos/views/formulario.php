<form action="<?= site_url($current_module->slug . '/' . (isset($item) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?= $id; ?>" id="inputId" />
  <?php   } ?>

  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" class="glyphicons notes" data-toggle="tab"><i></i> <?= T_('Dados Gerais'); ?></a></li>
    <li><a href="#tab5" class="glyphicons notes" data-toggle="tab"><i></i> <?= T_('Informações Técnicas'); ?></a></li>
    <li><a href="#tab3" class="glyphicons notes" data-toggle="tab"><i></i> <?= T_('SEO'); ?></a></li>
  </ul>

  <div class="tab-content col-sm-12">

    <!-- Tab (Dados Gerais) -->
    <div class="tab-pane fade active in" id="tab1">
      <input type="hidden" name="to_product" value="1">
      <div class="form-group col-sm-12">
        <label class="col-sm-12 control-label"><?= T_('Ativo:'); ?> </label>
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

      <?= $this->load->view('comum/nav-lang', array('tabname' => 'tablang')); ?>
      <div class="tab-content">
        <!-- Body da tab linguagem -->
        <?php foreach ($languages as $key => $language) { ?>
          <div class="tab-pane<?= ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?= $key; ?>" data-lang="<?= $language->id; ?>">

            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
              <label for="inputTitle<?= $key; ?>" class="control-label"><?= T_('Título'); ?>: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][title]" id="inputTitle<?= $key; ?>" placeholder="<?= T_('Título'); ?>" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->title : ''; ?>" <?= ($language->id == 1) ? ' required' : ''; ?>>
            </div>

            <div class="form-group col-xs-12">
              <label for="attributes" class="control-label"><?= T_('Atributos:') ?></label>
              <select id="attributes" name="attributes[]" class="form-control select2" data-placeholder="<?= T_('Atributos') ?>" multiple>
                <option></option>
                <?php foreach ($attributes as $each) { ?>
                  <option value="<?= $each->id; ?>" <?= isset($item) && in_array($each->id, $item->attributes) ? 'selected' : '' ?>><?= $each->title; ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group col-xs-12">
              <label for="selectType" class="control-label"><?= T_('Tipo de Telha:') ?></label>
              <select id="selectType" name="type" class="form-control select2" data-placeholder="<?= T_('Tipo de Telha') ?>" required>
                <option></option>
                <option value="4mm" <?= isset($item) && $item->type == '4mm' ? ' selected' : ''; ?>>4mm</option>
                <option value="5mm" <?= isset($item) && $item->type == '5mm' ? ' selected' : ''; ?>>5mm</option>
                <option value="6mm" <?= isset($item) && $item->type == '6mm' ? ' selected' : ''; ?>>6mm</option>
                <option value="8mm" <?= isset($item) && $item->type == '8mm' ? ' selected' : ''; ?>>8mm</option>
              </select>
            </div>

            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
              <label for="inputSubtitle<?= $key; ?>" class="control-label"><?= T_('Subtítulo'); ?>: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][subtitle]" id="inputSubtitle<?= $key; ?>" placeholder="<?= T_('Subtítulo'); ?>" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->subtitle : ''; ?>">
            </div>

            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
              <label for="inputDescription<?= $key; ?>" class="control-label"><?= T_('Descrição'); ?>: </label>
              <textarea id="inputDescription<?= $key; ?>" name="value[<?= $language->id; ?>][text]" class="form-control ckeditor" style="height: 320px;" rows="5"><?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->text : ''; ?></textarea>
            </div>

            <?php $this->load->view('gallery/single-file', array(
              'label' => 'Imagem',
              'typeupload' => null,
              'module' => $current_module->slug,
              'file' => (isset($item->languages[$language->id]) && $item->languages[$language->id]->image) ? $item->languages[$language->id]->image : null,
              'dimensions' => array(
                'w' => 480,
                'h' => 480
              ),
              'resize' => 'false',
              'id' => 'fileUploadImage',
              'name' => 'image',
              'key' => $key,
              'id_lang' => $language->id,
              'upload' => site_url('gallery/upload/image'),
              'ext' => 'jpg|png'
            )); ?>

            <?php $this->load->view('gallery/single-file', array(
              'label' => 'Imagem de Fundo',
              'typeupload' => null,
              'module' => $current_module->slug,
              'file' => (isset($item->languages[$language->id]) && $item->languages[$language->id]->background_image) ? $item->languages[$language->id]->background_image : null,
              'dimensions' => array(
                'w' => 2000,
                'h' => 757
              ),
              'resize' => 'false',
              'id' => 'fileUploadImage-secondary',
              'name' => 'background_image',
              'key' => $key,
              'id_lang' => $language->id,
              'upload' => site_url('gallery/upload/image'),
              'ext' => 'jpg|png'
            )); ?>
          </div>
        <?php
        }
        ?>
      </div>
    </div>

    <!-- Tab Informações -->
    <div class="tab-pane fade" id="tab5">
      <div class="col-xs-12">
        <?php
        $this->load->view('infos', array(
          'infos' => isset($item) ? $item->infos : false
        ));
        ?>
      </div>
    </div>

    <!-- Tab (SEO) -->
    <div class="tab-pane fade" id="tab3">
      <ul class="nav nav-pills">
        <!-- Header da tab linguagem -->
        <?php foreach ($languages as $key => $language) { ?>
          <li<?= ($key == 0) ? ' class="active"' : ''; ?>>
            <a href="#tabseolang<?= $key; ?>" data-toggle="tab">
              <i class="lang-flag">
                <img src="<?= base_img($language->image); ?>" alt="<?= $language->name; ?>">
              </i>
              <?= $language->name; ?>
            </a>
            </li>
          <?php } ?>
      </ul>
      <div class="tab-content">
        <!-- Body da tab linguagem -->
        <?php foreach ($languages as $key => $language) { ?>
          <div class="tab-pane<?= ($key == 0) ? ' active in ' : ''; ?> fade" id="tabseolang<?= $key; ?>">
            <div class="col-xs-12 col-sm-12 form-group">
              <label for="inputTitle<?= $key; ?>" class="control-label"><?= T_('Meta Title'); ?>: </label>
              <input type="text" id="inputTitle<?= $key; ?>" name="value[<?= $language->id; ?>][meta_title]" class="form-control" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_title : ''; ?>" />
            </div>
            <div class="col-xs-12 col-sm-12 form-group">
              <label for="inputKeywords<?= $key; ?>" class="control-label"><?= T_('Meta Keywords'); ?>: </label>
              <input type="text" id="inputKeywords<?= $key; ?>" name="value[<?= $language->id; ?>][meta_keywords]" class="form-control" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_keywords : ''; ?>" />
            </div>
            <div class="col-xs-12 col-sm-12 form-group">
              <label for="inputDescription<?= $key; ?>" class="control-label"><?= T_('Meta Description'); ?>: </label>
              <input type="text" id="inputDescription<?= $key; ?>" name="value[<?= $language->id; ?>][meta_description]" class="form-control" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_description : ''; ?>" />
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

  </div>

  <div class="col-sm-12 text-center">
    <a href="<?= site_url($current_module->slug); ?>" class="btn btn-default">Cancelar</a>
    <button type="submit" class="btn btn-primary"><?= isset($item) ? 'Salvar' : 'Cadastrar'; ?></button>
  </div>
</form>