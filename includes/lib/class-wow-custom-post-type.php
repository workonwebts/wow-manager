<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Custom_Post_Type' ) ) {

class WoW_Custom_Post_Type {

	/**
	 * The name for the custom post type.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $post_type;

	/**
	 * The plural name for the custom post type posts.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $plural;

	/**
	 * The singular name for the custom post type posts.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $single;

	/**
	 * The description of the custom post type.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $description;

	/**
	 * The options of the custom post type.
	 * @var 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public $options;

	/**
	 * The parent object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $appRef;

  	/**
	 * The domain translation
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $textdomain;

	public $has_cats;

	public function __construct ( $post_type = '', $plural = '', $single = '', $description = '', $options = array(), $has_cats=false, $ref = NULL ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		// Post type name and labels
		$this->post_type = $post_type;
		$this->plural = $plural;
		$this->single = $single;
		$this->description = $description;
		$this->options = $options;
		$this->has_cats = false;
		if ($ref) {
			$this->appRef=$ref;
		} else {
			$this->appRef=$this;
		}
		if(isset($this->appRef->basevars)) {
			$this->basevars=$this->appRef->basevars;
		} else {
			$this->basevars='';
		}
		
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		$this->has_cats=$has_cats;

		// Register post type
		if ($ref) {
			$this->register_post_type();
		} else {
			add_action( 'init', array( $this, 'register_post_type' ));
		}
		// add category custom columns to main view
		add_action( "manage_{$this->post_type}_posts_columns" , 'custom_columns', 10, 2 );
		add_action( "manage_{$this->post_type}_posts_custom_column", 'render_columns' , 11, 2 );
		// Display custom update messages for posts edits
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}

	/**
	 * Register new post type
	 * @return void
	 */
	public function register_post_type () {

		$labels = array(
			'name'				 	=> $this->plural,
			'singular_name' 		=> $this->single,
			'menu_name' 			=> $this->plural,
			'name_admin_bar'		=> $this->single,
			'archives'              => sprintf(__( 'Archivio %s', $this->textdomain ), $this->plural),
			'attributes'            => sprintf(__( 'Attributi %s', $this->textdomain ), $this->single),
			'parent_item_colon' 	=> sprintf( __( 'Id Padre %s' , $this->textdomain ), $this->single ),
			'all_items' 			=> sprintf( __( 'Lista %s' , $this->textdomain ), $this->plural ),
			'add_new_item' 			=> sprintf( __( 'Nuovo %s' , $this->textdomain ), $this->single ),
			'add_new' 				=> _x( 'Aggiungi Nuovo', $this->post_type , $this->textdomain ),
			'new_item' 				=> sprintf( __( 'Nuovo %s' , $this->textdomain ), $this->single ),
			'edit_item' 			=> sprintf( __( 'Modifica %s' , $this->textdomain ), $this->single ),
			'update_item'           => sprintf( __( 'Aggiorna %s' , $this->textdomain ), $this->single ),
			'view_item' 			=> sprintf( __( 'Vedi %s' , $this->textdomain ), $this->single ),
			'view_items'            => sprintf( __( 'Vedi %s', $this->textdomain  ), $this->plural),
			'search_items' 			=> sprintf( __( 'Cerca %s' , $this->textdomain ), $this->plural ),
			'not_found' 			=> sprintf( __( 'Nessun %s Trovato' , $this->textdomain ), $this->plural ),
			'not_found_in_trash' 	=> sprintf( __( 'Nessun %s Trovato nel Cestino' , $this->textdomain ), $this->plural ),
			'featured_image'        => __( 'Immagine in Evidenza', $this->textdomain ),
			'set_featured_image'    => __( 'Imposta immagine in evidenza', $this->textdomain ),
			'remove_featured_image' => __( 'Rimuovi Immagine in Evidenza', $this->textdomain ),
			'use_featured_image'    => __( 'Usa questa Immagine come Immagine in Evidenza', $this->textdomain ),
			'insert_into_item'      => __( 'Inserisci nell\'elemento', $this->textdomain ),
			'uploaded_to_this_item' => __( 'Caricato in questo elemento', $this->textdomain ),
			'items_list'            => sprintf( __( 'Lista %s', $this->textdomain ), $this->plural ),
			'items_list_navigation' => sprintf( __( 'Naviga tra i/gli %s', $this->textdomain ), $this->plural ),
			'filter_items_list'     => sprintf( __( 'Filtra %s', $this->textdomain ), $this->plural ),
		);

		$rewrite = array(
			'slug'                  => $this->post_type,
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);

		$args = array(
			'label'                 => sprintf(__( '%s', $this->textdomain ), $this->single),
			'labels' 				=> apply_filters( $this->post_type . '_labels', $labels ),
			'description' 			=> $this->description,
			'supports' 				=> array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
			'hierarchical' 			=> true,
			'taxonomies'            => array(),
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> false,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'menu_position'         => '5',
			'menu_icon'             => '',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus' 	=> true,
			'can_export' 			=> true,
			'has_archive' 			=> true,
			'query_var'             => $this->post_type, // post_type o stringa custom
			'exclude_from_search'   => false,
			'show_in_rest'       	=> true,
	  		'rest_base'          	=> $this->post_type,
	  		'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rewrite' 				=> $rewrite, // false x disabilitare o array x personalizzare
			'capability_type' 		=> 'post',
		);

		$args = array_merge($args, $this->options);

		register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
		if ($this->has_cats) {
			register_taxonomy_for_object_type( get_option($this->basevars.'wow_tax_connect'), $this->post_type );
		}
	}

	/**
	 * Set up admin messages for post type
	 * @param  array $messages Default message
	 * @return array           Modified messages
	 */
	public function updated_messages ( $messages = array() ) {
	  global $post, $post_ID;

	  $messages[ $this->post_type ] = array(
	    0 => '',
	    1 => sprintf( __( '%1$s updated. %2$sView %3$s%4$s.' , $this->textdomain ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
	    2 => __( 'Custom field updated.' , $this->textdomain ),
	    3 => __( 'Custom field deleted.' , $this->textdomain ),
	    4 => sprintf( __( '%1$s updated.' , $this->textdomain ), $this->single ),
	    5 => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s.' , $this->textdomain ), $this->single, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __( '%1$s published. %2$sView %3$s%4s.' , $this->textdomain ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
	    7 => sprintf( __( '%1$s saved.' , $this->textdomain ), $this->single ),
	    8 => sprintf( __( '%1$s submitted. %2$sPreview post%3$s%4$s.' , $this->textdomain ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
	    9 => sprintf( __( '%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s.' , $this->textdomain ), $this->single, '<strong>' . date_i18n( __( 'M j, Y @ G:i' , $this->textdomain ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
	    10 => sprintf( __( '%1$s draft updated. %2$sPreview %3$s%4$s.' , $this->textdomain ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
	  );

	  return $messages;
	}

	/**
	 * Set up bulk admin messages for post type
	 * @param  array  $bulk_messages Default bulk messages
	 * @param  array  $bulk_counts   Counts of selected posts in each status
	 * @return array                Modified messages
	 */
	public function bulk_updated_messages ( $bulk_messages = array(), $bulk_counts = array() ) {

		$bulk_messages[ $this->post_type ] = array(
	        'updated'   => sprintf( _n( '%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], $this->textdomain ), $bulk_counts['updated'], $this->single, $this->plural ),
	        'locked'    => sprintf( _n( '%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], $this->textdomain ), $bulk_counts['locked'], $this->single, $this->plural ),
	        'deleted'   => sprintf( _n( '%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], $this->textdomain ), $bulk_counts['deleted'], $this->single, $this->plural ),
	        'trashed'   => sprintf( _n( '%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], $this->textdomain ), $bulk_counts['trashed'], $this->single, $this->plural ),
	        'untrashed' => sprintf( _n( '%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], $this->textdomain ), $bulk_counts['untrashed'], $this->single, $this->plural ),
	    );

	    return $bulk_messages;
	}

	/**
	 * Define custom columns for products
	 * @param  array $existing_columns
	 * @return array
	 */
	function custom_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
	
		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );
	
		$columns          = array();
		$columns['cb']    = '<input type="checkbox" />';
		$columns['title']  = $this->single;
		if ($this->has_cats) {
			$args=array(
			  'name' => get_option($this->basevars.'wow_tax_connect')
			);
			$output = 'objects'; // or names
			$tax=get_taxonomies($args,$output); 
			$columns[get_option($this->basevars.'wow_tax_connect')]  = $tax->name;
		}
		$columns['author']        = __( 'Autore', $this->textdomain );
		$columns['date']         = __( 'Data Invio', $this->textdomain );
	
		return array_merge( $columns, $existing_columns );
	}

	/**
	 * Ouput custom columns for products
	 * @param  string $column
	 */
	function render_columns( $column ) {
		global $post;
	
		if ( empty( $the_post ) || $the_post->id != $post->ID ) {
			$the_post = get_post( $post );
		}
	
		switch ( $column ) {
			case get_option($this->basevars.'wow_tax_connect') :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					$termlist = array();
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . "&post_type={$this->post_type}" ) . ' ">' . $term->name . '</a>';
					}
	
					echo implode( ', ', $termlist );
				}
				break;
			default :
				break;
		}
	}

}
}
