<?php
/*
  $Id: general_panel.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2010 TomatoCart

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.store.GeneralPanel = function(config) {
  config = config || {};    
  
  config.title = 'General';
  config.layout = 'form';
  config.labelSeparator: ' ';
  config.style = 'padding: 8px';
  config.defaults = {anchor:98%}
  config.items = this.buildForm();
    
  Toc.store.GeneralPanel.superclass.constructor.call(this, config);
};

Ext.extend(Toc.store.GeneralPanel, Ext.Panel, {
  buildForm: function() {
    var items = [
    	{xtype: 'textfield', fieldLabel: 'Store URL<br />', name: 'store_url', allowBlank: false},
    	{xtype: 'textfield', fieldLabel: 'SSL URL', name: 'ssl_url'},
    	{xtype: 'textfield', fieldLabel: 'Store Name', name: 'store_name', allowBlank: false}
    ];
    
    return items;
  } 
});