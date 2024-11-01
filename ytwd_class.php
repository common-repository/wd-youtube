<?php

class YTWD {

  public static $params;
  protected static $instance = NULL;
  protected static $frontend_view = NULL;

  private function __construct() {
    add_action('init', array( $this, 'ytwd_do_output_buffer' ));
    // includes
    $this->ytwd_includes();
    // add scripts
    add_action('wp_enqueue_scripts', array( $this, 'ytwd_frontend_scripts' ), 2);
    add_action('init', array( $this, 'add_localization' ), 1);
    add_shortcode('YouTube_WD', array( 'YTWD', 'ytwd_shortcode' ));
    add_action('wp_head', array( $this, 'add_meta_tags' ));
    add_filter('language_attributes', array( $this, 'doctype_opengraph' ));
    add_action('wp_ajax_update_plays_analytics_data', array( $this, 'ytwd_ajax' ));
    add_action('wp_ajax_nopriv_update_plays_analytics_data', array( $this, 'ytwd_ajax' ));
    add_action('wp_ajax_get_video_data', array( $this, 'ytwd_ajax' ));
    add_action('wp_ajax_nopriv_get_video_data', array( $this, 'ytwd_ajax' ));
    add_action('wp_ajax_accept_gdpr', array( $this, 'ytwd_ajax' ));
    add_action('wp_ajax_nopriv_accept_gdpr', array( $this, 'ytwd_ajax' ));
  }

  //add meta tags
  public function add_meta_tags() {
    if ( ytwd_get_option("fb_markup") == 1 && shortcode_exists('YouTube_WD') && ytwd_get_option("api_key") ) {
      if ( is_single() || is_page() ) {
        global $post;
        if ( $post->ID && strpos($post->post_content, '[YouTube_WD') !== FALSE ) {
          $post_content = $post->post_content;
          $post_content = substr($post_content, strpos($post_content, '[YouTube_WD'));
          $post_content = str_replace(array( '\u0022', '\u0022' ), '', $post_content);
          $post_content = substr($post_content, 12);
          $tag_text = substr($post_content, 0, strpos($post_content, ']'));
          $params = array();

          /* Make this compatible with editors which are using only one of "id/item".
          (Notice: In the shortcode keep the order use "id" first, then "item") */
          if (strpos($tag_text, 'item=') === FALSE){
            $item1 = explode('id=', $tag_text);
            $params["item"] = intval($item1[1]);
          }
          else{
            $id1 = explode('item=', $tag_text);
            $params["item"] = intval($id1[1]);
          }
          $frontend_view = new Frontend_ytwd($params);
          self::$frontend_view = $frontend_view;
          $frontend_view->fb_markup();
        }
      }
    }
  }

  public function doctype_opengraph( $output ) {
    if ( ytwd_get_option("fb_markup") == 1 ) {
      return $output . '
            xmlns:og="http://opengraphprotocol.org/schema/"
            xmlns:fb="http://www.facebook.com/2008/fbml"';
    }
  }

  // Return an instance of this class.
  public static function ytwd_get_instance() {
    if ( NULL == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  public function ytwd_do_output_buffer() {
    ob_start();
  }

  public function ytwd_includes() {
    require_once(YTWD_DIR . '/includes/frontend_base_ytwd.php');
    require_once(YTWD_DIR . '/includes/youtube_api_data.php');
    require_once(YTWD_DIR . '/helpers/ytwd_functions.php');
    require_once(YTWD_DIR . '/helpers/ytwd_db.php');
    require_once(YTWD_DIR . '/includes/frontend_ytwd.php');
    require_once(YTWD_DIR . '/includes/ajax_ytwd.php');
  }

  public function add_localization() {
    $path = dirname(plugin_basename(__FILE__)) . '/languages/';
    $loaded = load_plugin_textdomain('ytwd', FALSE, $path);
    if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) && !$loaded ) {
      echo '<div class="error">Youtube ' . __('Could not load the localization file: ' . $path, 'ytwd') . '</div>';

      return;
    }
  }

  public static function ytwd_frontend() {
    $params = self::$params;
    $frontend_view = self::$frontend_view;
    if ( NULL == $frontend_view ) {
      $frontend_view = new Frontend_ytwd($params);
    }
    $frontend_view->execute();
    self::$frontend_view = NULL;
  }

  public function ytwd_frontend_scripts() {
    global $wpdb, $wp_scripts;
    $version = get_option("ytwd_version");
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery');
    wp_enqueue_script('ytwd_linkify-js', YTWD_URL . '/js/linkify.js');
    wp_enqueue_script('ytwd_frontend_main-js', YTWD_URL . '/js/frontend_main.js', array(), $version, TRUE);
    wp_enqueue_script('ytwd_platform-js', 'https://apis.google.com/js/platform.js');
    wp_localize_script('ytwd_frontend_main-js', 'ytwdGlobal', array(
      "ajaxURL" => admin_url('admin-ajax.php'),
      "YTWD_URL" => YTWD_URL,
      "txt_show_more" => __("Show More", "ytwd"),
      "txt_show_less" => __("Show Less", "ytwd"),
      "txt_loading" => __("Loading", "ytwd"),
    ));
    //wp_enqueue_script( 'ytwd-js', 'https://www.youtube.com/iframe_api');
    wp_enqueue_style('ytwd_frontend_main-css', YTWD_URL . '/css/frontend_main.css', array(), $version);
    wp_enqueue_style('ytwd_bootstrap-css', YTWD_URL . '/css/bootstrap.css', array(), $version);
    wp_enqueue_style('ytwd_font-css', YTWD_URL . '/css/font-awesome/font-awesome.css');
  }

  // Shortcode function
  public static function ytwd_shortcode( $params ) {
    //@ToDo doesn't get the reason of this if statement, please comment more information about this type of codes
    if ( empty($params["item"]) ) {
      $params["item"] = $params["id"];
      $params["id"] = isset($_REQUEST["shortcode_id"]) ? sanitize_text_field($_REQUEST["shortcode_id"]) : time() . rand(1, 100);
    }
    $params = shortcode_atts(array(
                               'id' => "1",
                               'item' => "1",
                             ), $params, 'YouTube_WD');
    ob_start();
    self::$params = $params;
    self::ytwd_frontend();

    return str_replace(array( "\r\n", "\n", "\r" ), '', ob_get_clean());
  }

  public function ytwd_ajax() {
    $action = isset($_REQUEST["action"]) ? sanitize_text_field($_REQUEST["action"]) : '';
    if ( method_exists('Ajax_ytwd', $action) ) {
      Ajax_ytwd::$action();
    }
    die();
  }
}
