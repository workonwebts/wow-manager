<?php 
	
define('CHARSET', 'ISO-8859-1');
define('REPLACE_FLAGS', 'ENT_QUOTE | ENT_XHTML');

	function sanitize_func($nome) {
		return str_replace('-','_',$nome);
	}

	function sanitize_tax($nome) {
		return str_replace('_','-',$nome);
	}
	
	function html($string) {
		return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
	}
	
	function WoW_get_terms_namelike($tx='', $t='') {
		if ($tx=='' || $t=='') { 
			$aterm = array();
		} else {
			$aterm=get_terms(array(
				'taxonomy' => $tx,
				'name__like' => $t,
				));
		}
		return $aterm;
	}
	
	 // load role names
	function WoW_load_rolenames() {
		$newroles=array();
		$newroles = wp_roles()->get_names();
		return ($newroles);
	}
	 // filter role editable
	function WoW_filter_role($roles,$noRole) {
		$newroles=array();
		foreach($roles as $slug=>$role) {
			if (! in_array($slug,$noRole)) $newroles[$slug]=translate_user_role($role);
		}
		return ($newroles);
	}

	function WoW_is_nonvalid_taxonomy_for_image($tax) {
		$btax=WoW_get_taxonomy($tax);
		if ($btax) {
			$ctax=str_replace($tax,"",$btax);
			if ($ctax=="product_") return 'no';
		}
		return 'yes';
	}
	function WoW_is_disabled_image_taxonomy($tax) {
		$is_tax=get_option('wowbs_tax_'.$tax.'_use_image');
		if (WoW_is_nonvalid_taxonomy_for_image($tax)=='no' && $is_tax=='yes') { 
			return 'no';
		}
		return 'yes';
	}
	function WoW_is_disabled_image_category($tax) {
		$is_tax=get_option('wowbs_'.$tax.'_use_image');
		if ($is_tax=='yes') { 
			return 'no';
		}
		return 'yes';
	}

	if (! function_exists("WoW_popAdminMessage")) {
		function WoW_popAdminMessage($msg, $tipo=WOW_MANAGER_MSG_STATUSINFO, $dismiss=true){
			echo '<div id="WoWmessage" class="notice notice-'.$tipo.($dismiss==true?' is-dismissible':'').'">';
			echo '<p>'. $msg .'</p>';
			echo '</div>';
		}
	}
	
	function WoW_fill_conndata_to_array($base='wowbs_') {
		$opt=array();
		$opt['store_user']=get_option($base.'store_user');
		$opt['store_pass']=get_option($base.'store_pass');
		$opt['store_dbname']=get_option($base.'store_dbname');
		$opt['store_host']=get_option($base.'store_host');
		$opt['store_url']=get_option($base.'store_url');
		return $opt;
	}
	
	function WoW_fill_gpsamain_to_array($base='wowbs_') {
		$opt=array();
		$opt['category_use_image']=get_option($base.'category_use_image');
		$opt['category_enable_column_image']=get_option($base.'category_enable_column_image');
		$opt['category_enable_default_image']=get_option($base.'category_enable_default_image');
		$opt['category_default_image']=get_option($base.'category_default_image');
		$opt['product_enable_default_image']=get_option($base.'product_enable_default_image');
		$opt['product_default_image']=get_option($base.'product_default_image');
		return $opt;
	}
	
	function WoW_get_taxonomy_config($base='wowbs_',$tax='category') {
		$opt=array();
		$opt['use_image']=get_option($base.$tax.'_use_image');
		$opt['enable_column_image']=get_option($base.$tax.'_enable_column_image');
		$opt['enable_default_image']=get_option($base.$tax.'_enable_default_image');
		$opt['default_image']=get_option($base.$tax.'_default_image');
		return $opt;
	}
	
	function WoW_get_product_config($base='wowbs_') {
		$opt=array();
		$opt['product_enable_default_image']=get_option($base.'product_enable_default_image');
		$opt['product_default_image']=get_option($base.'product_default_image');
		return $opt;
	}
	
	function WoW_get_pages() {
		$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 0,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => 0,
			'exclude_tree' => 1,
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		$pages = get_pages($args);
		$aPage=array();
		$aPage[0] =  "** Seleziona **";
		foreach ( $pages as $page ) {
			$aPage[$page->ID] =  $page->post_title;
		}
		return $aPage;
  	}

	function WoW_connection() {
		global $wowdb;
		$success=false;
		if($wowdb->ready){
			$success=true;
		}
		return ($success);
	}
	
	function wow_array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}

	function wow_filter_gpm($array){
		$mk = array();
		foreach($array as $k => $v){
			if(is_array($v) && count($v) == 1){
				$mk = wow_array_push_assoc($mk, $k, $v[0]);
			} else {
				$mk = wow_array_push_assoc($mk, $k, $v);
			}
		}
		return $mk;
	}

	function get_date_format($date) { 
		list($year,$month,$day) = explode("-",$date);
		return "$day/$month/$year";
	}

	function debug_var($v) { echo '<pre>'.print_r($v,true).'</pre>'; }

	function is_wow($val,$key,$flt) {
		$pos = strpos($key,$flt);
		if ($pos === false) {
		} else {
			return (array($key=>$val));
		}
	}
	
	if ( ! function_exists( 'WoW_is_woocommerce_activated' ) ) {
		function WoW_is_woocommerce_activated() {
			return class_exists( 'woocommerce' ) ? true : false;
		}
	}
	
?>
<?php
if ( !function_exists( "GetSQLValueString" ) ) {
	function WoW_GetSQLValueString( $theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "" ) {
		if ( PHP_VERSION < 6 ) {
			$theValue = get_magic_quotes_gpc() ? stripslashes( $theValue ) : $theValue;
		}

		//  $theValue = function_exists("mysql_real_escape_string") ? mysqli_real_escape_string($theValue) : mysqli_escape_string($theValue);

		switch ( $theType ) {
			case "text":
				$theValue = ( $theValue != "" ) ? "'" . $theValue . "'": "NULL";
				break;
			case "long":
			case "int":
				$theValue = ( $theValue != "" ) ? intval( $theValue ) : "NULL";
				break;
			case "double":
				$theValue = ( $theValue != "" ) ? doubleval( $theValue ) : "NULL";
				break;
			case "date":
				$theValue = ( $theValue != "" ) ? "'" . $theValue . "'": "NULL";
				break;
			case "defined":
				$theValue = ( $theValue != "" ) ? $theDefinedValue : $theNotDefinedValue;
				break;
		}
		return $theValue;
	}
}

	function WoW_get_post_by_id($id) {
		$exist_page = get_post($id);
		if (is_null($exist_page)) {
			$exist_page=new WP_Error( 'error', sprintf( 'Elemento con ID:<%s> non &egrave; presente in archivio!', $id ) );
		}
		return ($exist_page);
	}

	function WoW_get_post_by_meta($meta, $idsku, $type='post', $status='any') {
		$args = array(
					'post_type' => $type,
					'posts_per_page' => 1,
					'post_status' => $status,
					'meta_query' => array(
						array(
							'key' => $meta,
							'value' => $idsku,
							)
					));
		$exist_page = get_posts($args);
		if( ( ! $exist_page ) ) {
			$exist_page=new WP_Error( 'error', sprintf( 'Articolo con Campo: %s e Codice: %s non &egrave; presente in archivio!', $meta, $idsku ) );
		}
		return ($exist_page);
	}

	function WoW_get_post_by_sku($idsku, $status='any') {
		$args = array(
					'post_type' => 'product',
					'posts_per_page' => 1,
					'post_status' => $status,
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $idsku,
							)
					));
		$exist_page = get_posts($args);
		if( ( ! $exist_page ) ) {
			$exist_page=new WP_Error( 'error', sprintf( 'Il Prodotto con Codice:<%s> non &egrave; presente in archivio!', $idsku ) );
		}
		return ($exist_page);
	}

	function WoW_get_post_by_slug($the_slug, $status='publish') {
		$args = array(
			'name'           => $the_slug,
			'post_type'      => 'product',
			'post_status'    => $status,
			'posts_per_page' => 1
		);
		$exist_page = get_posts($args);
		if( ( ! $exist_page ) ) {
			$exist_page=new WP_Error( 'error', sprintf( 'Il Prodotto con Slug:<%s> non &egrave; presente in archivio!', $idsku ) );
		}
		return ($exist_page);
	}

	function WoW_get_posts_by_array_sku($aSku=array(),$status='any') {
		$skus=implode(',',$aSku);
		$args = array(
					'post_type' => 'product',
					'posts_per_page' => -1,
					'post_status' => $status,
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $skus,
							'compare' => 'IN'
							)
					));
		$exist_page = get_posts($args);
		if( ( ! $exist_page ) ) {
			$exist_page=new WP_Error( 'error', sprintf( 'I Prodotti con Codice:<%s> non sono presenti in archivio!', $skus ) );
		}
		return ($exist_page);
	}

	function WoW_get_posts_by_array_meta($meta, $aSku=array(), $type='post', $status='any') {
		$skus=implode(',',$aSku);
		$args = array(
					'post_type' => $type,
					'posts_per_page' => -1,
					'post_status' => $status,
					'meta_query' => array(
						array(
							'key' => $meta,
							'value' => $skus,
							'compare' => 'IN'
							)
					));
		$exist_page = get_posts($args);
		if( ( ! $exist_page ) ) {
			$exist_page=new WP_Error( 'error', sprintf( 'Articolo con Campo: %s e Codice: %s non &egrave; presente in archivio!', $meta, $skus ) );
		}
		return ($exist_page);
	}
//
// DoFormatCurrency
// formatta una cifra in formato valuta e aggiunge il simbolo prima o dopo
// parameters:
// $theObject: number to format
// $NumDigitsAfterDecimal: numero decimali 
// $DecimalSeparator: separatore decimali
// $GroupDigits: separatore migliaia
// $CurrencySymbol: simbolo valuta
// $SymbolBefore: true/false simbolo prima del numero o dopo
// 
// Usage: DoFormatCurrency($price, 2, ',', '.', '€ ',true);
	if (!function_exists("DoFormatCurrency")) {
		function DoFormatCurrency($theObject,$NumDigitsAfterDecimal,$DecimalSeparator,$GroupDigits,$CurrencySymbol,$SymbolBefore) { 
			if ($SymbolBefore) {
				$currencyFormat=$CurrencySymbol.'&nbsp;'.number_format($theObject,$NumDigitsAfterDecimal,$DecimalSeparator,$GroupDigits);
			} else {
				$currencyFormat=number_format((float)$theObject,$NumDigitsAfterDecimal,$DecimalSeparator,$GroupDigits).'&nbsp;'.$CurrencySymbol;
			}
			return ($currencyFormat);
		}
	}

?>
