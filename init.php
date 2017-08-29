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
  $logger = wc_get_logger();
  $context = array( 'source' => 'Threekit-for-WooCommerce' );
  foreach ( $active_plugins as $key => $active_plugin ) {
    $logger->debug( $active_plugin, $context );
    if ( strstr( $active_plugin, '/woocommerce-product-addons.php' ) ) {
        $logger->debug( 'goal!', $context );
    }
  }
  if (in_array( 'woocommerce/woocommerce-product-addons.php', $active_plugins )) {


    $logger->debug( 'addon enabled', $context );
  }
  else {
    define('THREEKIT_FOR_WOOCOMMERCE_DIR', rtrim(plugin_dir_path(__FILE__),'/'));
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

?>
