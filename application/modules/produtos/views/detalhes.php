<article id="product-details" itemscope itemtype="http://schema.org/Product">
  <meta itemprop="url" content="<?= base_url($this->uri->uri_string()); ?>">

  <?= $this->load->view('comum/common_banner', ['banner' => (object) ['title' => $item->title, 'subtitle' => $item->subtitle]]); ?>

  <div class="common-limiter">
    <?= lazyload(array(
      'src' => base_url('image/resize_canvas?w=400&h=400&src=userfiles/produtos/' . $item->image),
      'alt' => $item->title,
      'data-background' => 0,
      'class' => 'lazyload image'
    )); ?>
  </div>

  <section id="text">
    <div class="common-limiter">
      <div class="common-text">Possui uma variedade em peças complementares que atendem às necessidades de todos os projetos.</div>
    </div>
  </section>

  <?php if (isset($item->attributes) && !empty($item->attributes)) { ?>
    <section id="attributes">
      <div class="attributes-wrapper">
        <div class="common-limiter">
          <h4 class="common-title"><span><?= T_('Benefícios <b>do Produto</b>'); ?></span></h4>
          <ul class="attributes-list">
            <?php foreach ($item->attributes as $key => $each) { ?>
              <li class="attribute">
                <div class="attribute-image">
                  <?= lazyload(array(
                    'src' => base_url('image/resize_canvas?w=38&h=38&src=userfiles/atributos/' . $each->image),
                    'alt' => $each->title,
                    'data-background' => 1,
                    'class' => 'lazyload',
                  )); ?>
                </div>
                <h2 class="attribute-title" itemprop="name"><?php echo $each->title; ?></h2>
              </li>
            <?php   } ?>
          </ul>
        </div>
      </div>
    </section>
  <?php } ?>

  <?php if ((isset($item->infos) && $item->infos) || (isset($item->downloads) && $item->downloads)) { ?>
    <div id="infos">
      <div class="infos-wrapper">
        <div class="common-limiter">
          <h2 class="common-title"><?= T_('Informações <b>Técnicas</b>'); ?></h2>

          <div class="infos-list">
            <?php if (isset($item->infos) && $item->infos) { ?>
              <?php foreach ($item->infos as $info) { ?>
                <div class="infos-row">
                  <div class="attribute"><?= $info->field; ?></div>
                  <div class="value"><?= $info->value; ?></div>
                </div>
              <?php } ?>
            <?php } ?>
            <?php if (isset($item->downloads) && $item->downloads) { ?>
              <div class="infos-row">
                <div class="attribute"><?= T_('Catálogo') ?></div>
                <div class="value"><a href="<?= site_url() ?>" download class="common-button outlined"><span><?= T_('Download do Catálogo') ?></span></a></div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php if (isset($contents['home-televendas']) && !empty($contents['home-televendas'])) { ?>
    <div class="common-limiter">
      <section id="telesales">
        <div class="telesales-wrapper">
          <div class="content">
            <h3 class="title"><?= $contents['home-televendas']->title ?></h3>
            <div class="common-text"><?= $contents['home-televendas']->subtitle ?></div>
          </div>
          <?= lazyload(array(
            'src' => base_url('image/resize_crop?w=896&h=260&src=userfiles/paginas/' . $contents['home-televendas']->image),
            'alt' => $contents['home-televendas']->title,
            'class' => 'lazyload',
            'data-background' => 1,
          )); ?>
        </div>
      </section>
    </div>
  <?php } ?>

  <section id="inner-banners">
    <div class="inner-banners-wrapper">
      <?php if (isset($contents['home-guia-instale-facil']) && !empty($contents['home-guia-instale-facil'])) { ?>
        <div class="inner-banners-item">
          <div class="content">
            <div class="content-title">
              <div class="icon"><?= load_svg('guide.svg'); ?></div>
              <h3 class="title"><?= $contents['home-guia-instale-facil']->title ?></h3>
            </div>
            <a href="<?= $contents['home-guia-instale-facil']->link; ?>" target="<?= $contents['home-guia-instale-facil']->target; ?>" class="common-button outlined"><span><?= $contents['home-guia-instale-facil']->link_label; ?></span></a>
          </div>
          <?= lazyload(array(
            'src' => base_url('image/resize_crop?w=896&h=260&src=userfiles/paginas/' . $contents['home-guia-instale-facil']->image),
            'alt' => $contents['home-guia-instale-facil']->title,
            'class' => 'lazyload',
            'data-background' => 1,
          )); ?>
        </div>
      <?php } ?>

    </div>
  </section>

</article>