<form action="<?php echo site_url($current_module->slug . '/' . (isset($item) ? 'edit/' . $id : 'add')) ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" id="inputId" />
  <?php } ?>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" data-toggle="tab"><i class="fa fa-file-alt"></i> <?php echo T_('Dados Gerais'); ?></a></li>
    <?php if (!isset($permission) || (!empty($permission->enable_gallery) && $permission->enable_gallery == 'enabled')) { ?>
      <li><a href="#tab2" data-toggle="tab"><i class="fa fa-images"></i> <?php echo T_('Galeria de Imagens'); ?></a></li>
    <?php }
    if (!isset($permission) || (!empty($permission->enable_videos) && $permission->enable_videos == 'enabled')) { ?>
      <li><a href="#tab3" data-toggle="tab"><i class="fa fa-play"></i> <?php echo T_('Galeria de Vídeos'); ?></a></li>
    <?php } ?>
  </ul>

  <div class="tab-content col-sm-12">

    <!-- Tab Dados Gerais -->
    <div class="tab-pane fade active in" id="tab1">

      <div class="col-xs-12 col-sm-12 form-group">
        <label class="col-sm-12 control-label">Ativo: </label>
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

      <?php if (!isset($item) || (!empty($permission->enable_area) && $permission->enable_area == 'enabled')) { ?>
        <div class="col-xs-12 col-sm-4 form-group">
          <label class="control-label">Área: </label>
          <input type="text" placeholder="Área" class="form-control" name="area" value="<?php echo isset($item->languages[1]) ? $item->languages[1]->area : ''; ?>" id="inputArea" <?php echo $this->auth->data('admin') != 1 ? 'readonly' : ''; ?> />
        </div>
      <?php }

      if (!isset($item) || (!empty($permission->enable_subarea) && $permission->enable_subarea == 'enabled')) { ?>
        <div class="col-xs-12 col-sm-4 form-group">
          <label class="control-label">Subarea: </label>
          <input placeholder="Subárea" type="text" class="form-control" name="subarea" value="<?php echo isset($item->languages[1]) ? $item->languages[1]->subarea : ''; ?>" id="inputSubarea" <?php echo $this->auth->data('admin') != 1 ? 'readonly' : ''; ?> />
        </div>
      <?php }

      if ($this->auth->data('id') == 1) { ?>
        <div class="col-xs-12 col-sm-4 form-group">
          <label class="control-label">Slug: </label>
          <input type="text" class="form-control" placeholder="Slug" name="slug" value="<?php echo isset($item) ? $item->slug : ''; ?>" required />
        </div>
      <?php
      }
      ?>

      <div class="tab-pane fade active in" id="tabLang">
        <ul class="nav nav-pills">
          <!-- Header da tab linguagem -->
          <?php
          foreach ($languages as $key => $language) {
          ?>
            <li<?php echo ($key == 0) ? ' class="active"' : ''; ?>>
              <a href="#tablang<?php echo $key; ?>" data-toggle="tab">
                <i class="lang-flag">
                  <img src="<?php echo base_img($language->image); ?>" alt="<?php echo $language->name; ?>">
                </i>
                <?php echo $language->name; ?>
              </a>
              </li>
            <?php
          }
            ?>
        </ul>
        <div class="tab-content">
          <!-- Body da tab linguagem -->
          <?php
          foreach ($languages as $key => $language) {
          ?>
            <div class="tab-pane<?php echo ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?php echo $key; ?>">
              <?php
              if (!isset($item) || (!empty($permission->enable_title) && $permission->enable_title == 'enabled')) {
              ?>
                <div class="form-group col-xs-12">
                  <label for="inputTitle<?php echo $key; ?>" class="control-label">Título: </label>
                  <textarea type="text" class="form-control title-height inputWithCK <?php echo (!isset($item) || (!empty($permission->enable_title_bold) && $permission->enable_title_bold == 'enabled')) ? 'inputWithCK' : '' ?>" name="value[<?php echo $language->id; ?>][title]" id="inputTitle<?php echo $key; ?>" <?php echo ($language->id == 1) ? ' required' : ''; ?>><?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->title : ''; ?></textarea>
                </div>
              <?php
              }
              if (!isset($item) || (!empty($permission->enable_subtitle) && $permission->enable_subtitle == 'enabled')) {
              ?>
                <div class="form-group col-xs-12">
                  <label for="inputSubtitle<?php echo $key; ?>" class="control-label">Subtítulo: </label>
                  <textarea type="text" class="form-control title-height inputWithCK <?php echo (!isset($item) || (!empty($permission->enable_subtitle_bold) && $permission->enable_subtitle_bold == 'enabled')) ? 'inputWithCK' : '' ?>" name="value[<?php echo $language->id; ?>][subtitle]" id="inputSubtitle<?php echo $key; ?>"><?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->subtitle : ''; ?></textarea>
                </div>
              <?php
              }
              if (!isset($item) || (!empty($permission->enable_text) && $permission->enable_text == 'enabled')) {
              ?>
                <div class="form-group col-xs-12">
                  <label for="inputDescription<?php echo $key; ?>" class="control-label">Texto: </label>
                  <textarea id="inputDescription<?php echo $key; ?>" name="value[<?php echo $language->id; ?>][text]" class="form-control ckeditor" style="height: 320px;" rows="5"><?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->text : ''; ?></textarea>
                </div>
              <?php
              }
              if (!isset($item) || (!empty($permission->enable_youtube_id) && $permission->enable_youtube_id == 'enabled')) {
              ?>
                <div class="form-group col-xs-12">
                  <label for="inputLinkLabel<?php echo $key; ?>" class="control-label">Youtube ID: </label>
                  <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][youtube_id]" id="inputLinkLabel<?php echo $key; ?>" placeholder="ID do Youtube" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->youtube_id : ''; ?>" />
                </div>
              <?php
              }
              if (!isset($item) || (!empty($permission->enable_link) && $permission->enable_link == 'enabled')) {
              ?>
                <div class="form-group col-xs-10">
                  <label for="inputLink<?php echo $key; ?>" class="control-label">Link: </label>
                  <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][link]" id="inputLink<?php echo $key; ?>" placeholder="Link" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->link : ''; ?>" />
                </div>

                <div class="col-xs-12 col-sm-5 col-md-2 form-group">
                  <label for="inputType<?php echo $key; ?>" class="control-label">Abrir Link</label>
                  <select name="value[<?php echo $language->id; ?>][target]" id="inputType<?php echo $key; ?>" class="form-control select2 required">
                    <option value="_self" <?php echo (!isset($item->languages[$language->id]) || $item->languages[$language->id]->target == '_self') ? 'selected' : ''; ?>>Mesma Janela</option>
                    <option value="_blank" <?php echo (isset($item->languages[$language->id]) && $item->languages[$language->id]->target == '_blank') ? 'selected' : ''; ?>>Nova Janela</option>
                  </select>
                </div>

              <?php
              }
              if (!isset($item) || (!empty($permission->enable_link_label) && $permission->enable_link_label == 'enabled')) {
              ?>
                <div class="form-group col-xs-12">
                  <label for="inputLinkLabel<?php echo $key; ?>" class="control-label">Link Label: </label>
                  <input type="text" class="form-control" name="value[<?php echo $language->id; ?>][link_label]" id="inputLinkLabel<?php echo $key; ?>" placeholder="Texto do Link" value="<?php echo (isset($item->languages[$language->id])) ? $item->languages[$language->id]->link_label : ''; ?>" />
                </div>
              <?php
              }
              if (!isset($item) || (!empty($permission->enable_archive) && $permission->enable_archive == 'enabled')) {
                $this->load->view('gallery/single-file', array(
                  'label' => 'Arquivo',
                  'typeupload' => 'archive',
                  'module' => $current_module->slug,
                  'file' => (isset($item->archive_name) && !empty($item->archive_name)) ? $item->archive_name : null,
                  'id' => 'fileUploadArchive',
                  'name' => 'archive',
                  'key' => $key,
                  'id_lang' => $language->id,
                  'upload' => site_url('gallery/upload/archive'),
                  'ext' => 'doc|docx|pdf',
                ));
              } ?>
              <div class="col-xs-12 col-sm-5 col-md-2 form-group">
                <?php if (!isset($item) || (!empty($permission->enable_image) && $permission->enable_image == 'enabled')) {
                  $this->load->view('gallery/single-file', array(
                    'label' => 'Imagem',
                    'typeupload' => null,
                    'module' => $current_module->slug,
                    'file' => (isset($item->languages[$language->id]) && $item->languages[$language->id]->image) ? $item->languages[$language->id]->image : null,
                    'dimensions' => array(
                      'w' => (isset($item)) ? $item->image_width : 1920,
                      'h' => (isset($item)) ? $item->image_height : 1080
                    ),
                    'resize' => 'false',
                    'id' => 'fileUploadImage',
                    'name' => 'image',
                    'key' => $key,
                    'id_lang' => $language->id,
                    'upload' => site_url('gallery/upload/image'),
                    'ext' => 'jpg|png'
                  ));
                } ?>
              </div>
              <div class="col-xs-12 col-sm-5 col-md-2 form-group">
                <?php /* if (!isset($item) || (!empty($permission->enable_image_mobile) && $permission->enable_image_mobile == 'enabled')) {
                  $this->load->view('gallery/single-file', array(
                    'label' => 'Imagem Mobile',
                    'typeupload' => null,
                    'module' => $current_module->slug,
                    'file' => (isset($item->languages[$language->id]) && $item->languages[$language->id]->image_mobile) ? $item->languages[$language->id]->image_mobile : null,
                    'dimensions' => array(
                      'w' => (isset($item)) ? $item->image_mobile_width : 1920,
                      'h' => (isset($item)) ? $item->image_mobile_height : 1080
                    ),
                    'resize' => 'false',
                    'id' => 'fileUploadImage',
                    'name' => 'image_mobile',
                    'key' => $key + 1,
                    'id_lang' => $language->id,
                    'upload' => site_url('gallery/upload/image'),
                    'ext' => 'jpg|png'
                  ));
                } */ ?>
              </div>
            </div>
          <?php
          } ?>
        </div>
      </div>

    </div>

    <!-- Tab Imagens -->
    <?php if (!isset($item) || (!empty($permission->enable_gallery) && $permission->enable_gallery == 'enabled')) { ?>
      <div class="tab-pane fade" id="tab2">
        <?php
        $this->load->view(
          'gallery/images',
          array(
            'label'         => 'Galeria de Imagens',
            'images'        => isset($item) ? $item->images : array(),
            'gallerytable'  => 'site_common_content_gallery',
            'path'          => 'userfiles/paginas/',
            'resize'        => 'true',
            'width'         => '1920',
            'height'        => '1000',
            'fit'           => 'inside',
            'typeGallery'   => null,
            //'multilang'     => true
          )
        );
        ?>
      </div>
    <?php } ?>

    <?php if (!isset($item) || (!empty($permission->enable_videos) && $permission->enable_videos == 'enabled')) { ?>
      <!-- Tab Vídeos -->
      <div class="tab-pane fade" id="tab3">
        <?php
        $this->load->view('comum/videos', array(
          'videos' => isset($item) ? $item->videos : false
        ));
        ?>
      </div>
    <?php } ?>

  </div>

  <div class="col-sm-12 text-center">
    <button type="submit" class="btn btn-primary"><?php echo T_('Salvar'); ?></button>
  </div>
</form>