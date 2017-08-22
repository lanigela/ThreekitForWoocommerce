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

  /* Rearrange templates by modifying hook
  *  woocommerce_before_single_product_summary
  *  woocommerce_single_product_summary
  */
  function replace_product_template_with_clara() {
    remove_all_actions('woocommerce_before_single_product_summary');
    remove_all_actions('woocommerce_single_product_summary');
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
          replace_product_template_with_clara();
        }
      }
    }
  }
}

?>
