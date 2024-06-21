<!doctype html>
<html lang="<?php echo $lang; ?>">

<head>
  <meta charset="UTF-8">
  <title>404 Page Not Found</title>
  <link rel="stylesheet" href="<?php echo base_css('error_404'); ?>">
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_img('favicon.ico'); ?>">
</head>

<body>
  <div id="wrapper">
    <img src="<?php echo base_img('logo.png'); ?>" alt="" class="logo">
    <div class="message">
      <p class="big">Olá!</p>
      <p>Esta página não foi encontrada :(</p>
    </div>
    <a href="<?php echo site_url(); ?>">Clique aqui para acessar o site</a>
  </div>
</body>

</html>