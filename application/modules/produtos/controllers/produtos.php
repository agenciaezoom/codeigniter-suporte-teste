<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Produtos extends MY_Controller
{
  public function __construct($isRun = FALSE)
  {
    parent::__construct($isRun);
    $this->load->model(PATH_TO_MODEL . 'produtos/models/produtos_m');
    $this->load->model(PATH_TO_MODEL . 'downloads/models/downloads_m');
  }

  public function index($slug = FALSE, $pg = FALSE)
  {
    $slug = $this->fix_slug($slug);
    if (is_array($slug)) {
      $slug_item = end($slug);
      $item = $this->produtos_m->get(['where' => ['site_product.status' => 1], 'slug' => $this->fix_slug($slug_item)]);

      if (!$item) {
        $this->_list($pg, $slug);
      } else {
        $this->_details($slug, $item);
      }
    } else {
      $this->_list($pg, $slug);
    }
  }

  private function _details($slug, $item)
  {
    if (!$item || empty($item))
      show_404();

    if (is_array($item)) {
      $item = reset($item);
    }

    $item->downloads = $this->downloads_m->get_downloads($item->id);
    $item->related = $this->produtos_m->get(['where' => ['site_product.id != ' . $item->id => NULL, 'site_product.id_category' => $item->id_category]]);

    if (isset($item->image) && $item->image) {
      $item->images[] = (object) [
        'file' => $item->image
      ];
    }

    //complemento da url para troca de idiomas
    $urlComplement = array();
    if ($this->config->config['routes_db']) {
      foreach ($this->config->config['supported_lang_id'] as $key => $id_language) {
        $urlComplement[$key] = '';
        if ($item->languages[$id_language]->slug)
          $urlComplement[$key] = $item->languages[$id_language]->slug;
      }
    }

    // SEO - Description
    $meta_description = $item->meta_description ? $item->meta_description : $this->template->get_metadata('description');
    if ($meta_description) {
      $this->template
        ->add_metadata('description', $meta_description)
        ->add_metadata('og:description', $meta_description, 'property')
        ->add_metadata('twitter:description', $meta_description);
    }

    // SEO - Keywords
    if (isset($item->meta_keywords) && !empty($item->meta_keywords)) {
      $this->load->helper('text');
      $this->template->add_metadata('keywords', html_entity_decode(character_limiter(strip_tags($item->meta_keywords), 200)), 'property');
    }

    // SEO - Imagem
    if (isset($item->image) && !empty($item->image)) {
      $ogImage = base_url('image/resize_crop?w=610&h=355&src=userfiles/produtos/' . $item->image);
      $ogImageW = 610;
      $ogImageH = 355;
    } else {
      $ogImage = base_url('image/resize_canvas?w=380&h=200&src=' . base_img('logo.png'));
      $ogImageW = 380;
      $ogImageH = 200;
    }

    $breadcrumb = [
      site_url($this->langLinks['produtos']) => T_('Produtos'),
      site_url($this->langLinks['produtos']) . $item->slug => $item->title,
    ];

    $this->template
      ->add_metadata('og:locale', $this->lang->lc_time_names, 'property')
      ->add_metadata('og:url', base_url($this->uri->uri_string()), 'property')
      ->add_metadata('og:title', $item->title, 'property')
      ->add_metadata('og:site_name', $this->company->meta_title, 'property')
      ->add_metadata('og:type', 'article', 'property')
      ->add_metadata('og:image', $ogImage, 'property')
      ->add_metadata('og:image:width', $ogImageW, 'property')
      ->add_metadata('og:image:height', $ogImageH, 'property')
      ->add_metadata('twitter:card', 'summary')
      ->add_metadata('twitter:title', $item->title)
      ->add_metadata('twitter:image', $ogImage)
      ->add_metadata('twitter:url', base_url($this->uri->uri_string()));

    $contents = array_merge($this->comum_m->getPageContent(array('area' => 'Produtos')), $this->comum_m->getPageContent(array('area' => 'Home')));

    $this->template
      ->set('urlComplement', $urlComplement)
      ->set('breadcrumb_route', $breadcrumb)
      ->set('item', $item)
      ->set('contents', $contents)
      ->add_js('plugins/magnificpopup/magnificpopup.js', 'comum')
      ->add_js('plugins/slick/slick.min.js', 'comum')
      ->add_js('plugins/zoom/jquery.zoom.min.js', 'comum')
      ->add_css('css/detalhes')
      ->add_js('js/detalhes')
      ->build('detalhes');
  }

  private function _list($pg = 1, $slug)
  {
    // if (!$slug) {
    //   $category = $this->categorias_m->get(['where' => ['site_category.status' => '1'], 'limit' => 1]);
    //   $category = reset($category);
    // }

    // if (is_array($slug) && isset($slug[0])) {
    //   $category = $this->categorias_m->get(['where' => ['site_category_description.slug' => $slug[0]]]);
    //   $category || show_404();

    //   if (is_array($category)) {
    //     $category = reset($category);
    //   }
    // }

    // if (is_array($slug) && isset($slug[0])) {
    //   $slug = $slug[0];
    // }

    $where = ['site_product.status' => '1'];

    // PRODUCTS
    $max = 24;
    $start = ($pg - 1) * $max;

    $items = $this->produtos_m->get(['where' => $where, 'limit' => $max, 'offset' => $start, 'site' => 1]);
    $total = count($items);

    $pagination = $this->_initPagination(array(
      'url'       => site_url((isset($this->langLinks['produtos']) ? $this->langLinks['produtos'] : 'produtos/') . $slug),
      'total'     => isset($total) ? $total : 200,
      'max'       => $max,
    ));

    $page_content = $this->comum_m->getPageContent(array(
      'area' => 'Produtos'
    ));

    $this->template
      ->add_js('plugins/slick/slick.min.js', 'comum')
      ->add_css('css/produtos')
      ->add_js('js/produtos')
      ->set('pagination', $pagination)
      ->set('items', $items)
      ->set('page_content', $page_content)
      ->build('produtos');
  }

  public function _remap($method, $params = [])
  {
    if (method_exists($this, $method)) {
      return call_user_func_array(array($this, $method), $params);
    } else {
      $segments = $params;
      array_unshift($segments, $method);
      $segments = array_map(array($this, 'fix_slug'), $segments);
      $pg = end($segments);
      if (is_numeric($pg)) {
        array_pop($segments);
        call_user_func_array(array($this, 'index'), array(0 => ($segments ? $segments : false), 1 => $pg));
      } else
        call_user_func_array(array($this, 'index'), array($segments));
    }
  }
}
