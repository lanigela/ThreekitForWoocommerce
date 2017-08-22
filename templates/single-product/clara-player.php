<?php
/**
 *
 *
 * @author  Daniel@exocortex.com
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
$logger = wc_get_logger();
$context = array( 'source' => 'Threekit-for-WooCommerce' );

if ( ! defined( 'ABSPATH' ) ) {
  $logger->debug( 'aaaaaaaaaaaa', $context );
  exit;
}


$logger->debug( 'template loaded', $context );

?>
<div class="" id="clara-player" style="height:400px">
</div>
