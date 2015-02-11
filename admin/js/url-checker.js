(function( $ ) {
	'use strict';
	
	var $state = $(".octavius-status-display");
	var $bar_wrapper = $(".progress-bar-wrapper");
	var $bar = $bar_wrapper.find(".progress-bar");


	$("#ph_octavius_reload").on("click", function(e){
		e.preventDefault();
		startLoading();
		loadURLs(1);
	});

	/**
	 * fetches data from server for a single page
	 */
	function loadURLs(page)
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
						loadURLs(page);
					}, 1000);
				} else {
					displayResults(data);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log([jqXHR, textStatus, errorThrown]);
			},
		});
	}
	/**
	 * displays the results of loadURLs. If needed loads next page
	 */
	function displayResults(json)
	{
		console.log(["display", json]);
		$state.html(json.page+"/"+json.pages);
		$bar.css("width", parseInt( (json.page/json.pages *100) )+"%" );
		if(json.page < json.pages)
		{
			loadURLs(++json.page);
		} else {
			doneLoading();		
		}
	}
	/**
	 * loading phase starts
	 */
	function startLoading()
	{
		$bar_wrapper.removeClass("done");
		$bar_wrapper.addClass("loading");
	}
	/**
	 * loading phase ends
	 */
	function stopLoading()
	{
		$bar_wrapper.removeClass("loading");
	}

	/**
	 * done loading
	 */
	function doneLoading(){
		stopLoading();
		$bar_wrapper.addClass("done");
	}

})( jQuery );
