<?php 
	global $current_screen;
	
	$tab = 'default';
	$logs=array_keys($this->logs);
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'wow-actions-logger' ) {
		if ( isset( $_GET['tab'] ) ) {
			if ( in_array( $_GET['tab'], $logs ) )
				$tab = esc_attr( $_GET['tab'] );
		}
	}
?>

<div class="wrap woocommerce">
  <div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"></div>
  <h1 class="wp-heading-inline"><?php _e('Visualizzazione Registrazioni Attivit&agrave;', $this->textdomain)?></h1>
  <h1 class="nav-tab-wrapper">
  <a href="<?php echo admin_url('admin.php?page=wow-actions-logger&tab=default'); ?>" class="nav-tab <?php if ( $tab == 'default' ) echo 'nav-tab-active'; ?>"><?php _e('Main', $this->textdomain)?></a>
  <?php 
  foreach($this->logs as $k=>$log) {
  ?>
  <a href="<?php echo admin_url('admin.php?page=wow-actions-logger&tab='. $k .''); ?>" class="nav-tab <?php if ( $tab == $k ) echo 'nav-tab-active'; ?>"><?php echo $log->get_logtitle() ?></a>
<?php } ?>
  </h1>
</div>
  <?php
	switch ( $tab ) {
		case 'default':		
      		echo '<h2>' . __( 'Selezionare un log da visualizzare !', $this->textdomain) . '</h2>';
		break;
		default:
			$thislog=$this->get_log($tab);
			$thislog->display_log_tab();
		break;
	}
?>

<script>
jQuery(function() {
//	jQuery( "#woowowopt" ).tabs();
});
</script> 
