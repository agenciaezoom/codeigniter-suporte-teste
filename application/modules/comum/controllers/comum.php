<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Comum extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    //Manda pra home inves do 404
    if ($this->config->item('home_404')) {
      redirect(site_url(), 'location', 301);
    }

    set_status_header(404);
    $this->template->add_css('css/error_404', 'comum')
      ->set('title', T_('Página não encontrada') . ' - ' . $this->company->meta_title)
      ->build('comum/not_found');
  }

  public function change_mode($type = 'desktop')
  {
    if ($type == 'desktop') {
      $this->session->set_userdata('force_desktop', TRUE);
      redirect(site_url());
    } else {
      $this->session->set_userdata('force_desktop', FALSE);
      redirect(site_url());
    }
  }

  public function get_cities()
  {
    $this->input->is_ajax_request() || show_404();

    $cities = $this->comum_m->get_cities($this->input->post('id'));

    $this->output->set_content_type('application/json')
      ->set_output(json_encode($cities));
  }

  public function page($slug = FALSE)
  {
    $slug || show_404();
    $slug = $this->fix_slug($slug);

    if (empty($this->currentDbRoute))
      $this->template->set('title', $this->company->meta_title);

    $page_content = $this->comum_m->getPageContent(array(
      'slug' => $slug
    ));

    $this->template
      ->add_css('css/page')
      ->set('page_content', $page_content)
      ->build('pagina');
  }
}
