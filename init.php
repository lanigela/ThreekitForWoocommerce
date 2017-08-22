<?php
/**
 * Plugin Name: Threekit for WooCommerce
 * Plugin URI: https://github.com/lanigela/ThreekitForWoocommerce
 * Description: Your extension's description text.
 * Version: 1.0.0
 * Author: Daniel Zhou
 * Author URI: daniel@exocortex.com
 * Developer: Exocortex
 * Developer URI: http://exocortex.com/
 * Text Domain: Threekit-for-WooCommerce
 *
 * Copyright: © 2017 Exocortex
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  // Put your plugin code here
  add_action('init', 'threekit_for_woocommerce_init');
  add_action('woocommerce_single_product_summary', 'enable_threekit_by_checking_clarauuid_attribute', 100);
}

if (!function_exists('threekit_for_woocommerce_init')) {
  function threekit_for_woocommerce_init() {
    // logger
    $logger = wc_get_logger();
    $context = array( 'source' => 'Threekit-for-WooCommerce' );
  }
}

/* Rearrange templates by modifying hook
*  woocommerce_before_single_product_summary
*  woocommerce_single_product_summary
*/
if (!function_exists('replace_product_template_with_clara')) {
  function replace_product_template_with_clara() {
    remove_all_actions('woocommerce_before_single_product_summary');
    remove_all_actions('woocommerce_single_product_summary');
  }
}

if (!function_exists('enable_threekit_by_checking_clarauuid_attribute')) {
  function enable_threekit_by_checking_clarauuid_attribute() {
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
