<?php
/**
 * Copyright: © 2017 Exocortex
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

class ThreeKit {

  protected $clarauuid;

  public function __construct() {

  }

  /*********** Callback Functions *****************/

  public function enable_threekit_by_checking_clarauuid_attribute() {
    global $product;
    $logger = wc_get_logger();
    $context = array( 'source' => 'Threekit-for-WooCommerce' );

    $logger->debug( 'Product id='.$product->get_id(), $context );

    $attributes = $product->get_attributes();
    foreach ( $attributes as $attribute_name => $options ) {
      $logger = wc_get_logger();
      $context = array( 'source' => 'Threekit-for-WooCommerce' );
      $logger->debug( $attribute_name, $context );
      $logger->debug( $product->get_attribute($attribute_name), $context );

      // enable clara player and configurator when attribute clarauuid exist
      if (!strcmp($attribute_name, 'pa_clarauuid')) {
        $this->clarauuid = $product->get_attribute($attribute_name);
        if (!empty($this->clarauuid)) {
          $logger->debug( "Threekit plugin enabled", $context );
          $this->replace_product_template_with_clara();
        }
      }
    }
  }

  public function show_clara_player() {
    $logger = wc_get_logger();
    $context = array( 'source' => 'Threekit-for-WooCommerce' );
    $logger->debug( 'Showing clara player', $context );
    $logger->debug( rtrim(plugin_dir_path(__FILE__),'/') . '/templates/single-product/clara-player.php', $context );
    $template_path = locate_template('clara-player.php');
    $logger->debug($template_path, $context);
    load_template($template_path);
    //wc_get_template(rtrim(plugin_dir_path(__FILE__),'/') . '/templates/single-product/clara-player.php');

    // load scripts to init clara player
    wp_enqueue_script( 'claraConfigurator', rtrim(plugin_dir_url(__FILE__),'/') . '/assets/js/threekit/claraConfigurator.js');
    $dataToBePassed = array(
      'clarauuid' => $this->clarauuid
    );
    wp_localize_script('claraConfigurator', 'php_vars', $dataToBePassed);
  }

  public function show_clara_configurator() {
    wc_get_template(rtrim(plugin_dir_path(__FILE__),'/') . '/templates/single-product/add-to-cart/clara-variation.php');
  }

  /*********** Internal Functions *****************/

  /* Rearrange templates by modifying hook
  *  woocommerce_before_single_product_summary
  *  woocommerce_single_product_summary
  */
  protected function replace_product_template_with_clara() {
    // remove existing contents
    remove_all_actions('woocommerce_before_single_product_summary');
    remove_all_actions('woocommerce_single_product_summary');
    // add clara player
    wp_enqueue_script( 'claraplayer', 'https://clara.io/js/claraplayer.min.js');
    add_action('woocommerce_before_single_product_summary', array($this, 'show_clara_player'));
  }


}

?>
