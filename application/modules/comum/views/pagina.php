<section id="page">

  <div id="banners">
    <div class="banners-item">
      <?= lazyload(array(
        'src' => base_url('image/resize_crop?w=1920&h=1080&src=userfiles/paginas/' . $page_content->image),
        'alt' => $page_content->title,
        'data-background' => 1,
        'class' => 'lazyload'
      )); ?>
      <div class="common-limiter">
        <div class="banners-content">
          <div class="limiter">
            <h3 class="title"><?= $page_content->title; ?></h3>
            <div class="common-text"><?= $page_content->subtitle; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="common-limiter">
    <section id="text">
      <div class="common-text">
        <?= $page_content->text ?>
      </div>
    </section>
  </div>

</section>