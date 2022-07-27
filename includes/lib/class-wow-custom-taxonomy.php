<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Custom_Taxonomy' ) ) {
class WoW_Custom_Taxonomy {

	/**
	 * The name for the taxonomy.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $taxonomy;

	/**
	 * The plural name for the taxonomy terms.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $plural;

	/**
	 * The singular name for the taxonomy terms.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $single;

	/**
	 * The array of post types to which this taxonomy applies.
	 * @var 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public $post_types;

  /**
	 * The array of taxonomy arguments
	 * @var 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public $taxonomy_args;

  /**
	 * The array of capabilities arguments
	 * @var 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public $capabilities_args;

  /**
	 * The array of rewrite arguments
	 * @var 	array or bool
	 * @access  public
	 * @since 	1.0.0
	 */
	public $rewrite_args;

  /**
	 * The domain translation
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $textdomain;

  /**
	 * The filter to apply
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $filter;

  /**
	 * The slug to rewrite url
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $slug;

  /**
	 * The parent object if exist
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $appRef;

	public function __construct ($taxonomy = '', $plural = '', $single = '', $post_types = array(), $tax_args = array(), $filter = 'wp', $slug = '', $ref='' ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		// Post type name and labels
		$this->taxonomy = $taxonomy;
		$this->plural = $plural;
		$this->single = $single;
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		$this->post_types = $post_types;
		$this->taxonomy_args = $tax_args;
		if ($ref) {
			$this->appRef=$ref;
		}
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		$this->filter = $filter;
		if ($slug != '') {
			$this->slug=sanitize_file_name($slug);
		} else {
			$this->slug=sanitize_file_name($this->taxonomy);
		}

		// Register taxonomy
//		add_action('init', array( $this, 'register_taxonomy' ) );
	}

	/**
	 * Register new taxonomy
	 * @return void
	 */
	public function register_taxonomy () {

        $labels = array(
            'name' => $this->plural,
            'singular_name' => $this->single,
            'menu_name' => $this->plural,
            'all_items' => sprintf( __( 'All %s' , $this->textdomain ), $this->plural ),
            'edit_item' => sprintf( __( 'Edit %s' , $this->textdomain ), $this->single ),
            'view_item' => sprintf( __( 'View %s' , $this->textdomain ), $this->single ),
            'update_item' => sprintf( __( 'Update %s' , $this->textdomain ), $this->single ),
            'add_new_item' => sprintf( __( 'Add New %s' , $this->textdomain ), $this->single ),
            'new_item_name' => sprintf( __( 'New %s Name' , $this->textdomain ), $this->single ),
            'parent_item' => sprintf( __( 'Parent %s' , $this->textdomain ), $this->single ),
            'parent_item_colon' => sprintf( __( 'Parent %s:' , $this->textdomain ), $this->single ),
            'search_items' =>  sprintf( __( 'Search %s' , $this->textdomain ), $this->plural ),
            'popular_items' =>  sprintf( __( 'Popular %s' , $this->textdomain ), $this->plural ),
            'separate_items_with_commas' =>  sprintf( __( 'Separate %s with commas' , $this->textdomain ), $this->plural ),
            'add_or_remove_items' =>  sprintf( __( 'Add or remove %s' , $this->textdomain ), $this->plural ),
            'choose_from_most_used' =>  sprintf( __( 'Choose from the most used %s' , $this->textdomain ), $this->plural ),
            'not_found' =>  sprintf( __( 'No %s found' , $this->textdomain ), $this->plural ),
        );

        $args = array(
        	'label' => $this->plural,
        	'labels' => apply_filters( $this->filter.'_labels_'.$this->taxonomy , $labels ),
        	'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'meta_box_cb' => null,
            'show_in_quick_edit' => true,
            'update_count_callback' => '',
            'show_in_rest'          => true,
            'rest_base'             => $this->taxonomy,
            'rest_controller_class' => 'WP_REST_Terms_Controller',
            'query_var' => $this->taxonomy,
            'rewrite' => array(
					'slug'         => _x( $this->slug, 'slug', $this->textdomain ),	//empty( $permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', 'woocommerce' ) : $permalinks['tag_base'],
																				//empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $tax->attribute_name ),
					'with_front'   => true,
					'hierarchical' => true,
				),
            'sort' => '',
        );

        $args = array_merge($args, $this->taxonomy_args);

        register_taxonomy( $this->taxonomy, apply_filters( $this->filter.'_objects_'.$this->taxonomy ,$this->post_types), apply_filters( $this->filter.'_args_'.$this->taxonomy, $args, $this->taxonomy, $this->post_types ) );
		$this->WoW_Custom_Taxonomy_Images = new WoW_Custom_Taxonomy_Images($this->taxonomy,$this);
		add_filter( 'post_type_link', array( $this, 'post_type_link'), 10, 2 );
    }

	public function post_type_link( $permalink, $post ) {
		// Abort if post is not a product.
		if ( ! in_array($post->post_type, $this->post_types) ) {
			return $permalink;
		}
	
		// Abort early if the placeholder rewrite tag isn't in the generated URL.
		if ( false === strpos( $permalink, '%' ) ) {
			return $permalink;
		}
		
		$myTerm=$this->taxonomy;
	
		// Get the custom taxonomy terms in use by this post.
		$terms = get_the_terms( $post->ID, $myTerm );
	
		if ( ! empty( $terms ) ) {
			if ( function_exists( 'wp_list_sort' ) ) {
				$terms = wp_list_sort( $terms, 'term_id', 'ASC' );
			} else {
				usort( $terms, '_usort_terms_by_ID' );
			}
			$category_object = apply_filters( 'wc_product_post_type_link_'.$this->taxonomy, $terms[0], $terms, $post );
			$category_object = get_term( $category_object, $myTerm );
			$product_cat     = $category_object->slug;
	
			if ( $category_object->appRef ) {
				$ancestors = get_ancestors( $category_object->term_id, $myTerm );
				foreach ( $ancestors as $ancestor ) {
					$ancestor_object = get_term( $ancestor, $myTerm );
					$product_cat     = $ancestor_object->slug . '/' . $product_cat;
				}
			}
		} else {
			// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
			$product_cat = sprintf( _x( 'senza-%s', 'slug' , $this->textdomain ), $this->taxonomy );
		}
	
		$find = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			'%post_id%',
			'%category%',
			'%'.$myTerm.'%'
		);
	
		$replace = array(
			date_i18n( 'Y', strtotime( $post->post_date ) ),
			date_i18n( 'm', strtotime( $post->post_date ) ),
			date_i18n( 'd', strtotime( $post->post_date ) ),
			date_i18n( 'H', strtotime( $post->post_date ) ),
			date_i18n( 'i', strtotime( $post->post_date ) ),
			date_i18n( 's', strtotime( $post->post_date ) ),
			$post->ID,
			$product_cat,
			$product_cat
		);
	
		$permalink = str_replace( $find, $replace, $permalink );
	
		return $permalink;
	}
}
}
