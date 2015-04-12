/*
  ID: popcart.js
  Author: jack.yin
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
+function($) {'use strict';
	// Constructor
	function Popcart(element) {
		this.element = $(element);
		
		this.initialize();
	}

	//static variables
	Popcart.VERSION = '1.0.0';
	
	//public methodes
	Popcart.prototype.initialize = function() {
		
	};
	
	Popcart.prototype.show = function() {
		
	};

	// PLUGIN DEFINITION
	function Plugin(option) {
		return this.each(function() {
			var $this = $(this),
				data = $this.data('toc.popcart');

			if (!data) {
				$this.data('toc.popcart', (data = new Popcart(this)));
			}
				
			if (typeof option == 'string') {
				data[option]();
			}
		})
	}

	var old = $.fn.popcart;
	$.fn.popcart = Plugin;
	$.fn.popcart.Constructor = Popcart;

	// CLEAR NO CONFLICT
	$.fn.popcart.noConflict = function() {
		$.fn.popcart = old;
		return this;
	};

	// DATA-API
	$(document).on('click.toc.popcart.data-api', '[data-toggle="popcart"]', function(event) {
		event.preventDefault()
		Plugin.call($(this), 'show')
	});
}(jQuery);
