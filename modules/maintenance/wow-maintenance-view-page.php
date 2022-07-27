<?php
if (isset($_POST['wow_maintenance_nonce'])) {
	$this->status_maintenance_save();
}
global $current_screen;
?>
<div class="wrap">
  <h2>WoW-Manager: Attiva/Disattiva Manutenzione Sito</h2><!--pageID: <?php echo $current_screen->id ?> -->
  <div id="wow-main">
     <hr />
   <div id="maintenance">
    <form action="" method="post" id="form_mainpage">
        <table class="form-table" width="auto">
            <tr valign="middle">
                <th>
                    <label for="wow_mm_enabled"><?php _e('Modalità Aggiornamento e Manutenzione', $this->textdomain); ?></label>
                </th>
                <td nowrap="nowrap">
                <input type="radio" id="wow_mm_enabled" name="wow_mm_enabled" value="0" <?php checked($this->is_maintenance, 0); ?>> Disabilitata
                <input type="radio" id="wow_mm_enabled" name="wow_mm_enabled" value="1" <?php checked($this->is_maintenance, 1); ?>> Abilitata
                <?php wp_nonce_field( 'wow_maintenance', 'wow_maintenance_nonce' );?>
                &nbsp;&nbsp;
                <?php submit_button('Salva Impostazione','primary',null,false); ?></td>
            </tr>
        </table>
    </form>
    </div>
    <hr />
    <div id="bookstore">
    </div>
  </div>
</div>
<script>
jQuery(function() {
//	jQuery( "#woowowopt" ).tabs();
});
</script> 
