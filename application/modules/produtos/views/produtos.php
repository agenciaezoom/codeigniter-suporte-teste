<section id="products">
  <?= $this->load->view('comum/common_banner', ['banner' => (object) ['title' => T_('Telhas')]]); ?>

  <div class="common-limiter">
    <div class="products-wrapper">

      <div class="products-list">
        <?php if (!empty($items)) { ?>
          <?= $this->load->view('_produtos', ['products' => $items]); ?>
        <?php } else { ?>
          <p class="no-results"><?= T_('Nenhum resultado encontrado'); ?>.</p>
        <?php } ?>
      </div>

    </div>
  </div>
</section>