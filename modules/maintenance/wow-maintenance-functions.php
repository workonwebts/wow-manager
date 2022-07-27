<?php
	function WoW_fill_maintenance_to_array($pref='wowbs_') {
		$opt=array();
		$opt['maintenance_redirect']=get_option($pref.'maintenance_redirect');
		$opt['redirect_page']=get_option($pref.'redirect_page');
		$opt['redirect_url']=get_option($pref.'redirect_url');
		$opt['maintenance_enabled']=get_option($pref.'maintenance_enabled');
		return $opt;
	}
	function WoW_is_enabled_page_url($pref='wowbs_') {
		$is_page=get_option($pref.'maintenance_redirect');
		return (($is_page!='page' && $is_page!='url')?'no':'yes');
	}
	function WoW_is_enabled_page($pref='wowbs_') {
		$is_page=get_option($pref.'maintenance_redirect');
//		if(WoW_is_enabled_page_url()=='no') return 'yes';
		return ($is_page=='page'?'yes':'no');
	}
	function WoW_is_enabled_url($pref='wowbs_') {
		$is_url=get_option($pref.'maintenance_redirect');
//		if(WoW_is_enabled_page_url()=='no') return 'yes';
		return ($is_url=='url'?'yes':'no');
	}

?>
