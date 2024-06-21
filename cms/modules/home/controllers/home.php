<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class home extends MY_Controller
{
  public function index()
  {
    $permissions = $this->auth->get_session_permissions();
    $modules = $this->auth->prepare_modules($permissions);

    if (!empty($modules)) {
      $redirect = site_url(reset($modules)->slug);
      redirect($redirect);
    }

    $this->template
      ->add_css('css/home')
      ->add_js('js/home')
      ->add_css('plugins/owl-carousel/owl.carousel', 'comum')
      ->add_js('plugins/owl-carousel/owl.carousel.min', 'comum')
      ->build('home');
  }
}
