<form action="<?php echo site_url($current_module->slug . '/' . (isset($item) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" id="inputId" />
  <?php   } ?>

  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" class="glyphicons notes" data-toggle="tab"><i></i> <?php echo T_('Dados Gerais'); ?></a></li>
  </ul>

  <div class="tab-content col-sm-12">
    <div class="tab-pane fade active in" id="tab1">

      <div class="form-group col-sm-12">
        <label class="col-xs-12 control-label"><?php echo T_('Ativo'); ?>: </label>
        <div class="make-switch">
          <?php if (!isset($item) || $item->status == '1') { ?>
            <div class="button-switch button-on"><?php echo T_('Sim'); ?></div>
            <input type="checkbox" name="status" checked="checked" id="inputStatus">
          <?php } else { ?>
            <div class="button-switch button-off"><?php echo T_('Não'); ?></div>
            <input type="checkbox" name="status" id="inputStatus">
          <?php } ?>
        </div>
      </div>

      <ul class="nav nav-pills">
        <?php foreach ($languages as $key => $language) { ?>
          <li <?php echo ($key == 0) ? 'class="active"' : ''; ?>>
            <a href="#tablang<?php echo $key; ?>" data-toggle="tab">
              <i class="lang-flag">
                <img src="<?php echo base_img($language->image); ?>" alt="<?php echo $language->name; ?>">
              </i>
              <?php echo $language->name; ?>
            </a>
          </li>
        <?php } ?>
      </ul>

      <div class="tab-content">
        <?php foreach ($languages as $key => $language) { ?>
          <div class="tab-pane<?php echo ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?php echo $key; ?>">
            <div class="col-sm-12 form-group">
              <label for="inputTitle<?php echo $key; ?>" class="control-label"><?php echo T_('Título:'); ?> </label>
              <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][title]" id="inputTitle<?php echo $key; ?>" placeholder="<?php echo T_('Título'); ?>" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->title : ''; ?>" <?php echo ($language->id == 1) ? ' required' : ''; ?>>
            </div>
          </div>
        <?php } ?>
      </div>

      <?php $this->load->view('gallery/single-file', array(
        'label' => T_('Imagem'),
        'typeupload' => null,
        'module' => $current_module->slug,
        'file' => (isset($item) && $item->image) ? $item->image : null,
        'resize' => TRUE,
        'dimensions' => array(
          'w' => 720,
          'h' => 300
        ),
        'id' => 'fileuploadImage-primary',
        'name' => 'image',
        'key' => 1,
        'id_lang' => null,
        'upload' => site_url('gallery/upload/image'),
        'ext' => 'jpg|jpeg|png'
      )); ?>

    </div>
  </div>

  <div class="col-sm-12 text-center">
    <a href="<?php echo site_url($current_module->slug); ?>" class="btn btn-default"><?php echo T_('Cancelar'); ?></a>
    <button type="submit" class="btn btn-primary"><?php echo isset($item) ? T_('Salvar') : T_('Cadastrar'); ?></button>
  </div>
</form>