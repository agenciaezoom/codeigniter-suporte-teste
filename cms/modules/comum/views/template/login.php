<!doctype html>
<html lang="<?php echo !empty($lang) ? $lang : null; ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="theme-color" content="#2D6BA1" />
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo !empty($title) ? $title : null; ?></title>
    <?php echo !empty($metadata) ? $metadata : null; ?>
    <script type="text/javascript">
        var site_url = '<?php echo site_url(); ?>',
            base_img = '<?php echo base_img(); ?>',
            module = '<?php echo $slug; ?>',
            <?php echo isset($i18n) ? 'i18n = '.$i18n.',' : ''; ?>
            segments = ('<?php echo $this->uri->uri_string(); ?>').split('/');
    </script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>
    <link rel="shortcut icon"  type="image/x-icon" href="<?php echo base_url('modules/comum/assets/img/favicon.ico'); ?>">
    <?php echo $head_styles, $head_scripts; ?>
</head>
<body class="loginWrapper preload">
    <?php echo $sidebar, $header; ?>
    <div class="container-fluid menu-hidden">
        <div id="content">
            <?php echo $content; ?>
        </div>
    </div>
    <?php echo $footer; ?>

    <script src="<?php echo base_url("modules/comum/assets/plugins/jquery-1.11.0.min.js"); ?>"></script>
    <?php echo $body_scripts; ?>
</body>
</html>
