<?php

function get_sections_base($text) {
	$sec=array();
	// sezioni
	$sec[]=array('base',
		__( 'Generali', $text ),
		__( 'Impostazioni Generali Plugin.', $text )
	);
	return $sec;
}
function get_fields_base($text) {
	$fld=array();
	// campi
	$fld[]=array('base',
			array(
				'id' 			=> 'category_use_image',
				'label'			=> __( 'Immagine su Categoria' , $text ),
				'description'	=> __( 'Abilita Immagine su Categoria.', $text ),
				'type'			=> 'radio',
				'options'		=> array( 'no' => 'Disabilitato', 'yes' => 'Abilitato' ),
				'default'		=> 'no',
				'disabled'		=> 'no',//'yes', // yes , no or function callback that returns yes or no
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('base',
			array(
				'id' 			=> 'category_enable_column_image',
				'label'			=> __( 'Visualizza Colonna Immagine' , $text ),
				'description'	=> __( 'Abilita Visualizzazione Colonna Immagine in Elenco.', $text ),
				'type'			=> 'radio',
				'options'		=> array( '0' => 'Disabilitato', '1' => 'Abilitato' ),
				'default'		=> '0',
				'disabled'		=> 'no',//'yes', // yes , no or function callback that returns yes or no
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('base',
			array(
				'id' 			=> 'category_enable_default_image',
				'label'			=> __( 'Default Img x Categorie' , $text ),
				'description'	=> __( 'Abilita Immagine di Default x le Categorie.', $text ),
				'type'			=> 'radio',
				'options'		=> array( '0' => 'Disabilitato', '1' => 'Abilitato' ),
				'default'		=> '0',
				'disabled'		=> 'no',//'yes', // yes , no or function callback that returns yes or no
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('base',
			array(
				'id' 			=> 'category_default_image',
				'label'			=> __( 'Immagine di Default delle Categorie' , $text ),
				'description'	=> __( "Qui puoi caricare l'immagine di default per le categorie e memorizzare il suo ID. Una volta selezionata o caricata la miniatura apparirà sopra questo bottone.", $text ),
				'type'			=> 'image',
				'disabled'		=> 'no', //'yes', // yes , no or function callback that returns yes or no
				'default'		=> '',
				'placeholder'	=> ''
			)
	);
	$fld[]=array('base',
			array(
				'id' 			=> 'product_enable_default_image',
				'label'			=> __( 'Default Img x Prodotti' , $text ),
				'description'	=> __( 'Abilita Immagine di Default x i Prodotti.', $text ),
				'type'			=> 'radio',
				'options'		=> array( '0' => 'Disabilitato', '1' => 'Abilitato' ),
				'default'		=> '0',
				'disabled'		=> (WoW_is_woocommerce_activated()?'no':'yes'),//'yes', // yes , no or function callback that returns yes or no
				'placeholder'	=> __( '', $text ),
				'callback'		=> ''
			)
	);
	$fld[]=array('base',
			array(
				'id' 			=> 'product_default_image',
				'label'			=> __( 'Immagine di Default dei Prodotti' , $text ),
				'description'	=> __( "Qui puoi caricare l'immagine di default per i prodotti dello shop e memorizzare il suo ID. Una volta selezionata o caricata la miniatura apparirà sopra questo bottone.", $text ),
				'type'			=> 'image',
				'disabled'		=> (WoW_is_woocommerce_activated()?'no':'yes'),//'yes', // yes , no or function callback that returns yes or no
				'default'		=> '',
				'placeholder'	=> ''
			)
	);

	return $fld;
}
?>