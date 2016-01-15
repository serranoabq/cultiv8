jQuery(document).ready( function($) {
	check_mbs( $('#page_template').val() );
	
	$('#page_template').change( function(){
		check_mbs( $(this).val() );
	});
	function check_mbs( cur_template ) {
	$('.tf-form-table').each(function(){
		var templates = $(this).data('template');
		if ( templates != '' ) {
			templates = templates.split(',');
			if ( templates.indexOf( cur_template ) == -1 ) {
				$(this).parents('.postbox').addClass( 'hidden' );
			} else {
				$(this).parents('.postbox').removeClass('hidden');
			}
		} 
	});
}
});

