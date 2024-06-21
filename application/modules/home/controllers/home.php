<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Home extends MY_Controller
{
  public function __construct($isRun = FALSE)
  {
    parent::__construct($isRun);
  }

  public function index()
  {
    if (empty($this->currentDbRoute)) {
      $this->template->set('title', $this->company->meta_title);
    }

    $page_content = $this->comum_m->getPageContent(array(
      'area' => 'Home'
    ));

    $this->load->model(PATH_TO_MODEL . 'banners/models/banners_m');
    $banners = $this->banners_m->get(array(
      'where' => array(
        'status' => 1
      )
    ));

    $this->load->model(PATH_TO_MODEL . 'produtos/models/produtos_m');
    $products = $this->produtos_m->get(['where' => ['status' => 1], 'limit' => 12]);

    $this->load->library('WPFeed');
    $this->load->helper('text');
    $wp = new WPFeed();
    $posts = $wp->posts();
    $posts = $posts && isset($posts['data']['posts']['edges']) ? $posts['data']['posts']['edges'] : [];

    $this->template
      ->add_js('plugins/magnificpopup/magnificpopup.js', 'comum')
      ->add_js('plugins/slick/slick.min.js', 'comum')
      ->add_css('css/home')
      ->add_js('js/home')
      ->set('page_content', $page_content)
      ->set('banners', $banners)
      ->set('products', $products)
      ->set('posts', $posts)
      ->build('home');
  }
}
