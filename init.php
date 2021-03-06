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


$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
if ( in_array( 'woocommerce/woocommerce.php', $active_plugins) ) {
  // Put your plugin code here
  $using_addons = false;
  foreach ( $active_plugins as $key => $active_plugin ) {
    if ( strstr( $active_plugin, 'woocommerce-product-addons.php' ) ) {
      $using_addons = true;
    }
  }

  define('THREEKIT_FOR_WOOCOMMERCE_DIR', rtrim(plugin_dir_path(__FILE__),'/'));

  if ($using_addons) {
    // using addons for configuration
    add_action('init', 'threekit_addons_for_woocommerce_init');
  }
  else {
    // using default variation for configuration
    add_action('init', 'threekit_for_woocommerce_init');
  }
}


if (!function_exists('threekit_for_woocommerce_init')) {
  function threekit_for_woocommerce_init() {
    require_once THREEKIT_FOR_WOOCOMMERCE_DIR . '/class-ThreeKit.php';
    $api = new ThreeKit();

    add_action('woocommerce_before_single_product', array($api, 'enable_threekit_by_checking_clarauuid_attribute'));
  }
}

if (!function_exists('threekit_addons_for_woocommerce_init')) {
  function threekit_addons_for_woocommerce_init() {
    require_once THREEKIT_FOR_WOOCOMMERCE_DIR . '/class-ThreeKit-addons.php';
    $api = new ThreeKitAddons();

    // using a low priority number to make sure running after woocommerce-addons
    add_action('woocommerce_before_single_product', array($api, 'enable_threekit_by_checking_clarauuid_attribute'), 100);
  }
}

?>
