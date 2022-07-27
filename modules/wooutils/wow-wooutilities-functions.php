<?php
	function WoW_fill_delprod_to_array($pref='wowbs_') {
		$opt=array();
		$opt['delete_product_enable']=get_option($pref.'delete_product_enable');
		$opt['delete_product_menu']=get_option($pref.'delete_product_menu');
		$opt['delete_product_log_enable']=get_option($pref.'delete_product_log_enable');
		return $opt;
	}
    /**
     * Checks if product download is permitted.
     *
     * @return bool
     */
    function WoW_custom_download_permitted() {
		return apply_filters('wow_wc_custom_download_permitted',wc_get_order_statuses());
    }

?>
