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

  public function __construct() {

  }

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
        $uuid = $product->get_attribute($attribute_name);
        if (!empty($uuid)) {
          $logger->debug( "Threekit plugin enabled", $context );
          $this->replace_product_template_with_clara();
        }
      }
    }
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
    add_action('woocommerce_before_single_product_summary', array($this, 'show_clara_player'));
  }

  protected function show_clara_player() {
    $logger = wc_get_logger();
    $context = array( 'source' => 'Threekit-for-WooCommerce' );
    $logger->debug( 'Showing clara player', $context );
    wc_get_template('single-product/clara-player.php');
  }

  protected function show_clara_configurator() {
    wc_get_template('single-product/add-to-cart/clara-variation.php');
  }
}

?>
