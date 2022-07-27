<?php

/**
 *
 * This class defines all code necessary to create custom product attributes. and permalink for public archive
 *
 * @since      1.0.0
 * @package    WoW_Product_Attribute
 * @param string nome -> nome o slug attributo
 * @param string etichetta -> label attributo
 * @param bool pubblico -> true attributo con archivio/false solo visualizzazione
 * Uso:
 * $var = new WoW_Product_Attribute(nome,etichetta, true or false)
 * $array_of_meta[]=$var->create_attribute();
 * @author     Andrea Starz
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Product_Attribute' ) ) {
class WoW_Product_Attribute {
	
	/**
	* variabile nome attributo
	**/
	public $name;
	
	/**
	* variabile etichetta attributo
	**/
	public $label;
	
	/**
	* tipo selezione
	**/
	public $type; // select/text
	
	/**
	* ordinamento
	**/
	public $orderby; // menu_order/name/name_num/id
	
	/**
	* archivio pubblico
	**/
	public $ispublic;
	
	/**
	* array impostazioni attributo
	**/
	public	$attribute;
	
	/**
	* bool verifica attributo
	**/
	public	$isvalid;
	
	/**
	* bool attributo esistente
	**/
	public	$isexist;
	
	/**
	* array valori attributo x meta prodotto
	**/
	public $errors;
	
	/**
	* obj parent class
	**/
	public $appRef;
	
	/**
	* textdomain
	**/
	public $text;

	/**
	 * Constructor function
	 * @param string $nome nome attibuto
	 * @param string $label etichetta attributo
	 * @param bool $bPubblico archivio pubblico/linkabile o solo visualizz. 
	 */
	public function __construct ($nome='', $label='', $bPubblico=false, $ref='') {
		$this->name = $nome;
		$this->label= $label;
		$this->ispublic= $bPubblico;
		$this->isvalid=false;
		$this->isexist=false;
		$this->errors=array();
		if ($ref) {
			$this->appRef=$ref;
		}
		if(isset($this->appRef->textdomain)) {
			$this->text=$this->appRef->textdomain;
		} else {
			$this->text=sanitize_file_name(basename(__FILE__,'.php'));
		}
	}

	/**
	 * Fill Attribute
	 * @
	 */
	public function fill_attribute() {
	
		if ( empty( $this->type ) ) {
			$this->type = 'select';
		}
		if (empty($this->orderby ) ) { 
			$this->orderby = 'menu_order';
		}
		if ( empty( $this->label ) ) {
			$this->label = ucfirst( $this->name );
		}
		if ( empty( $this->name ) ) {
			$this->name = wc_sanitize_taxonomy_name( $this->label );
		} else {
			$this->name = wc_sanitize_taxonomy_name( $this->name );
		}
		if (empty($this->ispublic ) ) { 
			$this->ispublic = false;
		}
		$this->attribute = array(
			'attribute_label'   => '',
			'attribute_name'    => '',
			'attribute_type'    => '',
			'attribute_orderby' => '',
			'attribute_public'  => ''
		);
		$this->isvalid=$this->check_attribute_name( );
		if ($this->isvalid) {
			$this->attribute['attribute_name'] = $this->name; 
			$this->attribute['attribute_label'] = $this->label;
			$this->attribute['attribute_type'] = $this->type; 
			$this->attribute['attribute_orderby'] = $this->orderby;
			$this->attribute['attribute_public'] = $this->ispublic;
/*		} else {
			wp_die($this->isvalid);
*/		}
	}

	/**
	 * Create Attribute
	 * @
	 */
	public function create_attribute() {
		$this->fill_attribute();
		if ($this->isvalid && ! $this->isexist) {
			$this->process_add_attribute();
		}
		if ($this->isvalid && $this->ispublic) {
			add_filter( 'post_type_link', array( $this, 'attr_post_type_link'), 10, 2 );
		}
		return $this->errors;
	}


	/**
	 * See if an attribute name is valid.
	 * @return bool|WP_error result
	 */
	public function check_attribute_name( ) {
		if ( empty( $this->name ) || empty( $this->label ) ) {
				$this->errors[] = new WP_Error( 'error', __( 'Please, provide an attribute name and slug.', 'woocommerce' ) );
				return false;
		} elseif ( ( $valid_attribute_name = $this->valid_attribute_name( ) ) && is_wp_error( $valid_attribute_name ) ) {
				$this->errors[] = $valid_attribute_name;
				return false;
		} elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $this->name ) ) ) {
				$this->errors[] = new WP_Error( 'error', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'woocommerce' ), sanitize_title( $this->name ) ) );
				$this->isexist = true;
		}
		return true;
	}

	/**
	 * See if an attribute name is valid.
	 * @return bool|WP_error result
	 */
	public function valid_attribute_name( ) {
		if ( strlen( $this->name ) >= 28 ) {
			return new WP_Error( 'error', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), sanitize_title( $this->name ) ) );
		} elseif ( wc_check_if_attribute_name_is_reserved( $this->name ) ) {
			return new WP_Error( 'error', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $this->name ) ) );
		}
		return true;
	}

	/**
	 * Set type
	 */
	public function set_type($tp) {
		$this->type=$tp;
	}
	/**
	 * Set order
	 */
	public function set_order($ord) {
		$this->orderby=$ord;
	}
	
	/**
	 * Add an attribute.
	 * @return bool|WP_Error
	*/
	private function process_add_attribute()
	{
		global $wpdb;
	
		$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $this->attribute );
		
		do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $this->attribute );
	
		flush_rewrite_rules();
		delete_transient( 'wc_attribute_taxonomies' );
	
		return true;
	}

	/**
	 * Filter to allow product_author in the permalinks for products.
	 *
	 * @param  string  $permalink The existing permalink URL.
	 * @param  WP_Post $post
	 * @return string
	 */
	function attr_post_type_link( $permalink, $post ) {
		// Abort if post is not a product.
		if ( $post->post_type !== 'product' ) {
			return $permalink;
		}
	
		// Abort early if the placeholder rewrite tag isn't in the generated URL.
		if ( false === strpos( $permalink, '%' ) ) {
			return $permalink;
		}
		
		$myTerm='pa_'.$this->name;
	
		// Get the custom taxonomy terms in use by this post.
		$terms = get_the_terms( $post->ID, $myTerm );
	
		if ( ! empty( $terms ) ) {
			if ( function_exists( 'wp_list_sort' ) ) {
				$terms = wp_list_sort( $terms, 'term_id', 'ASC' );
			} else {
				usort( $terms, '_usort_terms_by_ID' );
			}
			$category_object = apply_filters( 'wc_product_post_type_link_product_'.$this->name, $terms[0], $terms, $post );
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
			$product_cat = _x( 'senza-'.$this->name, 'slug', $this->text );
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
?>