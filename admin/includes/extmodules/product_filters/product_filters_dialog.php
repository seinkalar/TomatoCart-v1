<?php
/*
  $Id: product_filters_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.product_filters.ProductFiltersDialog = function(config) {

  config = config || {};
  
  config.id = 'product_filters-dialog-win';
  config.title = '<?php echo $osC_Language->get("action_heading_new_filter");?>';
  config.width = 440;
  config.modal = true;
  config.iconCls = 'icon-product_filters-win';
  config.items = this.buildForm();
  
  config.buttons = [
    {
      text:TocLanguage.btnSave,
      handler: function(){
        this.submitForm();
      }, 
      scope: this
    },
    {
      text: TocLanguage.btnClose,
      handler: function(){
        this.close();
      }, 
      scope: this
    }
  ];

  this.addEvents({'saveSuccess': true});  
  
  Toc.product_filters.ProductFiltersDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.product_filters.ProductFiltersDialog, Ext.Window, {

  show: function (id, filtersId) {
    this.groupsId = id || null;
    var filtersId = filtersId || null;
    
    this.frmFilter.form.reset();  
    this.frmFilter.form.baseParams['groups_id'] = this.groupsId;
    this.frmFilter.form.baseParams['filters_id'] = filtersId;
    
    if (filtersId > 0) {
      this.frmFilter.load({
        url: Toc.CONF.CONN_URL,
        params: {
          module: 'product_filters',
          action: 'load_filter',
          filters_id: filtersId
        },
        success: function (form, action) {
          Toc.product_filters.ProductFiltersDialog.superclass.show.call(this);
        },
        failure: function (form, action) {
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
        },
        scope: this
      });
    } else {
      Toc.product_filters.ProductFiltersDialog.superclass.show.call(this);
    }
  },
  
  buildForm: function() {
    this.frmFilter = new Ext.form.FormPanel({ 
      url: Toc.CONF.CONN_URL,
      baseParams: {  
        module: 'product_filters',
        action: 'save_filter'
      }, 
      defaults: {
        anchor: '97%'
      },
      layoutConfig: {
        labelSeparator: ''
      }
    });
    
    <?php
      $i = 1; 
      foreach ( $osC_Language->getAll() as $l ) {
        echo 'var lang' . $l['id'] . ' = new Ext.form.TextField({name: "filters_name[' . $l['id'] . ']",';
        
        if ($i != 1 ) 
          echo ' fieldLabel:"&nbsp;", ';
        else
          echo ' fieldLabel:"&nbsp;' . $osC_Language->get('field_filter_name') . '", ';
          
        echo "labelStyle: 'background: url(../images/worldflags/" . $l['country_iso'] . ".png) no-repeat right center !important',";
        echo 'allowBlank: false});';
        
        echo 'this.frmFilter.add(lang' . $l['id'] . ');';
        $i++;
      }     
    ?>
    
    this.frmFilter.add(new Ext.form.NumberField({name: 'sort_order', fieldLabel: '&nbsp;<?php echo $osC_Language->get('field_filter_sort_order'); ?>', minValue : 0, allowBlank: false}));
    
    return this.frmFilter;
  },

  submitForm: function() {
    this.frmFilter.form.submit({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      success:function(form, action){
        this.fireEvent('saveSuccess', action.result.feedback);
        this.close();
      },    
      failure: function(form, action) {
        if(action.failureType != 'client'){
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
        }
      },
      scope: this
    });   
  }
});