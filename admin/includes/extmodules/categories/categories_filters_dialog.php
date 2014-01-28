<?php
/*
  $Id: categories_filters_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2010 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.categories.FiltersDialog = function(config) {
  config = config || {};
  
  config.id = 'categories_filters_dialog-win';
  config.title = '<?php echo $osC_Language->get('heading_title_filters') ?>';
  config.width = 400;
  config.iconCls = 'icon-categories-win';
  this.owner = config.owner;
  
  config.items = this.buildForm();
  
  config.buttons = [{
    text: TocLanguage.btnSave,
    handler: function() {
      this.submitForm();
    },
    scope: this
  }, {
    text: TocLanguage.btnClose,
    handler: function() { 
      this.close();
    },
    scope: this
  }];
  
  Toc.categories.FiltersDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.categories.FiltersDialog, Ext.Window, {
  buildForm: function() {
    var dsFiltersGroups = new Ext.data.Store({
      url:Toc.CONF.CONN_URL,
      baseParams: {
        module: 'categories',
        action: 'get_filters_groups'
      },
      reader: new Ext.data.JsonReader({
        fields: ['id', 'text'],
        root: Toc.CONF.JSON_READER_ROOT
      }),
      autoLoad: true
    });
    
    this.dsGroupFilters = new Ext.data.Store({
      url: Toc.CONF.CONN_URL,
      baseParams: {
        module: 'categories',
        action: 'get_group_filters'
      },
      reader: new Ext.data.JsonReader({
        fields: ['id', 'text'],
        root: Toc.CONF.JSON_READER_ROOT
      }),
      autoLoad: false,
      listeners: {
        load: this.onLoadGroupFilters,
        scope: this
      }
    });
    
    this.cboGroupFilters =  new Ext.form.ComboBox({
      fieldLabel: '<?php echo $osC_Language->get('field_group_filters'); ?>',
      xtype: 'combo', 
      store: this.dsGroupFilters , 
      name: 'group_filters', 
      mode: 'local',
      hiddenName: 'group_filters', 
      displayField: 'text', 
      valueField: 'id', 
      triggerAction: 'all', 
      editable: false,
      forceSelection: true,
      disabled: true,
      listeners: {
        select: this.onFilterSelect,
        scope: this
      }    
    });
    
    this.cboFiltersGroups =  new Ext.form.ComboBox({
      fieldLabel: '<?php echo $osC_Language->get('field_filters_groups'); ?>',
      xtype: 'combo', 
      store: dsFiltersGroups, 
      name: 'filters_groups_id', 
      mode: 'local',
      hiddenName: 'filters_groups_id', 
      displayField: 'text', 
      valueField: 'id', 
      triggerAction: 'all', 
      editable: false,
      forceSelection: true,      
      listeners: {
        select: this.onGroupSelect,
        scope: this
      }
    });
    
    this.frmFilters = new Ext.form.FormPanel({
      border: false,
      url: Toc.CONF.CONN_URL,
      defaults: {
        anchor: '98%'
      },
      style: 'padding: 8px',
      border: false,
      labelWidth: 120,
      layoutConfig: {
        labelSeparator: ''
      },
      items: [
        this.cboFiltersGroups,
        this.cboGroupFilters
      ]
    });
    
    return this.frmFilters;
  },
  
  onGroupSelect: function(combo, record) {
    this.selectedGroupsName = record.get('text');
    
    this.dsGroupFilters.baseParams.groups_id = record.get('id');
    this.dsGroupFilters.load();
  },
  
  onFilterSelect: function(combo, record) {
    this.selectedFiltersName = record.get('text');
  },
  
  onLoadGroupFilters: function() {
    this.cboGroupFilters.enable();
  },
  
  submitForm: function() {
    var filters_groups_id = this.cboFiltersGroups.getValue();
    var filters_id = this.cboGroupFilters.getValue();
    
    this.owner.onChange({'groups_id': filters_groups_id, 'filters_id': filters_id, 'groups_name': this.selectedGroupsName, 'filters_name': this.selectedFiltersName}); 
    this.close();
  }
});