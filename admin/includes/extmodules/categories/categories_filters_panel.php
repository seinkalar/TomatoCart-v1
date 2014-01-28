<?php
/*
  $Id: categories_filters_panel.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com
  Author: Jack.yin

  Copyright (c) 2010 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.categories.FiltersPanel = function(config) {
  config = config || {};
  
  config.border = false;
  config.title = '<?php echo $osC_Language->get('section_filters'); ?>';
  config.viewConfig = {
    emptyText: TocLanguage.gridNoRecords
  };
  
  config.ds = new Ext.data.Store({
    url: Toc.CONF.CONN_URL,
    baseParams: {
      module: 'categories',
      categories_id: config.categoriesId,
      action: 'list_filters'        
    },
    reader: new Ext.data.JsonReader({
      root: Toc.CONF.JSON_READER_ROOT,
      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
      id: 'filters_id'
    },  [
      'filters_id',
      'filters_name',
      'filters_groups_id',
      'filters_groups_name'
    ]),
    autoLoad: true
  }); 
  
  config.rowActions = new Ext.ux.grid.RowActions({
    actions:[
      {iconCls: 'icon-delete-record', qtip: TocLanguage.tipDelete}],
      widthIntercept: Ext.isSafari ? 4 : 2
  });
  
  config.rowActions.on('action', this.onRowAction, this);
  config.plugins = config.rowActions;
  
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm,
    {id: 'filters_name', header: '<?php echo $osC_Language->get('table_heading_filters_name'); ?>', dataIndex: 'filters_name'},
    {header: '<?php echo $osC_Language->get('table_heading_filters_groups_name'); ?>', dataIndex: 'filters_groups_name', width: 250},
    config.rowActions 
  ]);
  config.autoExpandColumn = 'filters_name';
  
  config.tbar = [{
    text: TocLanguage.btnAdd,
    iconCls: 'add',
    handler: this.onAdd,
    scope: this
  },
  '-', 
  {
    text: TocLanguage.btnDelete,
    iconCls: 'remove',
    handler: this.onBatchDelete,
    scope: this
  }];
  
  var thisObj = this;
  config.bbar = new Ext.PageToolbar({
    pageSize: Toc.CONF.GRID_PAGE_SIZE,
    store: config.ds,
    steps: Toc.CONF.GRID_STEPS,
    beforePageText : TocLanguage.beforePageText,
    firstText: TocLanguage.firstText,
    lastText: TocLanguage.lastText,
    nextText: TocLanguage.nextText,
    prevText: TocLanguage.prevText,
    afterPageText: TocLanguage.afterPageText,
    refreshText: TocLanguage.refreshText,
    displayInfo: true,
    displayMsg: TocLanguage.displayMsg,
    emptyMsg: TocLanguage.emptyMsg,
    prevStepText: TocLanguage.prevStepText,
    nextStepText: TocLanguage.nextStepText
  });
  
  Toc.categories.FiltersPanel.superclass.constructor.call(this, config);
};

Ext.extend(Toc.categories.FiltersPanel, Ext.grid.GridPanel, {
  onChange: function(params) {
    var store = this.getStore();
    var record = Ext.data.Record.create([
      {name: 'filters_groups_id', type: 'int'},
      {name: 'filters_id', type: 'int'},
      {name: 'filters_groups_name', type: 'string'}, 
      {name: 'filters_name', type: 'string'}
    ]);
    
    var v = new record({
      filters_groups_id: params.groups_id, 
      filters_id: params.filters_id, 
      categories_id: this.categoriesId, 
      filters_groups_name: params.groups_name, 
      filters_name: params.filters_name
    });
    
    store.add(v);
  },
  
  onAdd: function() {
    var dlg = this.owner.createFiltersDialog({owner: this});
    
    dlg.show();
  },
  
  getFilters: function() {
    var data = [];

    this.getStore().each(function(record) {
      data.push(record.get('filters_id'));
    });

    return data;
  },
  
  onRowAction: function(grid, record, action, row, col) {
    switch(action) {
      case 'icon-delete-record':
        this.onDelete(record);
        break;
    }
  },
  
  onDelete: function(record) {
    this.getStore().remove(record);
  },
  
  onBatchDelete: function() {
    var filters = this.getSelectionModel().getSelections();

    if (filters.length > 0) {
      Ext.each(filters, function(filter) {
        this.getStore().remove(filter);
      }, this);
    }else{
       Ext.MessageBox.alert(TocLanguage.msgInfoTitle, TocLanguage.msgMustSelectOne);
    }
  }
});
