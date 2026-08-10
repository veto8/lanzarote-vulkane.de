jQuery(document).ready(function ($) {

    if (window.matchMedia('(min-width: 992px)').matches) {

		var $body = $("body");
		if ($body.hasClass('blog') || $body.hasClass('archive')  || $body.hasClass('search')) {

		$.ajaxSetup({cache:false});
			var post_link = $(".post-list .single-post-list:first a.trick").attr("rel");
            $("#single-home-container").html("<div class='loading-text'>" + inbox_data.inbox_load_string + "</div>");
            $("#single-home-container").load( post_link + " #single-post-pull");
        return false;
		
		}		
		
	}
		
});
jQuery(document).ready(function ($) {
								 
    if (window.matchMedia('(min-width: 992px)').matches) {
		
		var $body = $("body");
		if ($body.hasClass('blog') || $body.hasClass('archive')  || $body.hasClass('search')) {

       $.ajaxSetup({cache:false});
        $(".trick").click(function(){
            var post_link = $(this).attr("rel");
            $("#single-home-container").html("<div class='loading-text'>" + inbox_data.inbox_load_string + "</div>");
            $("#single-home-container").load( post_link + " #single-post-pull");
        return false;
        });
		
		}				
	}
		
});