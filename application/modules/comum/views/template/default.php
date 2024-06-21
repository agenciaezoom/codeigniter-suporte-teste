<!doctype html>
<html lang="<?php echo $lang == 'pt' ? 'pt-br' : $lang; ?>">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="theme-color" content="<?php echo isset($company->colors->primary) ? $company->colors->primary : '#000000'; ?>" />
  <meta name="HandheldFriendly" content="True">
  <meta name="MobileOptimized" content="320">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="cleartype" content="on">
  <title><?php echo $title; ?></title>
  <?php echo $metadata; ?>
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo base_img('icon/apple-touch-icon-144x144-precomposed.png'); ?>">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo base_img('icon/apple-touch-icon-114x114-precomposed.png'); ?>">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo base_img('icon/apple-touch-icon-72x72-precomposed.png'); ?>">
  <link rel="apple-touch-icon-precomposed" href="<?php echo base_img('icon/apple-touch-icon-57x57-precomposed.png'); ?>">
  <link rel="shortcut icon" href="<?php echo base_img('icon/apple-touch-icon.png'); ?>">
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_img('icon/favicon.ico'); ?>">
  <meta name="msapplication-TileImage" content="<?php echo base_img('icon/apple-touch-icon-144x144-precomposed.png'); ?>">
  <meta name="msapplication-TileColor" content="<?php echo isset($company->colors->primary) ? $company->colors->primary : '#000000'; ?>">
  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">

  <!-- Above the fold -->
  <style type="text/css">
    <?php echo (file_get_contents(APPPATH . 'modules/comum/assets/css/above_the_fold.css')); ?>
  </style>

  <?php
  echo $head_styles;

  //GOOGLE TAG MANAGER
  if (isset($company->google_tag_manager) && $company->google_tag_manager != '') { ?>
    <!-- Google Tag Manager -->
    <script>
      (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
          'gtm.start': new Date().getTime(),
          event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
          j = d.createElement(s),
          dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
          'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
      })(window, document, 'script', 'dataLayer', '<?php echo $company->google_tag_manager; ?>');
    </script>
    <!-- End Google Tag Manager -->
  <?php }

  echo $head_scripts;

  // Insere o css da empresa por último
  if (isset($company->css_file) && $company->css_file) {
  ?>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('userfiles/empresas/' . $company->css_file); ?>">
  <?php
  }
  ?>
</head>

<body>
  <div id="wrapper">
    <?php echo $header, $content, $footer; ?>

    <div id="common-dialog" class="zoom-anim-dialog mfp-hide common-dialog">
      <div class="icons">
        <div class="icon"><?php echo load_svg('succ.svg'); ?></div>
        <div class="icon"><?php echo load_svg('warning.svg'); ?></div>
        <div class="icon"><?php echo load_svg('cancel.svg'); ?></div>
      </div>
      <strong class="common-title"></strong>
      <div class="common-text"></div>
      <div class="common-buttons">
        <button type="button" class="common-button dismiss-modal"><span>Fechar</span></button>
      </div>
    </div>

  </div>
  <?php if (isset($company->google_tag_manager) && $company->google_tag_manager != '') { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $company->google_tag_manager; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?php } ?>
  <script type="text/javascript">
    var site_url = '<?php echo site_url(); ?>',
      base_url = '<?php echo base_url(); ?>',
      segments = ('<?php echo trim($this->uri->uri_string(), "/"); ?>').split('/'),
      <?php echo isset($i18n) ? 'i18n = ' . $i18n . ',' : ''; ?>
    mobile = <?php echo $mobile ? 'true' : 'false'; ?>,
      csrf_test_name = '<?php echo $csrf_test_name; ?>';
  </script>
  <?php echo $body_scripts; ?>
</body>

</html>