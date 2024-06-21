<aside id="newsletter">
  <div class="common-limiter">
    <form action="<?= site_url($langLinks['newsletter']); ?>" method="POST" id="form-newsletter" class="common-form ajax-form" novalidate>
      <h3 class="common-title"><?= T_('Receba nossa <b>Newsletter</b>'); ?></h3>

      <div class="form-box row">
        <div class="field">
          <input type="text" name="name" placeholder="<?= T_('Nome'); ?>" required>
        </div>
        <div class="field">
          <input type="email" name="email" placeholder="<?= T_('E-mail'); ?>" required>
        </div>
      </div>

      <button type="submit" class="common-button outlined submit">
        <span><?= T_('Cadastrar'); ?></span>
      </button>
    </form>
  </div>
</aside>