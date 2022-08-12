/**
 * Script for "Woocommerce Prices" Wordpress plugin
 */

(function($){

	$(document).ready(function() {
	
		$('.loadvars-buttons').on('click', function() {
			var id=this.id;
			$('#result_'+id).empty();
			var data = {
				action: 'get_var_dump',
				nonce: $('#wowdebug_nonce').val(),
				glob_var: id
			};
			$.post(ajaxurl, data, function(response) {
				$('#result_'+id).append(response);
			});
		});
		
		jQuery("#res").tabs();

	});

})(jQuery);