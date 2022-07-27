<?php 
	global $current_screen;
?>

<div class="wrap woocommerce">
  <div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"></div>
  <h1 class="wp-heading-inline">Cancellazione Prodotti</h1>
  <?php
	if ( isset($_REQUEST['settings-updated']) ) { 
?>
  <div id="message" class="updated fade">
    
      <?php echo '<p><strong>' . __( 'Your settings have been saved.', $this->textdomain) . '</strong></p>'; ?>
      
  </div>
<?php	} ?>
<?php 
	$maximum_time   = ini_get('max_execution_time');
	$timestamp      = time();
	$timeout_passed = false;
	$removed        = 0;
?>
</div>
<div id="wc_remove_all_products_options" class="woocommerce_options_panel">
  <?php
  
  	$products_count = 0;
  	foreach ( wp_count_posts( 'product' ) as $product )
		$products_count += $product;
  	foreach ( wp_count_posts( 'product_variation' ) as $variation )
		$products_count += $variation;
  

	if ( ! $products_count ) {
		echo '<h2>' . __( 'No products found.', $this->textdomain) . '</h2>';
	} else {
		echo '<h2>' . sprintf(__( 'Found %s products.', $this->textdomain), $products_count ) . '</h2>';
		
		if (  empty( $_POST ) ) {
			include_once( 'wow-deleteproduct-form.php' );

		} elseif ( check_admin_referer( 'delete_action', 'delete_security_nonce' ) ) {
			
			$args = array( 
				'post_type'   => array( 'product', 'product_variation' ),
				'post_status' => get_post_stati(),
				'numberposts' => 250, 
				);
//			$logdel=$this->loggers->get_log('delete_product');	
			$logdel=$this->logger;	
			$products = get_posts( $args );
			$action = $_POST['deletion_type'];
			if ($action<>'0') {
				$remove=($action=="D"?true:false);
				$msg = sprintf(__( 'Trying to remove %s products.', $this->textdomain), sizeof( $products ) );
				printf( '<p>%s</p><ol>', $msg );
				if ($logdel) $logdel->scrivi_log( $msg );
				foreach( $products as $product ) {
					printf( '<li>%s</li>', $product->post_title );
					wp_delete_post( $product->ID, $remove );
					$removed++;
					if ( ( time() - $timestamp ) > ( $maximum_time - 2 ) ) {
						$timeout_passed	= true;
						break;
					}
				}
				$msg =  sprintf(__( 'Removed %s products.', $this->textdomain), $removed ) ;		
				printf( '</ol><p>%s</p>', $msg );
				if ($logdel) $logdel->scrivi_log( $msg );
				if ( $timeout_passed ) {
					$msg =  sprintf(__( 'Stopped processing due to imminent timeout.', $this->textdomain) ) ;		
					printf( '<h3>%s</h3>', $msg );
					if ($logdel) $logdel->scrivi_log( $msg );
					include_once( 'wow-deleteproduct-form.php' );
	?>
	<?php				
				}
			} else {
				echo _e('Select an action before continue !', $this->textdomain);
				include_once( 'wow-deleteproduct-form.php' );
			}
		}
	}
?>
</div>

<script>
jQuery(function() {
//	jQuery( "div.icon32" ).show();
});
</script> 
