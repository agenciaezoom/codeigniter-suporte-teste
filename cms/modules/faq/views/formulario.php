<form action="<?= site_url($current_module->slug . '/' . (isset($item) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?= $id; ?>" id="inputId" />
  <?php   } ?>

  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" class="glyphicons notes" data-toggle="tab"><i></i> <?= T_('Dados Gerais'); ?></a></li>
  </ul>

  <div class="tab-content col-sm-12">

    <!-- Tab (Dados Gerais) -->
    <div class="tab-pane fade active in" id="tab1">
      <div class="form-group col-sm-12">
        <label class="col-xs-12 control-label"><?= T_('Ativo:'); ?> </label>
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

      <ul class="nav nav-pills">
        <!-- Header da tab linguagem -->
        <?php foreach ($languages as $key => $language) { ?>
          <li<?= ($key == 0) ? ' class="active"' : ''; ?>>
            <a href="#tablang<?= $key; ?>" data-toggle="tab">
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
          <div class="tab-pane<?= ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?= $key; ?>">
            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
              <label for="inputTitle<?= $key; ?>" class="control-label"><?= T_('Pergunta'); ?>: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][title]" id="inputTitle<?= $key; ?>" placeholder="<?= T_('Pergunta'); ?>" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->title : ''; ?>" <?= ($language->id == 1) ? ' required' : ''; ?>>
            </div>

            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
              <label for="inputDescription<?= $key; ?>" class="control-label"><?= T_('Resposta'); ?>: </label>
              <textarea id="inputDescription<?= $key; ?>" name="value[<?= $language->id; ?>][text]" class="form-control ckeditor" style="height: 320px;" rows="5"><?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->text : ''; ?></textarea>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    </div>

    <div class="col-sm-12 text-center">
      <a href="<?= site_url($current_module->slug); ?>" class="btn btn-default">Cancelar</a>
      <button type="submit" class="btn btn-primary"><?= isset($item) ? 'Salvar' : 'Cadastrar'; ?></button>
    </div>
</form>