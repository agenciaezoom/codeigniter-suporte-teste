<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Vendas extends MY_Controller
{
  public function __construct($isRun = FALSE)
  {
    parent::__construct();
  }

  public function index($slug = FALSE, $letter = FALSE)
  {
    if (empty($this->currentDbRoute))
      $this->template->set('title', $this->company->meta_title);

    $page_content = $this->comum_m->getPageContent(['area' => 'FAQ', 'status' => 1]);

    $this->load->model(PATH_TO_MODEL . 'faq/models/faq_m');
    $faq = $this->faq_m->get(['where' => ['status' => 1]]);

    $this->template
      ->add_css('css/vendas')
      ->add_js('js/vendas')
      ->set('page_content', $page_content)
      ->set('faq', $faq)
      ->set('slug', $slug)
      ->build('vendas');
  }
}
