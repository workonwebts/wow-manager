	<div class="panel woocommerce_options_panel" style="padding-right: 20px;">
	  <?php 	
		if ( isset($_GET['clear_log'] )
			&& 1 == $_GET['clear_log']  
			&& check_admin_referer() ) {
	
			$this->delete_log();
		}
	?>
	  <h3>
		<?php _e('Eventi Registrati', $this->textdomain);?>
		<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=wow-actions-logger&tab='.$this->name.'&clear_log=1' ) ); ?>" class="button-primary right">
		<?php _e( 'Cancella Registrazioni', $this->textdomain) ?>
		</a></h3>
	  <table class="widefat">
		<thead>
		  <tr>
			<th style="width: 150px"><?php _e( 'Timestamp', $this->textdomain) ?></th>
			<th><?php _e( 'Evento', $this->textdomain) ?></th>
			<th><?php _e( 'Utente', $this->textdomain) ?></th>
		  </tr>
		</thead>
		<tbody>
		  <?php 
	$class = '';
	$events=$this->leggi_log();
	foreach ( $events as $event ) {
		$user_data = get_userdata( $event[2] ); 
	?>
		  <tr <?php echo $class ?>>
			<td><?php echo $this->get_nice_time( $event[0] ); ?></td>
			<td><?php echo $event[1]; ?></td>
			<td><?php echo $user_data->display_name; ?></td>
		  </tr>
		  <?php 
		if ( empty( $class ) )  {
			$class = ' class="alternate"';
		} else {
			$class = '';
		}
	
	}
	?>
		</tbody>
	  </table>
	</div>
