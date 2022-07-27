<?php 
/**
 * Plugin class
 **/
if( ! class_exists( 'WoW_Cherry_Taxonomy_Images' ) ) {
  class WoW_Cherry_Taxonomy_Images {
	
	public $tax;
	public $display_cherry_column;
	/**
	* The parent object if exist
	* @var 	object
	* @access  public
	* @since 	1.0.0
	*/
	public $appRef;
	/**
	* The textdomain if exist
	* @var 	string
	* @access  public
	* @since 	1.0.0
	*/
	public $textdomain;
    
    public function __construct($display_cherry_column, $ref) {
     //
		if ( ! $display_cherry_column ) return;
		$this->display_cherry_column=$display_cherry_column;
		$this->tax='category';
		if ($ref) {
			$this->appRef=$ref;
		}
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		if ( $this->display_cherry_column ) {
			$this->init();
		}
    }

    /**
     * Initialize the class and start calling our hooks and filters
     */
	public function init() {
	// Image actions
//		add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
		add_filter( 'manage_edit-'.$this->tax.'_columns', array( $this, 'product_cat_columns' ) );
		add_filter( 'manage_'.$this->tax.'_custom_column', array( $this, 'product_cat_column' ), 10, 3 );
	}

	public function load_media() {
		if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != $this->tax ) {
			return;
		}
		wp_enqueue_media();
	}
 
 
	/**
	 * Thumbnail column added to taxonomy admin.
	 *
	 * @param mixed $columns
	 * @return array
	 */
	public function product_cat_columns( $columns ) {
		$new_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['thumb'] = __( 'Image', $this->textdomain );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to taxonomy admin.
	 *
	 * @param string $columns
	 * @param string $column
	 * @param int $id
	 *
	 * @return string
	 */
	public function product_cat_column( $columns, $column, $id ) {

		if ( 'thumb' == $column ) {

			$thumbnail_id = get_term_meta( $id, 'cherry_terms_thumbnails', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = $this->tax_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: https://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', $this->textdomain ) . '" class="wp-post-image" height="48" width="48" />';

		}

		return $columns;
	}
	
	/**
	 * Get the placeholder image URL for products etc.
	 *
	 * @access public
	 * @return string
	 */
	function tax_placeholder_img_src() {
		if ($this->appRef) {
			$th_id=get_option($this->appRef->basevars.$this->tax.'_default_image');
			if (WoW_is_disabled_image_category($this->tax)=='no' && $th_id!='') {
				$image = wp_get_attachment_thumb_url( $th_id );
				$image = str_replace( ' ', '%20', $image );
				return apply_filters( 'wow_category_placeholder_img_src', esc_url( $image ) );
			} else {
		//		return apply_filters( 'wow_category_placeholder_img_src', plugins_url( '/../assets/images/placeholder.png', dirname(__FILE__) ) );
				return apply_filters( 'wow_category_placeholder_img_src', plugins_url( '/assets/images/placeholder.png', $this->appRef->file ) );
			}
		} else {
			return apply_filters( 'wow_category_placeholder_img_src', plugins_url( '/assets/images/placeholder.png', __FILE__ ) );
		}
	}
	
	/**
	 * Get the placeholder image.
	 *
	 * @access public
	 *
	 * @param string $size Image size.
	 *
	 * @return string
	 */
	function tax_placeholder_img( $size = 'woocommerce_thumbnail' ) {
		$dimensions = wc_get_image_size( $size );
	
		return apply_filters( 'wow_category_placeholder_img', '<img src="' . $this->tax_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', $this->textdomain ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="woocommerce-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
	}

  }
//$WoW_Cherry_Taxonomy_Images = new WoW_Cherry_Taxonomy_Images(display_cherry_column: true attiva la colonna immagine cherry/ false non attiva ,parent class);
//$WoW_Cherry_Taxonomy_Images->init(); 
}
?>