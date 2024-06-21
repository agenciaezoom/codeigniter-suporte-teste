<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
       }
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package CodeIgniter
 * @author  ExpressionEngine Dev Team
 * @copyright  Copyright (c) 2006, EllisLab, Inc.
 * @license http://codeigniter.com/user_guide/license.html
 * @link http://codeigniter.com
 * @since   Version 1.0
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * CodeIgniter Template Class
 *
 * This class is and interface to CI's View class. It aims to improve the
 * interaction between controllers and views. Follow @link for more info
 *
 * @package     CodeIgniter
 * @author      Colin Williams
 * @subpackage  Libraries
 * @category    Libraries
 * @link        http://www.williamsconcepts.com/ci/libraries/template/index.html
 * @copyright  Copyright (c) 2008, Colin Williams.
 * @version 1.4.1
 *
 */

/**
 * Modificações para HMVC + diversas adições e correções
 * @package CodeIgniter
 * @author Fábio Augustin Neis
 */

class CI_Template
{
    public $CI;
    public $config;
    public $template;
    public $master;
    public $regions = array(
      'head_scripts' => array(),
      'head_styles' => array(),
      'body_scripts' => array(),
    );
    public $output;
    public $js = array();
    public $css = array();
    public $parser = 'parser';
    public $parser_method = 'parse';
    public $parse_template = false;

   /* HMVC */
    public $module = '';
    public $module_views = 'views/';
    public $module_assets = 'assets/';
    public $template_dir = 'modules/comum/views/template/';

    public $data = array();
    public $partials = array();

   /**
     * Constructor
     *
     * Loads template configuration, template regions, and validates existence of
     * default template
     *
     * @access  public
     */

    public function __construct()
    {
       // Copy an instance of CI so we can use the entire framework.
        $this->CI = &get_instance();
        $this->module = $this->CI->router->fetch_module().'/';

       // Load the template config file and setup our master template and regions
        include(APPPATH.'config/template'.EXT);
        if (isset($template)) {
            $this->config = $template;
            $this->set_template($template['active_template']);
        }
    }

   // --------------------------------------------------------------------

   /**
    * Use given template settings
    *
    * @access  public
    * @param   string   array key to access template settings
    * @return  void
    */

    public function set_template($group)
    {
        if (isset($this->config[$group])) {
            $this->template = $this->config[$group];
        } else {
            show_error('The "'. $group .'" template group does not exist. Provide a valid group name or add the group first.');
        }
        $this->initialize($this->template);
    }

      // --------------------------------------------------------------------

   /**
    * Set master template
    *
    * @access  public
    * @param   string   filename of new master template file
    * @return  void
    */

    public function set_master_template($filename)
    {
        if (file_exists(APPPATH .$this->template_dir . $filename) or file_exists(APPPATH . $this->template_dir . $filename . EXT)) {
            $this->master = $filename;
        } else {
            show_error('The filename provided does not exist in <strong>'. APPPATH .'views</strong>. Remember to include the extension if other than ".php"');
        }
    }

   // --------------------------------------------------------------------

   /**
    * Dynamically add a template and optionally switch to it
    *
    * @access  public
    * @param   string   array key to access template settings
    * @param   array properly formed
    * @return  void
    */

    public function add_template($group, $template, $activate = false)
    {
        if (! isset($this->config[$group])) {
            $this->config[$group] = $template;
            if ($activate === true) {
                $this->initialize($template);
            }
        } else {
            show_error('The "'. $group .'" template group already exists. Use a different group name.');
        }
    }

   // --------------------------------------------------------------------

   /**
    * Initialize class settings using config settings
    *
    * @access  public
    * @param   array   configuration array
    * @return  void
    */

    public function initialize($props)
    {
       // Set master template
        if (isset($props['template'])
         && (file_exists(APPPATH . $this->template_dir . $props['template']) or file_exists(APPPATH . $this->template_dir . $props['template'] . EXT))) {
            $this->master = $props['template'];
        } else {
           // Master template must exist. Throw error.
            show_error('Either you have not provided a master template or the one provided does not exist in <strong>'. APPPATH .'views</strong>. Remember to include the extension if other than ".php"');
        }

       // Load our regions
        if (isset($props['regions'])) {
            $this->set_regions($props['regions']);
        }

       // Set parser and parser method
        if (isset($props['parser'])) {
            $this->set_parser($props['parser']);
        }
        if (isset($props['parser_method'])) {
            $this->set_parser_method($props['parser_method']);
        }

       // Set master template parser instructions
        $this->parse_template = isset($props['parse_template']) ? $props['parse_template'] : false;
    }

   // --------------------------------------------------------------------

   /**
    * Set regions for writing to
    *
    * @access  public
    * @param   array   properly formed regions array
    * @return  void
    */

    public function set_regions($regions)
    {
        if (count($regions)) {
            $this->regions = array(
            'head_scripts' => $this->regions['head_scripts'],
            'head_styles' => $this->regions['head_styles'],
            'body_scripts' => $this->regions['body_scripts'],
            );
            foreach ($regions as $key => $region) {
               // Regions must be arrays, but we take the burden off the template
               // developer and insure it here
                if (! is_array($region)) {
                    $this->add_region($region);
                } else {
                    $this->add_region($key, $region);
                }
            }
        }
    }

   // --------------------------------------------------------------------

   /**
    * Dynamically add region to the currently set template
    *
    * @access  public
    * @param   string   Name to identify the region
    * @param   array Optional array with region defaults
    * @return  void
    */

    public function add_region($name, $props = array())
    {
        if (! is_array($props)) {
            $props = array();
        }

        if (! isset($this->regions[$name])) {
            $this->regions[$name] = $props;
        } else {
            show_error('The "'. $name .'" region has already been defined.');
        }
    }

   // --------------------------------------------------------------------

   /**
    * Empty a region's content
    *
    * @access  public
    * @param   string   Name to identify the region
    * @return  void
    */

    public function empty_region($name)
    {
        if (isset($this->regions[$name]['content'])) {
            $this->regions[$name]['content'] = array();
        } else {
            show_error('The "'. $name .'" region is undefined.');
        }
    }

   // --------------------------------------------------------------------

   /**
    * Set parser
    *
    * @access  public
    * @param   string   name of parser class to load and use for parsing methods
    * @return  void
    */

    public function set_parser($parser, $method = null)
    {
        $this->parser = $parser;
        $this->CI->load->library($parser);

        if ($method) {
            $this->set_parser_method($method);
        }
    }

   // --------------------------------------------------------------------

   /**
    * Set parser method
    *
    * @access  public
    * @param   string   name of parser class member function to call when parsing
    * @return  void
    */

    public function set_parser_method($method)
    {
        $this->parser_method = $method;
    }

   // --------------------------------------------------------------------

   /**
     * Write contents to a region
     *
     * @access  public
     * @param   string  region to write to
     * @param   string  what to write
     * @param   boolean FALSE to append to region, TRUE to overwrite region
     * @return void
     */

    public function write($region, $content, $overwrite = true, $prepend = false)
    {
        if (isset($this->regions[$region])) {
            if ($overwrite === true) { // Should we append the content or overwrite it
                $this->regions[$region]['content'] = array($content);
            } else {
                if ($prepend === false) { // Should we append the content or preppend it
                    $this->regions[$region]['content'][] = $content;
                } else {
                    array_unshift($this->regions[$region]['content'], $content);
                }
            }
        } // Regions MUST be defined
        else {
            show_error("Cannot write to the '{$region}' region. The region is undefined.");
        }

       //Magic for $this->template->function1->function2
        return $this;
    }

   // --------------------------------------------------------------------

   /**
     * Write content from a View to a region. 'Views within views'
     *
     * @access  public
     * @param   string  region to write to
     * @param   string  view file to use
     * @param   array   variables to pass into view
     * @param   boolean FALSE to append to region, TRUE to overwrite region
     * @return void
     */

    public function write_view($region, $view, $data = null, $overwrite = true, $prepend = false)
    {
        $args = func_get_args();

       // Get rid of non-views
        unset($args[0], $args[2], $args[3]);

       // Do we have more view suggestions?
        if (count($args) > 1) {
            foreach ($args as $suggestion) {
                if (file_exists(APPPATH . $this->module . $this->module_views . $suggestion . EXT) or file_exists(APPPATH . $this->module . $this->module_views . $suggestion)) {
                    {
                   // Just change the $view arg so the rest of our method works as normal
                    $view = $suggestion;
                    break;
                    }
                }
            }
        }

        $content = $this->CI->load->view($view, $data, true);
        $this->write($region, $content, $overwrite, $prepend);

        return $this;
    }

   // --------------------------------------------------------------------

   /**
    * Parse content from a View to a region with the Parser Class
    *
    * @access  public
    * @param   string   region to write to
    * @param   string   view file to parse
    * @param   array variables to pass into view for parsing
    * @param   boolean  FALSE to append to region, TRUE to overwrite region
    * @return  void
    */

    public function parse_view($region, $view, $data = null, $overwrite = true)
    {
        $this->CI->load->library('parser');

        $args = func_get_args();

       // Get rid of non-views
        unset($args[0], $args[2], $args[3]);

       // Do we have more view suggestions?
        if (count($args) > 1) {
            foreach ($args as $suggestion) {
                if (file_exists(APPPATH . $this->module . $this->module_views . $suggestion . EXT) or file_exists(APPPATH . $this->module . $this->module_views . $suggestion)) {
                   // Just change the $view arg so the rest of our method works as normal
                    $view = $suggestion;
                    break;
                }
            }
        }

        $content = $this->CI->{$this->parser}->{$this->parser_method}($view, $data, true);
        $this->write($region, $content, $overwrite);

    }

   // --------------------------------------------------------------------

   /**
    * Dynamically include javascript in the template
    *
    *
    * @access  public
    * @param   string   script to import or embed
    * @param   string  can load from another module
    * @param   string  define where to put head or body
    * @param   boolean  TRUE to use 'defer' attribute, FALSE to exclude it
    * @return  TRUE on success, FALSE otherwise
    */

    public function add_js($script, $module = null, $position = 'body', $defer = false)
    {
        $js = null;

        $this->CI->load->helper('url');
        $dir = APPPATH . 'modules/' . (($module) ? $module.'/' : $this->module) . $this->module_assets;

        $external = preg_match("/^http/i", $script) || preg_match("/^\/\//i", $script);

        if (!$external && !preg_match("/\.js/i", $script)) {
            $script .= '.js';
        }

        if ($external) {
            $url = $script;
        } else{
            $hasMinified = FALSE;
            if (ENVIRONMENT != 'development'){
                if (!preg_match("/^plugins/i", $script) && preg_match("/(?<!\.min)\.(js)$/i", $script)){
                    $minified = preg_replace("/\.(js)$/i", '.min.js', $script);
                    if (file_exists($dir . $minified)) {
                        $script = $minified;
                        $hasMinified = TRUE;
                    }
                }
            }
            if ($hasMinified || file_exists($dir . $script)) {
                $url = base_url() . $dir . $script . '?v='.$this->CI->config->item('version');
            }
        }

        if (isset($url)) {
            $js = '<script type="text/javascript" src="'. $url .'"';
            if ($defer) {
                $js .= ' defer="defer"';
            }
            $js .= "></script>";
        }

       // Add to js array if it doesn't already exist
        if ($js != null && !in_array($js, $this->js)) {
            $this->js[] = $js;

            if ($position == 'body') {
                $this->write('body_scripts', $js, false);
            } else {
                $this->write('head_scripts', $js, false);
            }
        }

        return $this;
    }

   // --------------------------------------------------------------------

   /**
    * Dynamically include CSS in the template
    *
    *
    * @access  public
    * @param   string   CSS file to link, import or embed
    * @param   string  can load from another module
    * @param   string  media attribute to use with 'link' type only, FALSE for none
    * @return  TRUE on success, FALSE otherwise
    */

    public function add_css($style, $module = '', $media = false)
    {
        $css = null;

        $this->CI->load->helper('url');
        $dir = APPPATH . 'modules/' . (($module) ? $module.'/' : $this->module) . $this->module_assets;

        $external = preg_match("/^http/i", $style) || preg_match("/^\/\//i", $style);

        if (!$external && !preg_match("/\.css/i", $style)) {
            $style .= '.css';
        }

        if ($external) {
            $url = $style;
        } elseif (file_exists($dir . $style)) {
            $url = base_url() . $dir . $style . '?v='.$this->CI->config->item('version');
        }

        if (isset($url)) {
            $css = '<link type="text/css" rel="stylesheet" href="'. $url .'"';
            if ($media) {
                $css .= ' media="'. $media .'"';
            }
            $css .= ' />';
        }

       // Add to css array if it doesn't already exist
        if ($css != null && !in_array($css, $this->css)) {
            $this->css[] = $css;
            $this->write('head_styles', $css, false);
        }

        return $this;
    }

   // --------------------------------------------------------------------

   /**
     * Render the master template or a single region
     *
     * @access  public
     * @param   string  optionally opt to render a specific region
     * @param  boolean FALSE to output the rendered template, TRUE to return as a string. Always TRUE when $region is supplied
     * @return void                                                                                        or string (result of template build)
     */

    public function render($region = null, $buffer = false, $parse = false)
    {
       // Just render $region if supplied
        if ($region) { // Display a specific regions contents
            if (isset($this->regions[$region])) {
                $output = $this->_build_content($this->regions[$region]);
            } else {
                show_error("Cannot render the '{$region}' region. The region is undefined.");
            }
        } // Build the output array
        else {
            foreach ($this->regions as $name => $region) {
                $this->output[$name] = $this->_build_content($region);
            }

            if ($this->parse_template === true or $parse === true) {
               // Use provided parser class and method to render the template
                $output = $this->CI->{$this->parser}->{$this->parser_method}($this->master, $this->output, true);

               // Parsers never handle output, but we need to mimick it in this case
                if ($buffer === false) {
                    $this->CI->output->set_output($output);
                }
            } else {
               // Use CI's loader class to render the template with our output array
                $output = $this->CI->load->view('../../comum/views/template/'.$this->master, $this->output, $buffer);
            }
        }

        return $output;
    }

   // --------------------------------------------------------------------

   /**
     * Build a region from it's contents. Apply wrapper if provided
     *
     * @access  private
     * @param   string  region to build
     * @param   string  HTML element to wrap regions in; like '<div>'
     * @param  array   Multidimensional array of HTML elements to apply to $wrapper
     * @return string                                                      Output of region contents
     */

    public function _build_content($region, $wrapper = null, $attributes = null)
    {
        $output = null;

       // Can't build an empty region. Exit stage left
        if (! isset($region['content']) or ! count($region['content'])) {
            return false;
        }

       // Possibly overwrite wrapper and attributes
        if ($wrapper) {
            $region['wrapper'] = $wrapper;
        }
        if ($attributes) {
            $region['attributes'] = $attributes;
        }

       // Open the wrapper and add attributes
        if (isset($region['wrapper'])) {
           // This just trims off the closing angle bracket. Like '<p>' to '<p'
            $output .= substr($region['wrapper'], 0, strlen($region['wrapper']) - 1);

           // Add HTML attributes
            if (isset($region['attributes']) && is_array($region['attributes'])) {
                foreach ($region['attributes'] as $name => $value) {
                   // We don't validate HTML attributes. Imagine someone using a custom XML template..
                    $output .= " $name=\"$value\"";
                }
            }

            $output .= ">";
        }

       // Output the content items.
        foreach ($region['content'] as $content) {
            $output .= $content;
        }

       // Close the wrapper tag
        if (isset($region['wrapper'])) {
           // This just turns the wrapper into a closing tag. Like '<p>' to '</p>'
            $output .= str_replace('<', '</', $region['wrapper']) . "\n";
        }

        return $output;
    }

  /**
   * Build the entire HTML output combining partials, layouts and views.
   *
   * @access  public
   * @param string
   * @return  void
   */
    public function build($view, $data = array())
    {
      // Set whatever values are given. These will be available to all view files
        is_array($data) or $data = (array) $data;

      // Merge in what we already have with the specific data
        if ($this->data) {
            $this->data = array_merge($this->data, $data);
        } else {
            $this->data = $data;
        }

      // We don't need you any more buddy
        unset($data);

        if ($this->partials) {
            foreach ($this->partials as $region => $files) {
              // If it uses a view, load it
                foreach ($files as $file) {
                    if ($file != '') {
                        $this->write_view($region, $file, $this->data, false);
                    }
                }
            }
        }

        $this->write_view('content', $view, $this->data, false, true);

        return $this->render();
    }

  /**
   * Set a view partial
   *
   * @access  public
   * @param string
   * @param string
   * @return  void
   */
    public function set_partial($region, $view, $module = '', $overwrite = true)
    {
        if ($module) {
            $view = '../../'.$module.'/'.$this->module_views.$view;
        }

        if ($overwrite) {
            $this->partials[$region] = array();
        }

        $this->partials[$region][] = $view;

        return $this;
    }


  /**
  * Set data using a chainable metod. Provide two strings or an array of data.
  * If $name is a region set data as region data
  *
  * @access  public
  * @param   string
  * @param   string
  * @return  mixed
  */
    public function set($name, $value = null)
    {
        if (isset($this->regions[$name])) {
            $this->write($name, $value);
        } else {
          // Lots of things! Set them all
            if (is_array($name) or is_object($name)) {
                foreach ($name as $item => $value) {
                    $this->data[$item] = $value;
                }
            } // Just one thing, set that
            else {
                $this->data[$name] = $value;
            }
        }

        return $this;
    }

  /**
   * Add metadata
   *
   * @access  public
   * @param   string  $name   keywords, description, etc
   * @param   string  $content  The content of meta data
   * @param   string  $type   Meta-data comes in a few types, links for example
   * @return  void
   */
    public function add_metadata($name, $content, $type = 'meta')
    {

        $metadata = array();
        $name = htmlspecialchars(strip_tags($name));
        $content = htmlspecialchars(strip_tags($content));

      // Keywords with no comments? ARG! comment them
        if ($name == 'keywords' and ! strpos($content, ',')) {
            $content = preg_replace('/[\s]+/', ', ', trim($content));
        }

        switch ($type) {
            case 'meta':
                $metadata = '<meta name="'.$name.'" content="'.$content.'" />';
                break;

            case 'link':
                $metadata = '<link rel="'.$name.'" href="'.$content.'" />';
                break;
        }

        $this->write('metadata', $metadata, false);

        return $this;
    }
}
// END Template Class

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */
