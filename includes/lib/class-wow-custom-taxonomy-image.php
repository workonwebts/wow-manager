<?php 
if( ! class_exists( 'WoW_Custom_Taxonomy_Images' ) ) {
  class WoW_Custom_Taxonomy_Images {
	
	public $tax;
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
    
    public function __construct($taxonomy, $ref) {
     //
		if ( ! $taxonomy ) return;
		$this->tax=$taxonomy;
		if ($ref) {
			$this->appRef=$ref;
		}
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		$this->init();
    }

    /**
     * Initialize the class and start calling our hooks and filters
     */
     public function init() {
     // Image actions
     add_action( $this->tax.'_add_form_fields', array( $this, 'add_category_image' ), 10, 2 );
     add_action( 'created_'.$this->tax, array( $this, 'save_category_image' ), 10, 2 );
     add_action( $this->tax.'_edit_form_fields', array( $this, 'update_category_image' ), 10, 2 );
     add_action( 'edited_'.$this->tax, array( $this, 'updated_category_image' ), 10, 2 );
     add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
     add_action( 'admin_footer', array( $this, 'add_script' ) );
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
    * Add a form field in the new category page
    * @since 1.0.0
    */
  
   public function add_category_image( $taxonomy ) { ?>
     <div class="form-field term-group">
       <label for="wow-taxonomy-image-id"><?php _e( 'Image', $this->textdomain ); ?></label>
       <input type="hidden" id="wow-taxonomy-image-id" name="wow-taxonomy-image-id" class="custom_media_url" value="">
       <div id="wow-taxonomy-image-wrapper"></div>
       <p>
         <input type="button" class="button button-secondary wow_tax_media_button" id="wow_tax_media_button" name="wow_tax_media_button" value="<?php _e( 'Add Image', $this->textdomain ); ?>" />
         <input type="button" class="button button-secondary wow_tax_media_remove" id="wow_tax_media_remove" name="wow_tax_media_remove" value="<?php _e( 'Remove Image', $this->textdomain ); ?>" />
       </p>
     </div>
   <?php }

	/**
	* Save the form field
	* @since 1.0.0
	*/
	public function save_category_image( $term_id, $tt_id ) {
		if( isset( $_POST['wow-taxonomy-image-id'] ) && '' !== $_POST['wow-taxonomy-image-id'] ){
			add_term_meta( $term_id, 'thumbnail_id', absint( $_POST['wow-taxonomy-image-id'] ), true );
		}
	}

    /**
     * Edit the form field
     * @since 1.0.0
     */
    public function update_category_image( $term, $taxonomy ) { ?>
      <tr class="form-field term-group-wrap">
        <th scope="row">
          <label for="wow-taxonomy-image-id"><?php _e( 'Image', $this->textdomain ); ?></label>
        </th>
        <td>
          <?php $image_id = get_term_meta( $term->term_id, 'thumbnail_id', true ); ?>
          <input type="hidden" id="wow-taxonomy-image-id" name="wow-taxonomy-image-id" value="<?php echo esc_attr( $image_id ); ?>">
          <div id="wow-taxonomy-image-wrapper">
            <?php if( $image_id ) { ?>
              <?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
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

	/**
	* Update the form field value
	* @since 1.0.0
	*/
	public function updated_category_image( $term_id, $tt_id ) {
		if( isset( $_POST['wow-taxonomy-image-id'] ) && '' !== $_POST['wow-taxonomy-image-id'] ){
			$image = absint( $_POST['wow-taxonomy-image-id'] );
			update_term_meta( $term_id, 'thumbnail_id', $image );
		} else {
			update_term_meta( $term_id, 'thumbnail_id', '' );
		}
	}
 
   /**
    * Enqueue styles and scripts
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
             if( _custom_media ) {
               $('#wow-taxonomy-image-id').val(attachment.id);
               $('#wow-taxonomy-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
               $( '#wow-taxonomy-image-wrapper .custom_media_image' ).attr( 'src',attachment.url ).css( 'display','block' );
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
         $('#wow-taxonomy-image-id').val('');
         $('#wow-taxonomy-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
       });
       // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
       $(document).ajaxComplete(function(event, xhr, settings) {
         var queryStringArr = settings.data.split('&');
         if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
           var xml = xhr.responseXML;
           $response = $(xml).find('term_id').text();
           if($response!=""){
             // Clear the thumb image
             $('#wow-taxonomy-image-wrapper').html('');
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
			$aTax=explode('_', $this->tax);
			$th_id=get_option($this->appRef->basevars.'tax_'.$aTax[1].'_default_image');
			if (WoW_is_disabled_image_taxonomy($aTax[1])=='no' && $th_id!='') {
				$image = wp_get_attachment_thumb_url( $th_id );
				$image = str_replace( ' ', '%20', $image );
				return apply_filters( 'wow_tax_placeholder_img_src', esc_url( $image ) );
			} else {
		//		return apply_filters( 'wow_tax_placeholder_img_src', plugins_url( '/../assets/images/placeholder.png', dirname(__FILE__) ) );
				return apply_filters( 'wow_tax_placeholder_img_src', plugins_url( '/assets/images/placeholder.png', $this->appRef->appRef->file ) );
			}
		} else {
			return apply_filters( 'wow_tax_placeholder_img_src', plugins_url( '/assets/images/placeholder.png', __FILE__ ) );
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
	
		return apply_filters( 'wow_tax_placeholder_img', '<img src="' . $this->tax_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', $this->textdomain ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="woocommerce-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
	}

  }
//$WoW_Custom_Taxonomy_Images = new WoW_Custom_Taxonomy_Images('my-custom-taxonomy',parent class);
//$WoW_Custom_Taxonomy_Images->init(); 
}
?>