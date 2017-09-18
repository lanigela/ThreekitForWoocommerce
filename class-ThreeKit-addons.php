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
          // remove addons templates
          $Product_Addon_Display = $GLOBALS['Product_Addon_Display'];
          remove_action( 'woocommerce_before_add_to_cart_button', array( $Product_Addon_Display, 'display' ), 10 );
          remove_action( 'woocommerce_before_variations_form', array( $Product_Addon_Display, 'reposition_display_for_variable_product' ), 10 );
          remove_action( 'woocommerce-product-addons_end', array( $Product_Addon_Display, 'totals' ), 10 );
        }
      }
    }
  }

  public function embed_clara_configurator() {
    global $product;

    if ( ! $post_id ) {
      global $post;
      $post_id = $post->ID;
    }

    $product_addons = get_product_addons( $post_id );


    load_template(rtrim(plugin_dir_path(__FILE__),'/') . '/templates/single-product/add-to-cart/clara-variation.php');

    // load react app and css
    wp_enqueue_style('reactcss', rtrim(plugin_dir_url(__FILE__),'/') . '/assets/css/main.css');
    wp_enqueue_script( 'claraConfigurator', rtrim(plugin_dir_url(__FILE__),'/') . '/assets/js/main.js');

    // load scripts to init clara player
    wp_enqueue_script( 'claraConfigurator', rtrim(plugin_dir_url(__FILE__),'/') . '/assets/js/threekit/claraConfigurator.js');
    $dataToBePassed = array(
      'clarauuid' => $this->clarauuid,
      'available_attributes' => $this->JSONConfig,
      'attributes' => $product_addons,
      'basePrice' => $product->get_price(),
      'usingAddons' => true
    );
    // variables will be json encoded here
    wp_localize_script('claraConfigurator', 'php_vars', $dataToBePassed);
  }
}

?>
