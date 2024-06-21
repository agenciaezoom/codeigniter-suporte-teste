<?php if (empty($products)) { ?>
  <div class="no-result"><?= T_('Nenhum resultado encontrado.') ?></div>
<?php } else { ?>
  <div class="slider">
    <?php foreach ($products as $product) { ?>
      <div class="products-item">
        <?= lazyload(array(
          'src' => base_url('image/resize_canvas?w=400&h=300&src=userfiles/produtos/' . $product->image),
          'alt' => $product->title,
          'data-background' => 0,
          'class' => 'lazyload'
        )); ?>
        <div class="content">
          <div class="title"><?= $product->title ?></div>
          <a href="<?= site_url($langLinks['produtos'] . $product->slug); ?>" class="common-button outlined"><span><?= T_('Ver produto'); ?></span></a>
        </div>
      </div>
    <?php } ?>
  </div>
<?php } ?>