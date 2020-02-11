(function($) {

	$('.cbbb-cookie-check button').on("click", function() {
		Cookies.set('cbbb_cookie', 'closed', { expires: 30 });

		// if($("#cb2").prop('checked') == true){
		// 	Cookies.set('analyticcheck', 'agreed', { expires: 365 });
		// }
		// if($("#cb2").prop('checked') == false){
		// 	Cookies.remove('analyticcheck');
		// }

		$(".cbbb-cookie-check").toggleClass('closed');
		$(".cbbb-cookie-icon").toggleClass('show');

	});

	$('.cbbb-cookie-icon').on("click", function() {
		$(".cbbb-cookie-check").toggleClass('closed');
		$(".cbbb-cookie-icon").toggleClass('show');
	});

})(jQuery);
