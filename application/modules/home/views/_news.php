<?php if (empty($news)) { ?>
  <div class="no-result"><?= T_('Nenhum resultado encontrado.') ?></div>
<?php } else { ?>
  <div class="slider">
    <?php foreach ($news as $each) { ?>
      <div class="news-item">
        <?= lazyload(array(
          'src' => $each['node']['featuredImage']['node']['mediaDetails']['sizes'][0]['sourceUrl'],
          'alt' => isset($each['node']['featuredImage']['node']['mediaDetails']['altText']) ? $each['node']['featuredImage']['node']['mediaDetails']['altText'] : $each['node']['title'],
          'data-background' => 1,
          'class' => 'lazyload'
        )); ?>
        <div class="content">
          <div class="date"><?= date("d/m/Y", strtotime($each['node']['date'])); ?></div>
          <div class="title"><?= $each['node']['title'] ?></div>
          <a href="<?= site_url($langLinks['blog'] . ($each->slug ?? '')) ?>" class="common-button outlined">
            <span><?= T_('Saiba mais') ?></span>
          </a>
        </div>
      </div>
    <?php } ?>
  </div>
<?php } ?>