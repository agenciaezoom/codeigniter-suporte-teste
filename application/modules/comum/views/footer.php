<?= Modules::run('newsletter') ?>

<?php if ($company->whatsapp) { ?>
  <div id="whatsapp">
    <a href="https://api.whatsapp.com/send?phone=<?= $company->whatsapp; ?>" class="whatsapp-wrapper">
      <?= load_svg('whatsapp.svg') ?>
    </a>
  </div>
<?php } ?>

<footer id="footer">
  <div class="common-limiter">
    <div class="ezoom">
      <a href="https://ezoom.com.br/" title="Desenvolvido por Ezoom"><?= load_svg('ezoom.svg') ?></a>
    </div>
</footer>