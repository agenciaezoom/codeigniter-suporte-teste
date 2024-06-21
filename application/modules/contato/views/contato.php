<section id="contact">
  <?= $this->load->view('comum/common_banner', ['banner' => $contents['contato-banner']]); ?>

  <div class="common-limiter">
    <section id="infos">
      <div class="infos-wrapper">
        <a class="infos-item" href="<?= 'https://www.google.com/maps?q=loc:(' . $company->latitude . ',' . $company->longitude . ')' ?>" target="_blank">
          <div class="icon"><?= load_svg('pin.svg') ?></div>
          <div class="title"><?= $company->address . ', ' . $company->city . ' - ' . $company->country ?></div>
        </a>
        <div class="infos-item">
          <div class="icon"><?= load_svg('mail.svg') ?></div>
          <div class="title"><a href="mailto:<?= $company->email ?>"><?= $company->email ?></a></div>
        </div>
        <div class="infos-item">
          <div class="icon"><?= load_svg('phone.svg') ?></div>
          <div class="title"><a href="tel:<?= $company->tel ?>"><?= $company->tel ?></a></div>
        </div>
        <?php if (isset($company->working_hours)) { ?>
          <div class="infos-item">
            <div class="icon"><?= load_svg('clock.svg') ?></div>
            <div class="title"><?= $company->working_hours ?></div>
          </div>
        <?php } ?>
      </div>
    </section>

    <section id="form">
      <div class="common-text"><?= T_('Preencha o formulário abaixo para falar com nossa equipe') ?></div>
      <form action="<?= site_url($langLinks['contato-send']); ?>" id="contact-form" method="POST" novalidate="novalidate" class="common-form ajax-form">
        <input type="hidden" name="csrf_test_name" value="<?= $csrf_test_name; ?>">

        <div class="row">
          <div class="field col-2">
            <label for="inputName"><?= T_('Nome'); ?></label>
            <input type="text" id="inputName" name="name" required>
          </div>
          <div class="field col-2">
            <label for="inputEmail"><?= T_('E-mail'); ?></label>
            <input type="email" id="inputEmail" name="email" required>
          </div>
        </div>

        <div class="row">
          <div class="field col-2">
            <label for="inputPhone"><?= T_('Telefone'); ?></label>
          </div>
          <div class="field col-2">
            <label for="selectDepartment"><?= T_('Assunto'); ?></label>
            <select name="id_department" id="selectDepartment" data-placeholder="">
              <option></option>
              <?php foreach ($departments as $each) { ?>
                <option value="<?= $each->id ?>"><?= $each->title ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="field">
            <label for="textMessage"><?= T_('Mensagem'); ?></label>
            <textarea name="message" id="textMessage" required><?= isset($product->title) ? (T_('Olá! Gostaria de solicitar um orçamento para o produto') . ' ' . $product->title . '.') : '' ?></textarea>
          </div>
        </div>

        <div class="row center">
          <button type="submit" class="common-button outlined submit">
            <span><?= T_('Enviar mensagem'); ?></span>
          </button>
        </div>

      </form>
    </section>
  </div>

</section>