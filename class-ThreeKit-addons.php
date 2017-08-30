<?php
/**
 * Copyright: © 2017 Exocortex
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once THREEKIT_FOR_WOOCOMMERCE_DIR . '/class-ThreeKit.php';

class ThreeKitAddons extends ThreeKit {

  public function enable_threekit_by_checking_clarauuid_attribute() {
    global $product;
    $logger = wc_get_logger();
    $context = array( 'source' => 'Threekit-for-WooCommerce' );

    $logger->debug( 'Product id='.$product->get_id(), $context );

    $product_type = $product->get_type();

    // only works for variable product for now
    // if (strcmp($product_type, 'variable')) {
    //   return;
    // }

    $attributes = $product->get_attributes();

    foreach ( $attributes as $attribute_name => $options ) {
      // enable clara player and configurator when attribute clarauuid exist
      if (!strcmp($attribute_name, 'pa_clarauuid')) {
        $this->clarauuid = $product->get_attribute($attribute_name);
        if (!empty($this->clarauuid)) {
          $logger->debug( "Threekit plugin enabled", $context );

          // load template
          $this->replace_product_template_with_clara();
        }
      }
    }
  }
}

?>
