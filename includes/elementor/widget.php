<?php

class YTWDElementorWidget extends \Elementor\Widget_Base {
  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'ytwd-elementor';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title() {
    return __('YouTube', 'ytwd');
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'twbb-youtube twbb-widget-icon';
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return [ 'tenweb-plugins-widgets' ];
  }

  /**
   * Register widget controls.
   */
  protected function _register_controls() {

    $this->start_controls_section('ytwd_general', [
      'label' => __('YouTube', 'ytwd'),
    ]);

    $this->add_control('ytwd_id', [
      'label' => __('YouTube Embed', 'ytwd'),
      'label_block' => TRUE,
      'description' => __('Select the YouTube Embed to display.', 'ytwd') . ' <a target="_balnk" href="' . add_query_arg(array( 'page' => 'youtube_ytwd' ), admin_url('admin.php')) . '">' . __('Edit Embed', 'ytwd') . '</a>',
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $this->get_embeds(),
      'default' => 0,
    ]);

    $this->end_controls_section();
  }

  /**
   * Render widget output on the frontend.
   */
  protected function render() {
    $settings = $this->get_settings_for_display();
	$shortcode_id = $settings['ytwd_id'];
	echo ytwd( rand(1, 100), $shortcode_id );
  }

  /**
   * Get Embeds.
   *
   * @return array
   */
  protected function get_embeds() {
		global $wpdb;
		$query = 'SELECT `id`, `title` FROM `' . $wpdb->prefix . 'ytwd_youtube` WHERE `published` = 1 ORDER BY id';				
		$rows = $wpdb->get_results($query);
		$embeds = array();
		foreach($rows as $row){
			$embeds[$row->id] = $row->title;
		}
		return $embeds;	
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YTWDElementorWidget() );