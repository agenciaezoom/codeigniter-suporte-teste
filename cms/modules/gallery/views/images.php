<div class="col-xs-12 gallery-images <?php echo (isset($chunk) && $chunk == true) ? 'chunk' : ''; ?>">
  <?php if (isset($label)) { ?>
    <h4 class="heading"><?php echo $label; ?></h4>
  <?php
  }
  if (!isset($viewUnit)) { ?>
    <div class="imageInputs"></div>
    <div class="fileupload-buttonbar">
      <div class="file-controllers">
        <span class="btn btn-success fileinput-button" data-type="images">
          <i class="glyphicon glyphicon-plus"></i>
          <span><?php echo T_('Adicionar Imagens'); ?></span>
          <input type="file" name="<?php echo isset($typeGallery) && !is_null($typeGallery) ? $typeGallery : 'images[]'; ?>" class="fileUploadImage" multiple>
        </span>
        <button type="reset" class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span><?php echo T_('Cancelar upload'); ?></span>
        </button>
        <span class="fileupload-process"></span>
      </div>
    </div>

    <?php if (isset($images) && count($images) > 0) { ?>

      <button type="reset" class="btn btn-warning delete-all">
        <i class="glyphicon glyphicon-trash"></i>
        <span><?php echo T_('Excluir Selecionadas'); ?></span>
      </button>
      <input type="hidden" id="deleteImages">
      <label for="inputPhotoDeleteAll" class="checkbox-custom">
        <i class="far fa-fw fa-square"></i>
        <?php echo T_('Selecionar todos'); ?>
        <input type="checkbox" name="inputPhotoDeleteAll" value="1" id="inputPhotoDeleteAll" class="deleteAll">
      </label>
    <?php } ?>
    <?php echo (isset($arquivos) && $arquivos) ? "<input type='hidden' id='upload_files'>" : '' ?>
  <?php }
  $newTypeGallery = str_replace('[]', '', isset($typeGallery) && !is_null($typeGallery)  ? $typeGallery : 'images'); ?>
  <div class="imageOlds imagesAll deleteIndividual <?php echo isset($multilang) && $multilang && is_array($languages) ? ' multilang ' : '';
                                                    echo $newTypeGallery;
                                                    echo (isset($viewUnit) && $viewUnit) ? ' only-view ' : ''; ?>" data-resize="<?php echo isset($resize) ? $resize : 'FALSE'; ?>" data-width="<?php echo isset($width) ? $width : '2000'; ?>" data-height="<?php echo isset($height) ? $height : '1000'; ?>" data-fit="<?php echo isset($fit) ? $fit : 'outside'; ?>" data-gallerytype="<?php echo $newTypeGallery; ?>" data-gallerytable="<?php echo isset($gallerytable) ? $gallerytable : 'gallery'; ?>" data-gallerypath="<?php echo isset($path) ? $path : '../userfiles/galeria/'; ?>" data-multilang="<?php echo isset($multilang) && $multilang && is_array($languages) ? 1 : 0; ?>" <?php echo (isset($chunk) && $chunk == true) ? 'data-gallerychunk="true"' : '' ?> <?php echo (isset($chunk) && $chunk == true) ? 'data-galleryname="videos"' : '' ?>>
    <?php
    if (isset($images) && count($images) > 0) {
      foreach ($images as $key => $row) { ?>
        <div class="template-upload fade in old" data-id="<?php echo $row->id; ?>">
          <?php if (!isset($viewUnit)) { ?>
            <label for="inputPhotoDelete<?php echo $row->id; ?>" class="checkbox-custom checkbox-delete-multiple">
              <i class="far fa-fw fa-square"></i>
              <?php echo T_('Selecione para Excluir'); ?>
              <input type="checkbox" name="inputPhotoDelete<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" id="inputPhotoDelete<?php echo $row->id; ?>">
            </label>
          <?php } ?>
          <span class="preview">
            <div class="highlighted <?php echo ($row->highlighted == 1) ? ' checked ' : ''; ?>"><input <?php echo ($row->highlighted == 1) ? ' checked ' : ' '; ?>type="checkbox" name="<?php echo 'oldImages' . $newTypeGallery . '[' . $row->id . '][highlighted]'; ?>" /></div>
            <a class="chamaModal config-link crop-image" href="#modal-crop" data-image="userfiles/<?php echo $path . '/' . $row->file; ?>" data-imagesite="<?php echo '../' . $path . '/' . $row->file; ?>" data-toggle="modal"><i class="fa fa-crop"></i></a>
            <?php
            $split = explode('.', $row->file);
            $ext = array_pop($split);
            if (isset($viewUnit) && $viewUnit) { ?>
              <a class="magnific-photo" rel="imagesUnitView" href="<?php echo site_url($path . $row->file); ?>" title="<?php echo isset($row->subtitle) ? $row->subtitle : ''; ?>">
                <?php if (in_array($ext, array('jpg', 'jpeg', 'png'))) { ?>
                  <img src="<?php echo site_url('image/resize_crop?src=../' . $path . $row->file . '&w=170&h=170'); ?>" alt="">
                <?php } else { ?>
                  <div class="file_input"><a href="<?php echo site_url('download/' . $arquivos . '/' . $row->file); ?>" class="btn btn-block btn-default"><?php echo strtoupper(array_pop(explode('.', $row->file))); ?> <span class="glyphicon glyphicon-save"></span></a></div>
                <?php } ?>
              </a>
              <?php } else {
              if (in_array($ext, array('jpg', 'jpeg', 'png'))) { ?>
                <img src="<?php echo site_url('image/resize_crop?src=../' . $path . $row->file . '&w=170&h=170'); ?>" alt="">
              <?php } elseif (in_array($ext, array('mp4', 'mov'))) { ?>
                <video src="<?php echo site_url('../' . $path . $row->file); ?>" controls></video>
              <?php } else { ?>
                <div class="file_input"><a href="<?php echo site_url('download/' . $arquivos . '/' . $row->file); ?>" class="btn btn-block btn-default"><?php echo strtoupper(array_pop(explode('.', $row->file))); ?> <span class="glyphicon glyphicon-save"></span></a></div>

              <?php } ?>
            <?php } ?>
          </span>
          <div class="separator"></div>
          <?php if (is_array($languages) && isset($multilang) && $multilang) {
            foreach ($languages as $key => $language) {
              $current = $lang != $language->code ? ' hide' : ''; ?>
              <p class="name language<?php echo $language->id . $current; ?>"><input type="text" name="<?php echo 'oldImages' . $newTypeGallery . '[' . $row->id . '][subtitle][' . $language->id . ']'; ?>" class="form-control" placeholder="Legenda" value="<?php echo isset($row->languages[$key]->subtitle) ? $row->languages[$key]->subtitle : ''; ?>" <?php echo (isset($viewUnit) && $viewUnit) ? ' readonly' : ''; ?> /></p>
            <?php } ?>
            <select name="<?php echo 'oldImages' . $newTypeGallery . '[' . $row->id . ']'; ?>[id_language]" class="form-control change-language">
              <?php foreach ($languages as $key => $language) { ?>
                <option <?php echo $lang == $language->code ? ' selected="selected"' : ''; ?> data-image="<?php echo $language->image; ?>" value="<?php echo $language->id; ?>"><?php echo strtoupper($language->code); ?></option>
              <?php } ?>
            </select>
          <?php } else { ?>
            <p class="name">
              <input type="<?= isset($item) && $item->id == 93 ? 'hidden' : 'text' ?>" name="<?php echo 'oldImages' . $newTypeGallery . '[' . $row->id . '][subtitle]'; ?>" class="form-control" placeholder="Legenda" value="<?php echo isset($row->subtitle) ? $row->subtitle : ''; ?>" <?php echo (isset($viewUnit) && $viewUnit) ? ' readonly' : ''; ?> />
            </p>
          <?php } ?>
          <input type="hidden" name="<?php echo 'oldImages' . $newTypeGallery . '[' . $row->id . '][table]'; ?>" value="<?php echo isset($gallerytable) ? $gallerytable : 'gallery'; ?>" />
          <input type="hidden" class="image-order" name="<?php echo 'oldImages' . $newTypeGallery . '[' . $row->id . '][order_by]'; ?>" value="<?php echo isset($row->order_by) ? $row->order_by : ''; ?>" />
          <?php if (!isset($viewUnit)) { ?>
            <button type="button" class="btn btn-danger btn-stroke delete">
              <i class="glyphicon glyphicon-trash"></i>
            </button>
          <?php } ?>
          <strong class="error text-danger"></strong>
        </div>
    <?php
      }
    } ?>
  </div>
  <?php if (!isset($viewUnit)) { ?>
    <div class="col-sm-12 sort-images-gallery">
      <p class="sort-info loading"><?php echo T_('* Arraste as imagens para alterar a ordem de exibição.'); ?><i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i></p>
    </div>
  <?php } ?>
</div>
<div class="clearfix"></div>
<div class="separator"></div>