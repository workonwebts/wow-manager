<?php

function get_sections($text) {
	$sec=array();
	// sezioni
/*
	$sec[]=array('maintenance',
		__( 'Manutenzione', $text ),
		__( 'Imposta cosa visualizzare durante la manutenzione/aggiornamento del sito.', $text )
	);
*/	return $sec;
}
function get_fields($text) {
	$fld=array();
	// campi
/*	$fld[]=array('maintenance',
		array(
			'id' 			=> 'maintenance_redirect',
			'label'			=> __( 'Redireziona su: ', $text ),
			'description'	=> __( 'Selezionare se redirezionare su una pagina o un indirizzo esterno.', $text ),
			'type'			=> 'radio',
			'placeholder'	=> __( '', $text ),
			'options'		=> array( 'no' => 'Nessuna azione.', 'page' => 'Pagina', 'url' => 'Indirizzo esterno' ),
			'default'		=> 'no'
		)
	);
*/	return $fld;
}

/*
		$settings['standard'] = array(
			'title'					=> __( 'Standard', $text ),
			'description'			=> __( 'These are fairly standard form input fields.', $text ),
			'fields'				=> array(
				array(
					'id' 			=> 'text_field',
					'label'			=> __( 'Some Text' , $text ),
					'description'	=> __( 'This is a standard text field.', $text ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', $text )
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , $text ),
					'description'	=> __( 'This is a standard password field.', $text ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', $text )
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , $text ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', $text ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', $text )
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , $text ),
					'description'	=> __( 'This is a standard text area.', $text ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', $text )
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', $text ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', $text ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', $text ),
					'description'	=> __( 'A standard select box.', $text ),
					'type'			=> 'select',
					'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
					'default'		=> 'wordpress'
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', $text ),
					'description'	=> __( 'A standard set of radio buttons.', $text ),
					'type'			=> 'radio',
					'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
					'default'		=> 'batman'
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', $text ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', $text ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
					'default'		=> array( 'circle', 'triangle' )
				)
			)
		);

		$settings['extra'] = array(
			'title'					=> __( 'Extra', $text ),
			'description'			=> __( 'These are some extra input fields that maybe aren\'t as common as the others.', $text ),
			'fields'				=> array(
				array(
					'id' 			=> 'number_field',
					'label'			=> __( 'A Number' , $text ),
					'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', $text ),
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '42', $text )
				),
				array(
					'id' 			=> 'colour_picker',
					'label'			=> __( 'Pick a colour', $text ),
					'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', $text ),
					'type'			=> 'color',
					'default'		=> '#21759B'
				),
				array(
					'id' 			=> 'an_image',
					'label'			=> __( 'An Image' , $text ),
					'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', $text ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'multi_select_box',
					'label'			=> __( 'A Multi-Select Box', $text ),
					'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', $text ),
					'type'			=> 'select_multi',
					'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
					'default'		=> array( 'linux' ),
					'disabled'      => 'no' // usare 'yes', 'no' o una funzione callback che restituisca yes o no con la sintassi array(nome_funzione_callbck, [param|array argomenti_callback])
				)
			)
		);
*/
?>