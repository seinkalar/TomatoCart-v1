<?php
/*
  $Id: store_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.store.StoreDialog = function(config) {
	config = config || {};
  
  config.id = 'store-dialog-win';
  config.title = '<?php echo $osC_Language->get('action_heading_new_store'); ?>';
  config.layout = 'fit';
  config.width = 850;
  config.height = 530;
  config.modal = true;
  config.iconCls = 'icon-store-win';
  config.items = this.buildForm();
  
  config.buttons = [
    {
      text:TocLanguage.btnSave,
      handler: function(){
        this.submitForm();
      },
      scope:this
    },
    {
      text: TocLanguage.btnClose,
      handler: function(){
        this.close();
      },
      scope:this
    }
  ];
  
  this.addEvents({'saveSuccess' : true})

  Toc.store.StoreDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.store.StoreDialog, Ext.Window, {
	show: function(id) {
    var storeId = id || null;
    
    this.frmStore.form.reset();  
    
    if(storeId > 0) {
			this.frmStore.form.baseParams['store_id'] = storeId;
    	    
      this.frmStore.load({
        url: Toc.CONF.CONN_URL,
        params:{
          action: 'load_store',
          store_id: storeId
        },
        success: function(form, action) {
          Toc.store.StoreDialog.superclass.show.call(this);
        },
        failure: function(form, action) {
          Ext.Msg.alert(TocLanguage.msgErrTitle, TocLanguage.msgErrLoadData);
        }, 
        scope: this
      });
    }
    
    Toc.store.StoreDialog.superclass.show.call(this);
  },
  
  buildForm: function() {
    this.frmStore = new Ext.form.FormPanel({
      layout: 'fit',
      url: Toc.CONF.CONN_URL,
      baseParams: {  
        module: 'store',
        action : 'save_store'
      },
      deferredRender: false,
      items: [this.buildTabPanel()]
    });  
    
    return this.frmStore;
  },
  
  buildTabPanel: function() {
  	var tabPanel = new Ext.TabPanel({
  		activeTab: 0,
  		deferredRender: false,
  		items: [
  			this.buildGeneralPanel(),
  			this.buildLocalPanel(),
  			this.buildConfigurationPanel()
  		]
  	});
  	
  	return tabPanel;
  },
  
  buildGeneralPanel: function() {
  	this.dsTemplates = new Ext.data.Store({
      url:Toc.CONF.CONN_URL,
      baseParams: {
        module: 'store',
        action: 'list_templates'
      },
      reader: new Ext.data.JsonReader({
        fields: ['template_code', 'template_name'],
        root: Toc.CONF.JSON_READER_ROOT
      }),
      autoLoad: true,
      listeners: {
        load: function() {this.cboTemplates.setValue('glass_gray');},
        scope: this
      }
    });
    
    this.cboTemplates = new Ext.form.ComboBox({
      fieldLabel: 'Templates', 
      xtype:'combo', 
      store: this.dsTemplates, 
      name: 'store_template', 
      hiddenName: 'store_template_code', 
      displayField: 'template_name', 
      valueField: 'template_code', 
      triggerAction: 'all', 
      editable: false,
      forceSelection: true      
    });    
    
  	var pnlGeneral = new Ext.Panel({
  		title: 'General',
  		layout: 'form',
  		labelWidth: 150,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  		  {xtype: 'panel', html: '<em style="color:red;"><?php echo $osC_Language->get('introduction_set_store_url'); ?></em>', border: false, style: 'margin:10px;'},
  			{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_store_url'); ?>', name: 'store_url', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<?php echo $osC_Language->get('field_store_ssl_url'); ?>', name: 'ssl_url'},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_store_name'); ?>', name: 'store_name', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_store_owner'); ?>', name: 'store_owner', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_email_address'); ?>', name: 'store_email_address', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_email_from'); ?>', name: 'store_email_from', allowBlank: false},
	    	{xtype: 'textarea', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_address_phone'); ?>', name: 'store_address_phone', height: 150, allowBlank: false},
	    	this.cboTemplates
  		]
  	});
  	
  	return pnlGeneral;
  },
  
  buildLocalPanel: function() {
  	var dsCountries = new Ext.data.Store({
      url: Toc.CONF.CONN_URL,
      baseParams: {
        module: 'zone_groups',
        action: 'list_countries'
      },
      reader: new Ext.data.JsonReader({
        root: Toc.CONF.JSON_READER_ROOT,
        totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
        fields: [
          'countries_id', 
          'countries_name'
        ]
      }),
      autoLoad: true
    });
    
    this.cboCountries = new Ext.form.ComboBox({
      name: 'countries',
      store: dsCountries,
      fieldLabel: '<?php echo $osC_Language->get("field_country"); ?>',
      valueField: 'countries_id',
      displayField: 'countries_name',
      hiddenName: 'countries_id',
      triggerAction: 'all',
      readOnly: true,
      listeners: {
        select: this.onCboCountriesSelect,
        scope: this
      }
    });
     
    var dsZones = new Ext.data.Store({ 
      url: Toc.CONF.CONN_URL,  
      baseParams: {
        module: 'zone_groups',
        action: 'list_zones'
      },
      reader: new Ext.data.JsonReader({  
        root: Toc.CONF.JSON_READER_ROOT,
        totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
        fields: [
          'zone_id', 
          'zone_name'
        ]
      })
    });  
    
    this.cboZones = new Ext.form.ComboBox({  
      name: 'zones',
      store: dsZones,  
      fieldLabel: '<?php echo $osC_Language->get("field_zone"); ?>',  
      valueField: 'zone_id',  
      displayField: 'zone_name',  
      hiddenName: 'zone_id',  
      triggerAction: 'all',  
      disabled: true,
      readOnly: true
    });
    
    var dsLanguages = new Ext.data.Store({ 
      url: Toc.CONF.CONN_URL,  
      baseParams: {
        module: 'store',
        action: 'list_languages'
      },
      reader: new Ext.data.JsonReader({  
        root: Toc.CONF.JSON_READER_ROOT,
        totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
        fields: [
          'code',
          'name'
        ]
      }),
      autoLoad: true,
      listeners: {
        load: function() {this.cboLanguages.setValue('en_US');},
        scope: this
      }
    });
    
    this.cboLanguages = new Ext.form.ComboBox({  
      name: 'language',
      store: dsLanguages,  
      fieldLabel: '<?php echo $osC_Language->get("field_language"); ?>',  
      valueField: 'code',  
      displayField: 'name',  
      hiddenName: 'language_code',  
      triggerAction: 'all',  
      allowBlank: false,
      readOnly: true
    });
    
    var dsCurrencies = new Ext.data.Store({ 
      url: Toc.CONF.CONN_URL,  
      baseParams: {
        module: 'store',
        action: 'list_currencies'
      },
      reader: new Ext.data.JsonReader({  
        root: Toc.CONF.JSON_READER_ROOT,
        totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
        fields: [
          'code',
          'title'
        ]
      }),
      autoLoad: true,
      listeners: {
        load: function() {this.cboCurrencies.setValue('USD');},
        scope: this
      }
    });
    
    this.cboCurrencies = new Ext.form.ComboBox({  
      name: 'currencies',
      store: dsCurrencies,  
      fieldLabel: '<?php echo $osC_Language->get("field_currency"); ?>',  
      valueField: 'code',  
      displayField: 'title',  
      hiddenName: 'currency_code',  
      triggerAction: 'all',  
      allowBlank: false,
      readOnly: true
    });    
    
  	var pnlLocal = new Ext.Panel({
  		title: '<?php echo $osC_Language->get('section_localization'); ?>',
  		layout: 'form',
  		labelWidth: 150,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  			this.cboCountries,
  			this.cboZones,
  			this.cboLanguages,
  			this.cboCurrencies,
  			{xtype: 'textfield', fieldLabel: '<?php echo $osC_Language->get('field_timezone'); ?>', name: 'time_zone'}
  		]
  	});
  	
  	return pnlLocal;
  },
  
  buildConfigurationPanel: function() {
  	var pnlConfiguration = new Ext.TabPanel({
  		title: '<?php echo $osC_Language->get('section_configurations'); ?>',
  		activeTab: 0,
  		deferredRender: false,
  		items: [
  			this.buildGeneralConfigPanel(),
  			this.buildStockConfigPanel(),
  			this.buildMaximumConfigPanel()
  		]
  	});
  	
  	return pnlConfiguration;
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
								{fieldLabel: '<?php echo $osC_Language->get('field_display_prices_with_tax'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'display_prices_with_tax', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_display_products_recursively'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'dislay_products_recursively', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_synchronize_cart_with_database'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'synchronize_cart_with_database', hideLabel: true, inputValue: '0', checked: true}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_show_confirmation_dialog'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'show_confirmation_dialog', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_check_stock_level'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'check_stock_level', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_subtract_stock'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'subtract_stock', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_allow_checkout'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'allow_checkout', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_stock_email_alerts'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'stock_email_alerts', hideLabel: true, inputValue: '0'}
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
								{fieldLabel: '<?php echo $osC_Language->get('field_check_stock_cart_synchronization'); ?>', boxLabel: '<?php echo $osC_Language->get('field_no'); ?>', xtype:'radio', name: 'check_stock_cart_synchronization', hideLabel: true, inputValue: '0'}
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
  },
  
  submitForm : function() {
    this.frmStore.form.submit({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      success: function(form, action){
        this.fireEvent('saveSuccess', action.result.feedback);
        this.close();
      },    
      failure: function(form, action) {
        if(action.failureType != 'client') {
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
        }
      }, 
      scope: this
    });   
  },
  
  updateCboZones: function(zoneId) {
    this.cboZones.reset();
    this.cboZones.enable();  
    this.cboZones.getStore().baseParams['countries_id'] = this.cboCountries.getValue();  
    
    if(zoneId) {
      this.cboZones.getStore().on('load', function(){
        this.cboZones.setValue(zoneId);
      }, this);
    }
    
    this.cboZones.getStore().load();
  },
  
  onCboCountriesSelect: function() {
    this.updateCboZones();
  }
});