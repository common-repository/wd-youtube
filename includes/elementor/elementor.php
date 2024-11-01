<?php
class YTWDElementor {

  protected static $instance = NULL;

  private function __construct() {
    // Register widget for Elementor builder.
    add_action('elementor/widgets/widgets_registered', array( $this, 'register_widgets' ));
    // Register 10Web category for Elementor widget if 10Web builder doesn't installed.
    add_action('elementor/elements/categories_registered', array( $this, 'register_widget_category' ), 1, 1);
    // Fires after elementor editor styles and scripts are enqueued.
    add_action('elementor/editor/after_enqueue_styles', array( $this, 'enqueue_styles' ), 1);
    add_action('elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_scripts' ));
  }

  public function register_widgets() {
    if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {
      $file = YTWD_DIR . '/includes/elementor/widget.php';
      if ( is_file($file) ) {
        require_once $file;
      }
    }
  }

  public function register_widget_category( $elements_manager ) {
    if ( !defined('TWBB_VERSION') ) {
      $elements_manager->add_category('tenweb-plugins-widgets', array(
        'title' => __('10WEB', 'tenweb-builder'),
        'icon' => 'fa fa-plug',
      ));
    }
  }

  public function enqueue_styles() {
    if ( !defined('TWBB_VERSION') ) {
      global $wp_styles;
      $style_key = 'twbb-fonts';
      $version = '1.2.13';
      foreach ( $wp_styles->queue as $style ) {
        if ( !empty($wp_styles->registered[$style_key]->ver) && version_compare($wp_styles->registered[$style_key]->ver, $version) === -1 ) {
          wp_deregister_style($style_key);
          wp_register_style($style_key, YTWD_URL . '/includes/elementor/assets/css/fonts.css', [], $version);
        }
      }
      wp_enqueue_style($style_key);
    }
  }

  public function enqueue_scripts() {
    wp_enqueue_script('ytwd_widget_js', YTWD_URL . '/includes/elementor/assets/js/script.js', array( 'jquery' ), '1.0.0');
  }

  public static function get_instance() {
    if ( self::$instance === NULL ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}