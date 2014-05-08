<?php
/*
  $Id: configurations_panel.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  
?>
Toc.store.ConfigurationsPanel = function (config) {
  config = config || {};
  
  config.title = '<?php echo $osC_Language->get('section_configurations'); ?>';
  config.border = false;
  config.layout = 'fit';
  
  config.items = this.buildForm();

  Toc.store.ConfigurationsPanel.superclass.constructor.call(this, config);
};

Ext.extend(Toc.store.ConfigurationsPanel, Ext.Panel, {
  buildForm: function () {
    var pnlConfigurations = new Ext.TabPanel({
  		activeTab: 0,
  		deferredRender: false,
  		items: [
  			this.buildGeneralConfigPanel(),
  			this.buildStockConfigPanel(),
  			this.buildMaximumConfigPanel()
  		]
  	});
  	
  	return pnlConfigurations;
  },
  
  buildGeneralConfigPanel: function() {
  	var pnlGeneralConfig = new Ext.Panel({
  		title: '<?php echo $osC_Language->get('section_general_config'); ?>',
  		layout: 'form',
  		labelWidth: 300,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  			{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_maintenance_mode'); ?>', xtype:'radio', name: 'maintenance_mode', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1'}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_maintenance_mode'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'maintenance_mode', hideLabel: true, inputValue: '0', checked: true}
							]
						}
					]
				},
  			{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_display_prices_with_tax'); ?>', xtype:'radio', name: 'display_prices_with_tax', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_display_prices_with_tax'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'display_prices_with_tax', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_display_products_recursively'); ?>', xtype:'radio', name: 'dislay_products_recursively', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_display_products_recursively'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'dislay_products_recursively', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_synchronize_cart_with_database'); ?>', xtype:'radio', name: 'synchronize_cart_with_database', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1'}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_synchronize_cart_with_database'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'synchronize_cart_with_database', hideLabel: true, inputValue: '-1', checked: true}
							]
						}
					]
				},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_show_confirmation_dialog'); ?>', xtype:'radio', name: 'show_confirmation_dialog', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_show_confirmation_dialog'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'show_confirmation_dialog', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				}
  		]
  	});
  	
  	return pnlGeneralConfig;
  },
  
  buildStockConfigPanel: function() {
  	var pnlStoreConfig = new Ext.Panel({
  		title: '<?php echo $osC_Language->get('section_stock_config'); ?>',
  		layout: 'form',
  		labelWidth: 300,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  			{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_check_stock_level'); ?>', xtype:'radio', name: 'check_stock_level', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_check_stock_level'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'check_stock_level', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_subtract_stock'); ?>', xtype:'radio', name: 'subtract_stock', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_subtract_stock'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'subtract_stock', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_allow_checkout'); ?>', xtype:'radio', name: 'allow_checkout', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_allow_checkout'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'allow_checkout', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				},
				{xtype: 'textfield', fieldLabel: '<?php echo $osC_Language->get('field_mark_out_of_stock'); ?>', name: 'mark_out_of_stock', value: '***'},
				{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_stock_reorder_level'); ?>', name: 'stock_reorder_level', value: 5},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_stock_email_alerts'); ?>', xtype:'radio', name: 'stock_email_alerts', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_stock_email_alerts'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'stock_email_alerts', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				},
				{
					layout: 'column',
					border: false,
					labelSeparator: ' ',
					items:[
						{
							width: 400,
							layout: 'form',
							border: false,
							items:[
								{fieldLabel: '<?php echo $osC_Language->get('field_check_stock_cart_synchronization'); ?>', xtype:'radio', name: 'check_stock_cart_synchronization', boxLabel: '<?php echo $osC_Language->get('field_yes'); ?>', xtype:'radio', inputValue: '1', checked: true}
							]
						},
						{
							width: 80,
							layout: 'form',
							border: false,
							items: [
								{fieldLabel: '<?php echo $osC_Language->get('field_check_stock_cart_synchronization'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'check_stock_cart_synchronization', hideLabel: true, inputValue: '-1'}
							]
						}
					]
				}
  		]
		});
		
		return pnlStoreConfig;		
  },
  
  buildMaximumConfigPanel: function() {
  	var panelMaximumConfig = new Ext.Panel({
			title: '<?php echo $osC_Language->get('section_maximum_config'); ?>',
			layout: 'form',
  		labelWidth: 300,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  			{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_search_results'); ?>', name: 'search_results', value: 20},
  			{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_list_per_row'); ?>', name: 'list_per_row', value: 3},
  			{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_new_products_listing'); ?>', name: 'new_products_listing', value: 10},
  			{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_search_results_auto_completer'); ?>', name: 'search_results_auto_completer', value: 10},
  			{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_maximum_product_name_length'); ?>', name: 'product_name_auto_completer', value: 40},
  			{xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_width_auto_completer'); ?>', name: 'width_auto_completer', value: 400}
			]
  	});
  	
  	return panelMaximumConfig;
  }  
});