<?php 
/**
 * Plugin class
 * Aggiunge l'immagine sulle tassonomie standard e visualizza la colonna
 **/
if( ! class_exists( 'WoW_Standard_Taxonomy_Images' ) ) {
  class WoW_Standard_Taxonomy_Images {
	
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
	public $core_class;
	public $tax;
	public $tax_args;
	  private $only_display_column;
	  private $use_image;
	  private $enable_column_image;
	  private $enable_default_image;
	  private $default_image;
    
    public function __construct($taxonomy, $args, $ref) {
     //
		if ( ! $taxonomy ) return;
		$this->tax=$taxonomy;
		$this->tax_args=$args;
		if ($ref) {
			$this->appRef=$ref;
		}
		extract($this->tax_args);
		$this->use_image=$use_image;
		$this->enable_column_image=$enable_column_image;
		$this->enable_default_image=$enable_default_image;
		$this->default_image=$default_image;
		$this->only_display_column=false;
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		// set class wich set your own thumbnail 
		$core_class=array();
		$core_class[]="Cherry_Core";
		
		foreach($core_class as $k=>$v){
			if (class_exists($v) && $this->tax=='category'){
				$this->only_display_column=true;
				continue;
			}
		}
		$this->init();
    }

    /**
     * Initialize the class and start calling our hooks and filters
     */
	public function init() {
	// Image actions
		if ( ! $this->only_display_column ) {
			add_action( $this->tax.'_add_form_fields', array( $this, 'add_category_image' ), 10, 2 );
			add_action( 'created_'.$this->tax, array( $this, 'save_category_image' ), 10, 2 );
			add_action( $this->tax.'_edit_form_fields', array( $this, 'update_category_image' ), 10, 2 );
			add_action( 'edited_'.$this->tax, array( $this, 'updated_category_image' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
			add_action( 'admin_footer', array( $this, 'add_script' ) );
		}
		if ( $this->enable_column_image ) {
			add_filter( 'manage_edit-'.$this->tax.'_columns', array( $this, 'product_cat_columns' ) );
			add_filter( 'manage_'.$this->tax.'_custom_column', array( $this, 'product_cat_column' ), 10, 3 );
		}
	}

	public function load_media() {
		if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != $this->tax ) {
			return;
		}
		wp_enqueue_media();
	}
 
	/*
	* Add a form field in the new category page
	* @since 1.0.0
	*/
	public function add_category_image ( $taxonomy ) { ?>
	<div class="form-field term-group">
	 <label for="wow-category-image-id"><?php _e('Image', $this->textdomain); ?></label>
	 <input type="hidden" id="wow-category-image-id" name="wow-category-image-id" class="custom_media_url" value="">
	 <div id="wow-category-image-wrapper"></div>
	 <p>
	   <input type="button" class="button button-secondary wow_tax_media_button" id="wow_tax_media_button" name="wow_tax_media_button" value="<?php _e( 'Add Image', $this->textdomain ); ?>" />
	   <input type="button" class="button button-secondary wow_tax_media_remove" id="wow_tax_media_remove" name="wow_tax_media_remove" value="<?php _e( 'Remove Image', $this->textdomain ); ?>" />
	</p>
	</div>
	<?php
	}
 
	/*
	* Save the form field
	* @since 1.0.0
	*/
	public function save_category_image ( $term_id, $tt_id ) {
		if( isset( $_POST['wow-category-image-id'] ) && '' !== $_POST['wow-category-image-id'] ){
		 $image = $_POST['wow-category-image-id'];
		 add_term_meta( $term_id, 'thumbnail_id', $image, true );
		}
	}
 
	/*
	* Edit the form field
	* @since 1.0.0
	*/
	public function update_category_image ( $term, $taxonomy ) { ?>
        <tr class="form-field term-group-wrap">
         <th scope="row">
           <label for="wow-category-image-id"><?php _e( 'Image', $this->textdomain ); ?></label>
         </th>
         <td>
           <?php $image_id = get_term_meta ( $term -> term_id, 'thumbnail_id', true ); ?>
           <input type="hidden" id="wow-category-image-id" name="wow-category-image-id" value="<?php echo $image_id; ?>">
           <div id="wow-category-image-wrapper">
             <?php if ( $image_id ) { ?>
               <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
             <?php } ?>
           </div>
           <p>
             <input type="button" class="button button-secondary wow_tax_media_button" id="wow_tax_media_button" name="wow_tax_media_button" value="<?php _e( 'Add Image', $this->textdomain ); ?>" />
             <input type="button" class="button button-secondary wow_tax_media_remove" id="wow_tax_media_remove" name="wow_tax_media_remove" value="<?php _e( 'Remove Image', $this->textdomain ); ?>" />
           </p>
         </td>
        </tr>
	<?php
	}

	/*
	 * Update the form field value
	 * @since 1.0.0
	 */
	public function updated_category_image ( $term_id, $tt_id ) {
		if( isset( $_POST['wow-category-image-id'] ) && '' !== $_POST['wow-category-image-id'] ){
			 $image = absint( $_POST['wow-category-image-id'] );
			 update_term_meta ( $term_id, 'thumbnail_id', $image );
		} else {
			 update_term_meta ( $term_id, 'thumbnail_id', '' );
		}
	}

	/*
	* Add script
	* @since 1.0.0
	*/
	public function add_script() {
	 if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != $this->tax ) {
	   return;
	 } 
	 ?>
	<script>
	 jQuery(document).ready( function($) {
       _wpMediaViewsL10n.insertIntoPost = '<?php _e( "Insert", $this->textdomain ); ?>';
	   function ct_media_upload(button_class) {
		 var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;
		 $('body').on('click', button_class, function(e) {
		   var button_id = '#'+$(this).attr('id');
		   var send_attachment_bkp = wp.media.editor.send.attachment;
		   var button = $(button_id);
		   _custom_media = true;
		   wp.media.editor.send.attachment = function(props, attachment){
			 if ( _custom_media ) {
			   $('#wow-category-image-id').val(attachment.id);
			   $('#wow-category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
			   $('#wow-category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
			 } else {
			   return _orig_send_attachment.apply( button_id, [props, attachment] );
			 }
			}
		 wp.media.editor.open(button);
		 return false;
	   });
	 }
	 ct_media_upload('.wow_tax_media_button.button'); 
	 $('body').on('click','.wow_tax_media_remove',function(){
	   $('#wow-category-image-id').val('');
	   $('#wow-category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
	 });
	 // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
	 $(document).ajaxComplete(function(event, xhr, settings) {
	   var queryStringArr = settings.data.split('&');
	   if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
		 var xml = xhr.responseXML;
		 $response = $(xml).find('term_id').text();
		 if($response!=""){
		   // Clear the thumb image
		   $('#wow-category-image-wrapper').html('');
		 }
	   }
	 });
	});
	</script>
	<?php
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

			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );
			$cherry_id = get_term_meta( $id, 'cherry_terms_thumbnails', true );
			
			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else if ( $cherry_id ) {
				$image = wp_get_attachment_thumb_url( $cherry_id );
			} else if ($this->enable_default_image){
				$image = $this->tax_placeholder_img_src();
			} else {
				$image = includes_url().'/images/blank.gif';
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
			$file=$this->appRef->file;
		} else {
			$file=__FILE__;
		}
		$th_id=$this->default_image;
		if ($th_id!='') {
			$image = wp_get_attachment_thumb_url( $th_id );
			$image = str_replace( ' ', '%20', $image );
			return apply_filters( 'wow_category_placeholder_img_src', esc_url( $image ) );
		} else {
			return apply_filters( 'wow_category_placeholder_img_src', plugins_url( '/assets/images/placeholder.png', $file ) );
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
//$WoW_Standard_Taxonomy_Images = new WoW_Standard_Taxonomy_Images(only_display_column: true attiva solo la colonna immagine/ false tutta la classe ,parent class);
//$WoW_Standard_Taxonomy_Images->init(); 
}
?>