<div class="menu">
  <div class="menu-content">

    <nav class="menu-list">
      <ul>
        <li class="products-menu">
          <span><?= T_('Nossos Produtos'); ?></span>
          <?= load_svg('angle-down.svg'); ?>

          <div class="submenu">
            <ul class="products-list">
              <li><a href="<?= site_url($langLinks['produtos']) ?>"><?= T_('Produtos') ?></a></li>
              <li><a href="<?= site_url($langLinks['produtos']) ?>"><?= T_('Acessórios') ?></a></li>
            </ul>
          </div>
        </li>
        <li><a href="<?= site_url($langLinks['contato']); ?>"><span><?= T_('Contato'); ?></span></a></li>
        <li><a href="<?= site_url($langLinks['faq']); ?>"><span><?= T_('FAQ'); ?></span></a></li>
      </ul>
    </nav>

    <div class="menu-buttons">
      <a href="https://api.whatsapp.com/send?phone=<?= $company->whatsapp ?>" class="common-button lighter">
        <span><?= T_('Faça seu pedido'); ?></span>
      </a>
    </div>

  </div>
</div>