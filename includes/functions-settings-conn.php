<?php

function get_sections_conn($text) {
	$sec=array();
	// sezioni
	$sec[]=array('conn',
		__( 'Dati Connessione DB Est.', $text ),
		__( 'Inserire le impostazioni di connessione per il DB esterno.', $text )
	);
	return $sec;
}
function get_fields_conn($text) {
	$fld=array();
	// connessione db
	$fld[]=array('conn',
			array(
				'id' 			=> 'store_url',
				'label'			=> __( 'URL Store' , $text ),
				'description'	=> __( 'URL dello Store da importare.', $text ),
				'type'			=> 'text',
				'default'		=> '',
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('conn',
			array(
				'id' 			=> 'store_host',
				'label'			=> __( 'Host DB' , $text ),
				'description'	=> __( 'URL Host del DB da importare.', $text ),
				'type'			=> 'text',
				'default'		=> '',
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('conn',
			array(
				'id' 			=> 'store_user',
				'label'			=> __( 'Nome Utente' , $text ),
				'description'	=> __( 'Nome Utente del DB.', $text ),
				'type'			=> 'text',
				'default'		=> '',
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('conn',
			array(
				'id' 			=> 'store_pass',
				'label'			=> __( 'Password' , $text ),
				'description'	=> __( 'Password Utente del DB.', $text ),
				'type'			=> 'password',
				'default'		=> '',
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('conn',
			array(
				'id' 			=> 'store_dbname',
				'label'			=> __( 'Nome DB' , $text ),
				'description'	=> __( 'Nome DB da importare.', $text ),
				'type'			=> 'text',
				'default'		=> '',
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	return $fld;
}
?>