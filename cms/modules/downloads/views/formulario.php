<form action="<?php echo site_url($current_module->slug . '/' . (isset($item) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" id="inputId" />
  <?php } ?>
  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" class="glyphicons notes" data-toggle="tab"><i></i> Dados Gerais</a></li>
  </ul>
  <div class="tab-content col-sm-12">

    <!-- Tab (Dados Gerais) -->
    <div class="tab-pane fade active in" id="tab1">
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

      <div class="form-group col-sm-12">
        <label for="selectProduct" class="control-label"><?= T_('Produtos:') ?></label>
        <select id="selectProduct" name="products[]" class="form-control select2" multiple data-placeholder="<?= T_('Produtos') ?>">
          <option></option>
          <?php foreach ($products as $product) { ?>
            <option <?= (isset($item) && in_array($product->id, $item->products)) ? 'selected="selected"' : '' ?> value="<?= $product->id ?>"><?= $product->title ?></option>
          <?php } ?>
        </select>
      </div>

      <ul class="nav nav-pills">
        <!-- Header da tab linguagem -->
        <?php foreach ($languages as $key => $language) { ?>
          <li<?php echo ($key == 0) ? ' class="active"' : ''; ?>>
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
        <!-- Body da tab linguagem -->
        <?php foreach ($languages as $key => $language) { ?>
          <div class="tab-pane<?php echo ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?php echo $key; ?>">

            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
              <label for="inputTitle<?php echo $key; ?>" class="control-label"><?= T_('Título') ?>: </label>
              <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][title]" id="inputTitle<?php echo $key; ?>" placeholder="<?= T_('Título') ?>" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->title : ''; ?>" <?php echo ($language->id == 1) ? ' required' : ''; ?>>
            </div>

            <?php
            $this->load->view('gallery/single-file', array(
              'label' => 'Arquivo',
              'typeupload' => 'archive',
              'module' => $current_module->slug,
              'file' => (isset($item->languages[$language->id]) && $item->languages[$language->id]->archive) ? $item->languages[$language->id]->archive : null,
              'id' => 'fileUploadArchive',
              'name' => 'archive',
              'key' => $key,
              'id_lang' => $language->id,
              'upload' => site_url('gallery/upload/archive'),
              'ext' => 'pdf|eps|cdr|png|jpeg|jpg|doc|docx|psd'
            ));
            ?>
          </div>
        <?php } ?>
      </div>
    </div>

  </div>

  <div class="col-sm-12 text-center">
    <a href="<?php echo site_url($current_module->slug); ?>" class="btn btn-default">Cancelar</a>
    <button type="submit" class="btn btn-primary"><?php echo isset($item) ? 'Salvar' : 'Cadastrar'; ?></button>
  </div>
</form>