<?php
/**
 *
 *
 * @author  Daniel@exocortex.com
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

global $product;

$available_variations = $product->get_available_variations();

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div style="display: flex; justify-content: space-between;">
  <div id="panel-container" style="width: 73%">
    <div id="panel-embed"></div>
  </div>
  <div id="threekit-add-to-cart" style="width: 20%">
    <form method="post" enctype='multipart/form-data' >
      <?php do_action( 'woocommerce_before_variations_form' ); ?>

      <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
      <p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
      <?php else : ?>
        <div id="threekit-add-to-cart-inputs"></div>
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <div class="single_variation_wrap">
          <?php
            /**
             * woocommerce_before_single_variation Hook.
             */
            do_action( 'woocommerce_before_single_variation' );

            /**
             * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
             * @since 2.4.0
             * @hooked woocommerce_single_variation - 10 Empty div for variation data.
             * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
             */
            do_action( 'woocommerce_single_variation' );

            /**
             * woocommerce_after_single_variation Hook.
             */
            do_action( 'woocommerce_after_single_variation' );
          ?>
        </div>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
      <?php endif; ?>

      <?php do_action( 'woocommerce_after_variations_form' ); ?>
    </form>
  </div>
</div>
