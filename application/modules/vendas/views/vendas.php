<section id="faq">
  <?= $this->load->view('comum/common_banner', ['banner' => $page_content['faq-banner']]); ?>

  <div class="common-limiter">
    <section id="questions">
      <div class="common-text">Respondemos as perguntas mais frequentes que nossos clientes nos fazem.</div>
      <div class="questions-wrapper">
        <?php foreach ($faq as $key => $each) { ?>
          <div class="question">
            <div class="title"><?= $each->title; ?></div>
            <div class="answer common-text"><?= $each->text; ?></div>
          </div>
        <?php } ?>
      </div>
    </section>

  </div>

</section>