<div id="home">

  <?php if (isset($banners) && !empty($banners)) { ?>
    <section id="banners">
      <div class="banners-wrapper">
        <?php foreach ($banners as $banner) { ?>

          <?php if ($banner->link) : ?>
            <a href="<?= $banner->link ?>" target="_blank">
            <?php endif ?>

            <div class="banner">
              <?php if ($banner->link_youtube || $banner->link_youtube) : ?>
                <iframe src="https://www.youtube-nocookie.com/embed/<?= get_youtube_id(!$mobile ? $banner->link_youtube : $banner->link_youtube_mobile) ?>?controls=0&mute=1&showinfo=0&rel=0&autoplay=1&loop=1&playlist=<?= get_youtube_id(!$mobile ? $banner->link_youtube : $banner->link_youtube_mobile); ?>&showinfo=0&wmode=transparent" frameborder="0" allowfullscreen>
                </iframe>
              <?php else : ?>
                <?= lazyload(array(
                  'src' => base_url('image/resize_crop?w=' . ($mobile ? '480' : '1920') . '&h=' . ($mobile ? '720' : '1080') . '&src=userfiles/banners/' . (!$mobile ? $banner->image : $banner->image_mobile)),
                  'alt' => 'Banner',
                  'data-background' => 1,
                  'class' => 'lazyload',
                )); ?>

                <div class="common-limiter">
                  <?php if ($banner->title) : ?>
                    <div class="content">
                      <h3 class="common-title white"><?= $banner->title; ?></h3>
                      <?php if ($banner->subtitle) : ?>
                        <p class="common-subtitle white"><?= $banner->subtitle; ?></p>
                      <?php endif ?>
                    </div>
                  <?php endif ?>
                <?php endif ?>
                </div>
            </div>

            <?php if ($banner->link) : ?>
            </a>
          <?php endif ?>

        <?php } ?>
      </div>
      <div class="banners-dots"></div>
    </section>
  <?php } ?>

  <?php if (isset($products) && !empty($products)) { ?>
    <section id="products">
      <div class="products-wrapper">
        <div class="common-slider products-slider">

          <?php foreach ($products as $each) { ?>
            <div class="product-item">

              <div class="content <?= (isset($each->background_image) && !empty($each->background_image)) ? '' : 'full-width' ?>">
                <span class="tile-title"><?= T_('Telhas') ?></span>
                <b class="product-title"><?= $each->title ?></b>
                <p class="common-text"><?= $each->subtitle; ?></p>
                <a href="<?= $langLinks['produtos'] . $each->slug; ?>" class="common-button outlined"><span><?= T_('Saiba mais'); ?></span></a>
              </div>

              <?php if (isset($each->background_image) && !empty($each->background_image)) {
                echo lazyload(array(
                  'src' => base_url('image/resize?w=500&h=420&src=userfiles/produtos/' . $each->background_image),
                  'alt' => $each->title,
                  'class' => 'lazyload',
                  'data-background' => 1,
                ));
              } ?>

            </div>
          <?php } ?>

        </div>
        <div class="common-dots"></div>

      </div>
    </section>
  <?php } ?>

</div>