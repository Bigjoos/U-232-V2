jQuery.preloadImages = function()
{
	for(var i = 0; i<arguments.length; i++)
	jQuery("<img>").attr("src", arguments[i]);
}
jQuery.preloadImages("css-menu/home", "css-menu/homeo", "css-menu/browse", "css-menu/browseo", "css-menu/search", "css-menu/searcho", "css-menu/upload", "css-menu/uploado", "css-menu/chat", "css-menu/chato", "css-menu/forum", "css-menu/forumo", "css-menu/top", "css-menu/topo", "css-menu/rules", "css-menu/ruleso", "css-menu/faq", "css-menu/faqo", "css-menu/links", "css-menu/linkso", "css-menu/staff", "css-menu/staffo");

jQuery(document).ready(function(){
	
	$("#iconbar li a").hover(
		function(){
			var iconName = $(this).children("img").attr("src");
			var origen = iconName.split(".png")[0];
			$(this).children("img").attr({src: "" + origen + "o.png"});
			$(this).css("cursor", "pointer");
			$(this).animate({ width: "100px" }, {queue:false, duration:"normal"} );
			$(this).children("span").animate({opacity: "show"}, "fast");
		}, 
		function(){
			var iconName = $(this).children("img").attr("src");
			var origen = iconName.split("o.")[0];
			$(this).children("img").attr({src: "" + origen + ".png"});			
			$(this).animate({ width: "57px" }, {queue:false, duration:"normal"} );
			$(this).children("span").animate({opacity: "hide"}, "fast");
		});
});