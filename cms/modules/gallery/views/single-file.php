<?php

//PARAMETROS QUE PODEM SER ENVIADOS
/*
    'label' => T_('Imagem'),
    'typeupload' => null,
    'module' => $module,
    'file' => (isset($item->image) && $item->image) ? $item->image : null,
    'dimensions' => array(
        'w' => 2000,
        'h' => 757
    ),
    'id' => 'fileuploadImage-primary',
    'name' => 'image',
    'key' => 1,
    'lang' => 1,
    'upload' => site_url('gallery/upload/image')
    colSize => 12,
    'chunk' => false
    */

if (isset($typeupload) && $typeupload == 'video') { ?>
  <div class="col-xs-12 col-sm-<?php echo isset($colSize) && $colSize ? $colSize : 12; ?> form-group" id="media-<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>">
    <label for="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" class="control-label"><?php echo $label; ?>: </label>
    <div class="upload-wrapper" data-resize="<?php echo isset($resize) ? $resize : false; ?>" data-width="???" data-height="???">
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
          <span class="sr-only">0%</span>
        </div>
      </div>
      <?php if ($file) { ?>
        <a data-dim="Vídeo" href="<?php echo site_url('../userfiles/' . $module . '/' . $file); ?>" class="upload-image <?php echo $name; ?> magnific">
          <video src="<?php echo site_url('../userfiles/' . $module . '/' . $file); ?>" class="img-original" controls=false></video>
        </a>
      <?php } else { ?>
        <a href="javascript:void(0);" data-dim="Vídeo" class="upload-image <?php echo $name; ?>">
          <span>???x???</span>
        </a>
      <?php } ?>
      <div class="upload-action">
        <label class="edit-upload" for="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>"></label>
        <a href="javascript:void(0);" class="remove-image <?php echo ($file) ? '' : ' hide '; ?>"><i class="fa fa-trash"></i></a>
      </div>

      <input class="fileuploadVideo <?php echo (isset($chunk) && $chunk) ? 'chunk' : '' ?>" id="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" data-id="media-<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" type="file" name="file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" data-ext="<?php echo isset($ext) ? $ext : ''; ?>" data-imgtype="<?php echo $name; ?>" data-url="<?php echo site_url('gallery/upload'); ?>">

      <input type="checkbox" name="delete-file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" <?php echo isset($file) && $file ? ' ' : ' checked '; ?> />
    </div>
  </div>
<?php } elseif (isset($typeupload) && $typeupload == 'archive') { ?>
  <div class="col-xs-12 col-sm-<?php echo isset($colSize) && $colSize ? $colSize : 12; ?> form-group upload-wrapper">
    <label for="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" class="control-label alinhamento"><?php echo $label . ':'; ?></label>
    <div class="upload-archive upload-archive-<?php echo $id_lang; ?>">
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
          <span class="sr-only">0%</span>
        </div>
      </div>

      <div class="progress">
        <p class="archivePlaceholder pointer" title="<?php echo T_('Selecione um arquivo'); ?>"><?php echo isset($file) ? $file : T_('Selecione um arquivo'); ?></p>
        <a href="javascript: void(0);" class="remove-archive <?php echo isset($file) ? '' : ' hide '; ?> glyphicons remove_2" title="<?php echo T_('Remover arquivo'); ?>"><i class="fa fa-trash"></i></a>
      </div>

      <input type="file" id="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" class="fileuploadArchive <?php echo (isset($chunk) && $chunk) ? 'chunk' : '' ?>" name="file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" data-id="file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" data-language="<?php echo $id_lang; ?>" data-imgtype="archive" data-ext="<?php echo isset($ext) ? $ext : ''; ?>" data-url="<?php echo site_url('gallery/upload/archive'); ?>" value="<?php echo isset($file) ? $file : null; ?>">

      <input type="checkbox" name="delete-file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" <?php echo isset($file) && $file ? ' ' : ' checked '; ?> />

      <?php if (isset($file) && $file) { ?>
        <div class="download-archive" id="idArchive-<?php echo (isset($key) ? $key : '1'); ?>">
          <a href="<?php echo site_url('../userfiles/' . $module . '/' . $file); ?>" target="_blank">
            <i class="fa fa-download"></i> <?php echo T_('Baixar Arquivo'); ?>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
<?php } else {
  $dimensionsStr = implode('x', $dimensions);
?>
  <div class="col-xs-12 col-sm-<?php echo isset($colSize) && $colSize ? $colSize : 12; ?> form-group">
    <label for="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" class="control-label"><?php echo $label; ?>: </label>
    <div class="upload-wrapper" data-resize="<?php echo isset($resize) ? $resize : false; ?>" data-width="<?php echo $dimensions['w']; ?>" data-height="<?php echo $dimensions['h']; ?>">
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
          <span class="sr-only">0%</span>
        </div>
      </div>
      <?php if ($file) { ?>
        <a data-dim="<?php echo $dimensionsStr; ?>" href="<?php echo site_url('../userfiles/' . $module . '/' . $file); ?>" class="upload-image <?php echo $name; ?> magnific">
          <img src="<?php echo site_url('image/resize_canvas?src=ezfiles/' . $module . '/' . $file . '&w=75&h=75'); ?>" class="img-original" alt="">
        </a>
      <?php } else { ?>
        <a href="javascript:void(0);" data-dim="<?php echo $dimensionsStr; ?>" class="upload-image <?php echo $name; ?>">
          <span><?php echo $dimensionsStr; ?></span>
        </a>
      <?php } ?>
      <div class="upload-action">
        <label class="edit-upload" for="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>"><i class="fa fa-edit"></i></label>
        <a href="javascript: void(0);" class="remove-image <?php echo ($file) ? '' : ' hide '; ?>"><i class="fa fa-trash"></i></a>
      </div>

      <input class="fileuploadImage <?php echo (isset($chunk) && $chunk) ? 'chunk' : '' ?>" id="<?php echo $id . '-' . (isset($key) ? $key : '1'); ?>" type="file" name="file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" data-id="file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" data-ext="<?php echo isset($ext) ? $ext : ''; ?>" data-imgtype="<?php echo $name; ?>" data-url="<?php echo $upload; ?>" <?php //echo isset($required)? $required : null;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ?>>
      <input type="checkbox" name="delete-file<?php echo '[' . $name . ']' . (isset($id_lang) && is_numeric($id_lang) ? '[' . $id_lang . ']' : ''); ?>" <?php echo isset($file) && $file ? ' ' : ' checked '; ?> />
    </div>
  </div>
<?php } ?>