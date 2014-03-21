<?php
/*
  $Id: manufacturers_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.manufacturers.ManufacturersDialog = function(config) {

  config = config || {};
  
  config.id = 'manufacturers_dialog-win';
  config.title = '<?php echo $osC_Language->get('action_heading_new_manufacturer'); ?>';
  config.width = 500;
  config.height = 380;
  config.modal = true;
  config.layout = 'fit';
  config.iconCls = 'icon-manufacturers-win';
  config.items = this.buildForm(config.manufactuersId);  
  
  config.buttons = [
    {
      text: TocLanguage.btnSave,
      handler: function() {
        this.submitForm();
      },
      scope: this
    },
    {
      text: TocLanguage.btnClose,
      handler: function() { 
        this.close();
      },
      scope: this
    }
  ];

  this.addEvents({'saveSuccess' : true});  
  
  Toc.manufacturers.ManufacturersDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.manufacturers.ManufacturersDialog, Ext.Window, {
  
  show: function (id) {
    var manufacturersId = id || null;
    
    this.frmManufacturer.form.reset();
    this.frmManufacturer.baseParams['manufacturers_id'] = manufacturersId;
    
    if (manufacturersId > 0) {
      this.frmManufacturer.load({
        url: Toc.CONF.CONN_URL,
        params: {
          module: 'manufacturers',
          action: 'load_manufacturer'
        },
        success: function(form, action) {
          var img = action.result.data.manufacturers_image;
          
          if (img) {
            var html = '<img src ="../images/manufacturers/' + img + '"  style = "margin-left: 110px; width: 80px; height: 80px" /><br/><span style = "padding-left: 110px;">/images/manufacturers/' + img + '</span>';
            this.frmManufacturer.findById('manufactuerer_image_panel').body.update(html);
          }          
          
          Toc.manufacturers.ManufacturersDialog.superclass.show.call(this);
        },
        failure: function() {
          Ext.Msg.alert(TocLanguage.msgErrTitle, TocLanguage.msgErrLoadData);
        },
        scope: this       
      });
    } else {   
      Toc.manufacturers.ManufacturersDialog.superclass.show.call(this);
    }
  },
      
  buildForm: function(manufactuersId) {
    this.pnlGeneral = new Toc.manufacturers.GeneralPanel();
    this.pnlMetaInfo = new Toc.manufacturers.MetaInfoPanel();
    this.grdStores = new Toc.common.StoresGrid();
    
    if (manufactuersId > 0) {
    	this.grdStores.getStore().on('load', function() {
    		Ext.Ajax.request({
					url: Toc.CONF.CONN_URL,
            params: {
              module: 'manufacturers',
              action: 'load_stores',
              manufactuers_id: manufactuersId
            },
            callback: function(options, success, response){
              var result = Ext.decode(response.responseText);
              
              if (result.success) {
              	var storesIds = result.stores;
              	
              	Ext.each(storesIds, function(storeId) {
              		var index = this.grdStores.getStore().indexOfId(storeId);
              		
              		this.grdStores.getSelectionModel().selectRow(index);
              	}, this);
              }
            },
            scope: this
				});   
			}, this);
    }	
    
    tabManufacturers = new Ext.TabPanel({
      activeTab: 0,
      defaults:{
        hideMode:'offsets'
      },
      deferredRender: false,
      items: [
        this.pnlGeneral,
        this.pnlMetaInfo,
        this.grdStores  
      ]
    });
    
    this.frmManufacturer = new Ext.form.FormPanel({
      id: 'form-manufacturers',
      layout: 'fit',
      fileUpload: true,
      labelWidth: 120,
      url: Toc.CONF.CONN_URL,
      baseParams: {  
        module: 'manufacturers',
        action: 'save_manufacturer'
      },
      scope: this,
      items: tabManufacturers
    });
    
    return this.frmManufacturer;
  },

  submitForm : function() {
  	this.frmManufacturer.form.baseParams['stores_ids'] =  this.grdStores.getStoreIds();
  	
    this.frmManufacturer.form.submit({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      success: function(form, action) {
         this.fireEvent('saveSuccess', action.result.feedback);
         this.close();  
      },    
      failure: function(form, action) {
        if (action.failureType != 'client') {
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
        }
      },  
      scope: this
    });   
  }
});