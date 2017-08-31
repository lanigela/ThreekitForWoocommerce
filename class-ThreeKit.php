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
  protected $JSONConfig;
  protected $attributes;

  public function __construct() {

  }

  /*********** Callback Functions *****************/

  public function enable_threekit_by_checking_clarauuid_attribute() {
    global $product;
    $product_type = $product->get_type();

    // only works for variable product for now
    if (strcmp($product_type, 'variable')) {
      return;
    }

    $attributes = $product->get_attributes();

    foreach ( $attributes as $attribute_name => $options ) {
      // enable clara player and configurator when attribute clarauuid exist
      if (!strcmp($attribute_name, 'pa_clarauuid')) {
        $this->clarauuid = $product->get_attribute($attribute_name);
        if (!empty($this->clarauuid)) {

          // generate varations JSON config
          $this->JSONConfig = $this->variation_attributes_to_JSON($product->get_available_variations());
          $this->attributes = $product->get_variation_attributes();
          // load template
          $this->replace_product_template_with_clara();
        }
      }
    }


  }

  public function embed_clara_player() {
    load_template(rtrim(plugin_dir_path(__FILE__),'/') . '/templates/single-product/clara-player.php');
  }

  public function embed_clara_configurator() {
    load_template(rtrim(plugin_dir_path(__FILE__),'/') . '/templates/single-product/add-to-cart/clara-variation.php');

    // load scripts to init clara player
    wp_enqueue_script( 'claraConfigurator', rtrim(plugin_dir_url(__FILE__),'/') . '/assets/js/threekit/claraConfigurator.js');
    $dataToBePassed = array(
      'clarauuid' => $this->clarauuid,
      'available_attributes' => $this->JSONConfig,
      'attributes' => $this->attributes,
      'usingAddons' => false
    );
    // variables will be json encoded here
    wp_localize_script('claraConfigurator', 'php_vars', $dataToBePassed);
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
    add_action('woocommerce_before_single_product_summary', array($this, 'embed_clara_player'), 40);
    add_action('woocommerce_before_single_product_summary', array($this, 'embed_clara_configurator'), 50);
  }

  // convert $product->get_variation_attributes() to JSON
  protected function variation_attributes_to_JSON($variations) {
    return $variations;
  }
}

?>
