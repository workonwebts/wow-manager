<?php
/*
function WoW_wowbs_maintenance_save( $post_id = null ) {
	global $wowbs;
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['wowbs_maintenance_nonce'] ) || !wp_verify_nonce( $_POST['wowbs_maintenance_nonce'], 'wowbs_maintenance' ) ) return;
     
     
    // now we can actually save the data
    if( isset( $_POST['wow_mm_enabled'] ) ) {
		update_option( 'wowbs_maintenance_enabled', $_POST['wow_mm_enabled'] );
		$wowbs->cache_plugin();
		wp_safe_redirect( $_POST['_wp_http_referer'] );
	}
}
if (isset($_POST['wowbs_maintenance_nonce'])) {
WoW_wowbs_maintenance_save();
//	wp_die('post non trovato');
//} else {
}
*/
		global $current_screen;
?>
<div class="wrap">
  <h2>WoW-Manager: Pagina Principale</h2><!--pageID: <?php echo $current_screen->id ?> -->
  <div id="wow-main">
    <hr />
    <div id="wow-manager">
    </div>
  </div>
</div>
<script>
jQuery(function() {
//	jQuery( "#woowowopt" ).tabs();
});
</script> 
