<!doctype html>
<html lang="<?php echo $lang; ?>">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="theme-color" content="#2D6BA1" />
  <meta name="HandheldFriendly" content="True">
  <meta name="MobileOptimized" content="320">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title; ?></title>
  <?php echo $metadata; ?>
  <script type="text/javascript">
    var site_url = '<?php echo site_url(); ?>',
      base_img = '<?php echo base_img(); ?>',
      module = '<?php echo $slug; ?>',
      <?php echo isset($i18n) ? 'i18n = ' . $i18n . ',' : ''; ?>
    segments = ('<?php echo $this->uri->uri_string(); ?>').split('/'),
      current_lang = '<?php echo $lang; ?>',
      gmaps_key = '<?php echo GMAPS_KEY; ?>',
      languages = {
        <?php foreach ($languages as $key => $language) { ?>
          <?php echo $language->id; ?>: {
            image: '<?php echo $language->image; ?>',
            code: '<?php echo $language->code; ?>',
          },
        <?php } ?>
      };
  </script>
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('modules/comum/assets/img/favicon.ico'); ?>">
  <?php echo $head_styles, $head_scripts; ?>
</head>

<body class="preload">
  <?php echo $sidebar, $header; ?>
  <section class="containerWithSidebar">

    <?php
    /**
     * VERIFICA SE AINDA HÁ ESPAÇO EM DISCO NO SERVIDOR
     */
    $space = disk_free_space('/');
    if ($space !== FALSE && ($space / (1024 * 1024)) <= 15) { //caso tenha 15MB livre ou menos 
    ?>
      <div class="col-xs-12 disk-space-alert">
        <div class="alert alert-danger"><strong>Atenção: </strong> Sua hospedagem está <?php echo $free_space <= 0 ? '' : 'quase '; ?>sem espaço de armazenamento. Evite realizar upload de arquivos e entre em contato conosco ou com a empresa responsável pela sua hospedagem se necessário.</div>
      </div>
      <div class="clearfix"></div>
    <?php } ?>

    <?php echo $breadcrumb; ?>
    <div class="containerBorder">
      <?php echo $content; ?>
    </div>
  </section>
  <?php echo $footer; ?>

  <div id="ajax-modal"></div>

  <div class="modal fade" id="modal-view">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h3 class="modal-title">Carregando...</h3>
        </div>
        <div class="modal-body padding-none loading">
          <i class="fa fa-fw fa-spinner fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
  <?php if ($method == 'editar') { ?>
    <div id="modal-crop" class="modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title">Recortar Imagem</h3>
          </div>
          <div class="modal-body">
            <div class="innerAll">
              <form action="<?php echo site_url('comum/crop-image'); ?>" class="margin-none innerLR inner-2x" method="post">
                <input type="hidden" name="image">
                <input type="hidden" name="image_width">
                <input type="hidden" name="image_height">
                <input type="hidden" name="crop_width">
                <input type="hidden" name="crop_height">
                <input type="hidden" name="crop_x">
                <input type="hidden" name="crop_y">
                <div class="widget widget-heading-simple widget-body-gray">
                  <div class="widget-body">
                    <div class="image-crop-holder"></div>
                  </div>
                </div>
                <div class="text-center innerAll btns-crop">
                  <button class="btn btn-default crop-cancel">Cancelar</button>
                  <button type="button" class="submit btn btn-primary">Recortar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  <script src="<?php echo base_url("modules/comum/assets/plugins/jquery-1.11.0.min.js"); ?>"></script>
  <?php echo $body_scripts; ?>
</body>

</html>