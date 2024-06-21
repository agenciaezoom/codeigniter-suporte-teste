<div id="banners" class="common-banner">
  <div class="banners-item">

    <?= lazyload(array(
      'src' => base_url('image/resize_crop?w=1920&h=' . (isset($height) ? $height : '564') . '&src=' . (isset($banner->path) ? $banner->path : 'userfiles/paginas/') . (isset($banner->image) ? $banner->image : 'default.jpg')),
      'alt' => isset($banner->title) ? $banner->title : 'Banner',
      'data-background' => 1,
      'class' => 'lazyload'
    )); ?>

    <?php if (isset($banner->tag) || isset($banner->title) || isset($banner->subtitle)) { ?>
      <div class="common-limiter">
        <div class="banners-content">
          <div class="limiter">
            <?php if (isset($banner->title)) { ?>
              <h3 class="title"><?= $banner->title; ?></h3>
            <?php } ?>
            <?php if (isset($banner->subtitle)) { ?>
              <div class="subtitle"><?= $banner->subtitle; ?></div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>