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
  			this.buildMetaInfoPanel(),
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
        fields: ['code', 'name'],
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
      displayField: 'name', 
      valueField: 'code', 
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
  		  {xtype: 'panel', html: '<em style="color:red;">Don\'t use directories for store url and ssl url to create a new store. You should always point another domain or sub domain to your hosting.<br />Example: http://sub.yourdomain.com/</em>', border: false, style: 'margin:10px;'},
  			{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em>Store URL', name: 'store_url', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: 'SSL URL', name: 'ssl_url'},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em>Store Name', name: 'store_name', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em>Store Owner', name: 'store_owner', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em>E-Mail Address', name: 'store_email_address', allowBlank: false},
	    	{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em>E-Mail From', name: 'store_email_from', allowBlank: false},
	    	{xtype: 'textarea', fieldLabel: '<em style="color:red;"> * </em>Store Address & Phone', name: 'store_address_phone', height: 150, allowBlank: false},
	    	this.cboTemplates
  		]
  	});
  	
  	return pnlGeneral;
  },
  
  buildMetaInfoPanel: function() {
  	var pnlMetaInfo= new Ext.Panel({
  		title: 'Meta Info',
  		layout: 'form',
  		labelSeparator: ' ',
			labelWidth: 150,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  			{xtype: 'textfield', fieldLabel: '<em style="color:red;"> * </em>Store Title', name: 'store_title'},
				{xtype: 'textfield', fieldLabel: 'Meta Keywords', name: 'meta_keywords'},
				{xtype: 'textarea', height: 150, fieldLabel: 'Meta Description', name: 'meta_description'},
				{xtype: 'textarea', height: 150, fieldLabel: 'Homepage Text', name: 'home_text'}
  		]
  	});
  	
  	return pnlMetaInfo;
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
      allowBlank: false,
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
      allowBlank: false,
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
      name: 'language',
      store: dsLanguages,  
      fieldLabel: '<?php echo $osC_Language->get("field_currency"); ?>',  
      valueField: 'code',  
      displayField: 'title',  
      hiddenName: 'currency_code',  
      triggerAction: 'all',  
      allowBlank: false,
      readOnly: true
    });    
    
  	var pnlLocal = new Ext.Panel({
  		title: 'Localization',
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
  			{xtype: 'textfield', fieldLabel: 'TimeZone', name: 'time_zone'}
  		]
  	});
  	
  	return pnlLocal;
  },
  
  buildConfigurationPanel: function() {
  	var pnlConfiguration = new Ext.TabPanel({
  		title: 'Configuration',
  		activeTab: 0,
  		deferredRender: false,
  		items: [
  			this.buildGeneralConfiguration()
  		]
  	});
  	
  	return pnlConfiguration;
  },
  
  buildGeneralConfiguration: function() {
  	var pnlGeneralConfig = new Ext.Panel({
  		title: 'General',
  		layout: 'form',
  		labelWidth: 150,
  		style: 'padding: 8px',
  		defaults: {
  			anchor: '96%'
  		},
  		items: [
  		]
  	});
  	
  	return pnlGeneralConfig;
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