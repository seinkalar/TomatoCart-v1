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
  config.height = 600;
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
        	if (action.result.data.store_logo) {
        		var logoImg = '<img src="' + action.result.data.store_logo + '" />';
        		this.frmStore.findById('logo_img').body.update(logoImg);
        	}
        	
          Toc.store.StoreDialog.superclass.show.call(this);
          
          this.cboCountries.setValue(action.result.data.countries_id);
          this.updateCboZones(action.result.data.zone_id);
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
    	fileUpload: true,
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
  			new Toc.store.MetaInfoPanel(),
  			this.buildLocalPanel(),
  			new Toc.store.ConfigurationsPanel()
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
	    	{xtype: 'textarea', fieldLabel: '<em style="color:red;"> * </em><?php echo $osC_Language->get('field_address_phone'); ?>', name: 'store_address_phone', height: 80, allowBlank: false},
	    	{xtype: 'fileuploadfield', width: 300, fieldLabel: '<?php echo $osC_Language->get('field_store_logo'); ?>', name: 'store_logo'},
	    	{xtype: 'panel', border: false, width: 400, id: 'logo_img', html:'', style: 'text-align:center;'},
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