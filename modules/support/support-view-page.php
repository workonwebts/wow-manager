<?php
/*
if (isset($_POST['wowbs_maintenance_nonce'])) {
	$this->status_maintenance_save();
}
*/
global $current_screen;

$_text1=$this->buildCustomText();
$_text2=$this->buildSupportText();
?>
<div class="wrap">
  <h2><?php echo WOW_MANAGER_TITLE ?>: Pagina Crediti</h2><!--pageID: <?php echo $current_screen->id ?> -->
  <div id="wow-main">
    <hr />
    <div id="customtext">
    <h2 align="center"><?php echo $_text1; ?></h2>
    <h4 align="center"><?php echo $_text2; ?></h4>
    </div>
  </div>
</div>
<script>
jQuery(function() {
//	jQuery( "#woowowopt" ).tabs();
});
</script> 
