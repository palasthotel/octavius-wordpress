(function( $ ) {
	'use strict';
	/**********************************
	 * loader area
	 * @type {[type]}
	 *********************************/
	var $state = $(".octavius-status-display");
	var $bar_wrapper = $(".progress-bar-wrapper");
	var $bar = $bar_wrapper.find(".progress-bar");


	$("#ph_octavius_reload").on("click", function(e){
		e.preventDefault();
		start_loading();
		load_urls(1);
	});

	/**
	 * fetches data from server for a single page
	 */
	function load_urls(page)
	{
		console.log(["loadpage", page]);
		$.ajax({
			url: ajaxurl,
			data: {
				/**
				 * registered action
				 */
				action: "ph_octavius_get_ga_urls",
				/**
				 * page of dataset
				 */
				page: page,
				/**
				 * if fresh data should be loaded from octavius service
				 */
				fetch: true,
			},
			dataType: "json",
			success: function(data){
				if( data.error ){
					console.log("error");
					console.log(data);
					setTimeout(function(){
						console.log("try again");
						load_urls(page);
					}, 1000);
				} else {
					display_results(data);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log([jqXHR, textStatus, errorThrown]);
			},
		});
	}
	/**
	 * displays the results of load_urls. If needed loads next page
	 */
	function display_results(json)
	{
		console.log(["display", json]);
		$state.html(json.page+"/"+json.pages);
		$bar.css("width", parseInt( (json.page/json.pages *100) )+"%" );
		if(json.page < json.pages)
		{
			load_urls(++json.page);
		} else {
			done_loading();		
		}
	}
	/**
	 * loading phase starts
	 */
	function start_loading()
	{
		$bar_wrapper.removeClass("done");
		$bar_wrapper.addClass("loading");
		$state.html("loading...");
	}
	/**
	 * loading phase ends
	 */
	function stop_loading()
	{
		$bar_wrapper.removeClass("loading");
	}

	/**
	 * done loading
	 */
	function done_loading(){
		stop_loading();
		$bar_wrapper.addClass("done");
	}

	/**********************************
	 * statistics 
	 *********************************/
	 var $meta_key_list = $("#meta-key-list");
	 var $found_display = $("#octavius-found");
	 var $lost_display = $("#octavius-lost");
	 /**
	  * on metakey change
	  */
	 $meta_key_list.on("change", function(){
	 	load_stats();
	 });
	/**
	 * loads statistics
	 */
	function load_stats(){
		$.ajax({
			url: ajaxurl,
			data: {
				/**
				 * registered action
				 */
				action: "ph_octavius_get_ga_statistics",
				/**
				 * if fresh data should be loaded from octavius service
				 */
				meta_key: $meta_key_list.val(),
			},
			dataType: "json",
			success: function(data){
				if(!data.error){
					$found_display.text(data.stats.found);
					$lost_display.text(data.stats.lost);
				} else {
					console.log(data);
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log([jqXHR, textStatus, errorThrown]);
			},
		});
	}
	/**
	 * load stats on ready
	 */
	$(function(){
		load_stats();
	});

})( jQuery );
