<header id="header">
  <div class="common-limiter">
    <div class="header-wrapper">

      <a href="<?= site_url($langLinks['home']); ?>" class="logo" title="<?= $company->name; ?>">
        <?= load_svg('logo.svg') ?>
      </a>

      <button class="menu-btn" id="open-menu" title="<?= T_('Abrir o menu'); ?>">
        <div class="icon-menu">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </div>
      </button>

      <?php $this->load->view('menu'); ?>
    </div>
  </div>

  <div class="menu-fixed">
    <?php $this->load->view('menu'); ?>
  </div>
</header>