<?php
if (! isset($_POST['del_step']) || $_POST['del_step']==0) {
	$stepdel=0;
} else {
	$stepdel=$_POST['del_step']+1;
}

?>
	  <form method="post">
      <select name="deletion_type">
        <option value="0" selected="selected" <?php if (!(strcmp(0, $_POST['deletion_type']))) {echo "selected=\"selected\"";} ?>><?php _e('** Seleziona **', $this->textdomain) ?></option>
        <option value="T" <?php if (!(strcmp("T", $_POST['deletion_type']))) {echo "selected=\"selected\"";} ?>><?php _e('Sposta nel Cestino', $this->textdomain) ?></option>
        <option value="D" <?php if (!(strcmp("D", $_POST['deletion_type']))) {echo "selected=\"selected\"";} ?>><?php _e('Cancella Definitivamente', $this->textdomain) ?></option>
      </select>
      <input name="del_step" type="hidden" value="<?php echo $stepdel; ?>" />
<?php 
if ($stepdel>0) { ?>
		<input type="submit" class="button button-primary" value="<?php _e('Prosegui con la Cancellazione dei Prodotti, nuovamente nessuna conferma verrà richiesta!!', $this->textdomain) ?>" />
<?php } else { ?>
		<input type="submit" class="button button-primary" value="<?php _e('Cancellazione dei Prodotti, nessuna conferma verrà richiesta!!', $this->textdomain) ?>" />
<?php } ?>
		<?php wp_nonce_field( 'delete_action', 'delete_security_nonce'); ?>
	  </form>
