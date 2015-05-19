(function( $ ) {
	'use strict';
	/**********************************
	 * loader area
	 * @type {[type]}
	 *********************************/
	var $state = $(".octavius-status-display");
	var $bar_wrapper = $(".progress-bar-wrapper");
	var $bar = $bar_wrapper.find(".progress-bar");
	var $page = $("#last-page-loaded");

	/**
	 * recheck button listener
	 * 
	 */
	$("#ph_octavius_reload").on("click", function(e){
		e.preventDefault();
		start_loading();
		load_urls(1);
	});
	$("#ph_octavius_load").on("click", function(e){
		e.preventDefault();
		start_loading();
		var page = $page.val();
		if(page == "" || page == 0){
			page = 1;
		}
		load_urls(page);
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
			var next_page = json.page+1;
			load_urls(next_page);
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
		load_stats();
	}

	/**********************************
	 * statistics 
	 *********************************/
	 var $meta_key_list = $("#meta-key-list");
	 var $found_display = $("#octavius-found");
	 var $lost_display = $("#octavius-lost");
	 // var $found_link = $("#octavius-found-link");
	 var $stats_loading = $("#octavius-loading");
	 // var $lost_link = $("#octavius-lost-link");
	 var $regex = $("#url-migration-regex");
	 var stats_click
	 $("#ph_octavius_check").click(function(e){
	 	e.preventDefault();
	 	window.location.reload();
	 });
	 /**
	  * on metakey change
	  */
	 $meta_key_list.on("change", function(){
	 	found = 0;
	 	// update_results_links();
	 	window.location.reload();
	 });
	/**
	 * loads statistics
	 */
	var dotchange = false;
	var dots = "";
	var found = 0;
	var stats_loading =  false;
	function load_stats(page){
		if(stats_loading) return;
		var stats_loading = true;
		if(typeof page === typeof undefined) page = 1;
		var loading_stats = setInterval(function(){
			dots = (dotchange)? "..": ".";
			dotchange = !dotchange;
			$stats_loading.text("Lade"+dots);
		},1000);
		var data = {
			/**
			 * registered action
			 */
			action: "ph_octavius_get_ga_statistics",
			/**
			 * if fresh data should be loaded from octavius service
			 */
			meta_key: $meta_key_list.val(),
			regex: $regex.val(),
			page: page,
		};
		console.log(data);
		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: data,
			dataType: "json",
			success: function(data){
				clearInterval(loading_stats);
				if(!data.error){
					if(typeof data.stats.found_meta !== typeof undefined){
						found+= parseInt(data.stats.found_meta);
					}
					found+=parseInt(data.stats.found_esenic);

					$found_display.text(found);
					var lost = parseInt(data.stats.overall) -found;
					$lost_display.text(lost);

					if(data.stats.again){
						page++;
						stats_loading = false;
						load_stats(page);
					} else {
						$stats_loading.text("Fertig");
					}
				}
				
				console.log(data);
				
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log([jqXHR, textStatus, errorThrown]);
			},
		});
	}
	/**
	 * updates links to details by select field
	 */
	function update_results_links()
	{
		// var key = $meta_key_list.val();
		// $found_link.attr("href", "?page=ph-octavius_url_checker&show_results=found&meta_key="+key+"&paged=1");
		// $lost_link.attr("href", "?page=ph-octavius_url_checker&show_results=lost&meta_key="+key+"&paged=1");
	}
	/**
	 * load stats on ready
	 */
	$(function(){
		update_results_links();
		load_stats();
	});

})( jQuery );
