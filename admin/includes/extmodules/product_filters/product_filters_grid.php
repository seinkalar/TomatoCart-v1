<?php
/*
  $Id: product_filters_grid.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>
Toc.product_filters.ProductFiltersGrid = function (config) {

  config = config || {};
  
  this.groupsId = null;
  this.groupsName = null;
    
  config.title = '<?php echo $osC_Language->get('heading_title'); ?>';
  config.region = 'east';
  config.border = false;
  config.split = true;
  config.minWidth = 240;
  config.maxWidth = 320;
  config.width = 260;
  config.viewConfig = {emptyText: TocLanguage.gridNoRecords};
  
  config.ds = new Ext.data.Store({
    url: Toc.CONF.CONN_URL,
    baseParams: {
      module: 'product_filters',
      action: 'list_filters'
    },
    reader: new Ext.data.JsonReader({
      root: Toc.CONF.JSON_READER_ROOT,
      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
      id: 'filters_id'
    }, [
      'filters_id',
      'filters_groups_id',
      'sort_order',
      'filters_name'
    ]),
    autoLoad: false
  });
  
  config.rowActions = new Ext.ux.grid.RowActions({
    actions: [
     {iconCls: 'icon-edit-record', qtip: TocLanguage.tipEdit},
     {iconCls: 'icon-delete-record', qtip: TocLanguage.tipDelete}],
    widthIntercept: Ext.isSafari ? 4 : 2
  });
  config.rowActions.on('action', this.onRowAction, this);    
  config.plugins = config.rowActions;
  
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm,
    {id: 'name', header: '<?php echo $osC_Language->get("table_heading_filters");?>', dataIndex: 'filters_name'},
    {header: '<?php echo $osC_Language->get("table_heading_sort_order");?>', dataIndex: 'sort_order'},
    config.rowActions
  ]);
  config.autoExpandColumn = 'name';
  
  config.tbar = [
    {
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
    },
    '-',
    {
      text: TocLanguage.btnRefresh,
      iconCls: 'refresh',
      handler: this.onRefresh,
      scope: this
    }
  ];
  
  config.bbar = new Ext.PagingToolbar({
    pageSize: Toc.CONF.GRID_PAGE_SIZE,
    store: config.ds,
    iconCls: 'icon-grid',
    emptyMsg: TocLanguage.emptyMsg
  });
  
  Toc.product_filters.ProductFiltersGrid.superclass.constructor.call(this, config);
};

Ext.extend(Toc.product_filters.ProductFiltersGrid, Ext.grid.GridPanel, {

  iniGrid: function (record) {
    this.groupsId = record.get('filters_groups_id');
    this.groupsName = record.get('filters_groups_name');
    
    this.getStore().baseParams['groups_id'] = record.get('filters_groups_id');
    this.getStore().load();
  },

  onAdd: function () {
    if (this.groupsId) {
      var dlg = this.owner.createProductFiltersDialog();
      
      dlg.on('saveSuccess', function() {
        this.onRefresh();
      }, this);
      
      dlg.show(this.groupsId);
    } else {
      Ext.MessageBox.alert(TocLanguage.msgInfoTitle, TocLanguage.msgMustSelectOne);
    }
  },
  
  onEdit: function (record) {
    var filtersId = record.get('filters_id');
    var dlg = this.owner.createProductFiltersDialog();
    dlg.setTitle(this.groupsName);
    
    dlg.on('saveSuccess', function() {
        this.onRefresh();
      }, this);
      
    dlg.show(this.groupsId, filtersId);
  },

  onDelete: function (record) {
    var filtersId = record.get('filters_id');
    var groupsId = this.groupsId;
     
    Ext.MessageBox.confirm(
      TocLanguage.msgWarningTitle, 
      TocLanguage.msgDeleteConfirm, 
      function (btn) {
        if (btn == 'yes') {
          Ext.Ajax.request({
            waitMsg: TocLanguage.formSubmitWaitMsg,
            url: Toc.CONF.CONN_URL,
            params: {
              module: 'product_filters',
              action: 'delete_filter',
              filters_id: filtersId,
              groups_id: groupsId
            },
            callback: function (options, success, response) {
              var result = Ext.decode(response.responseText);
              
              if (result.success == true) {
                this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                this.getStore().reload();
              } else {
                Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
              }
            },
            scope: this
          });
        }
      }, 
      this
    );
  },
  
  onBatchDelete: function () {
    var keys = this.getSelectionModel().selections.keys;
    var groupsId = this.groupsId;
    
    if (keys.length > 0) {
      var batch = Ext.util.JSON.encode(keys);
      
      Ext.MessageBox.confirm(
        TocLanguage.msgWarningTitle, 
        TocLanguage.msgDeleteConfirm, 
        function (btn) {
          if (btn == 'yes') {
            Ext.Ajax.request({
              waitMsg: TocLanguage.formSubmitWaitMsg,
              url: Toc.CONF.CONN_URL,
              params: {
                module: 'product_filters',
                action: 'delete_filters',
                batch: batch,
                groups_id: groupsId
              },
              callback: function (options, success, response) {
                var result = Ext.decode(response.responseText);
                
                if (result.success == true) {
                  this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                  this.getStore().reload();
                } else {
                  Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
                }
              },
              scope: this
            });
          }
        }, 
        this
      );
    } else {
      Ext.MessageBox.alert(TocLanguage.msgInfoTitle, TocLanguage.msgMustSelectOne);
    }
  },
  
  onRefresh: function () {
    this.getStore().load();
  },
  
  onRowAction: function (grid, record, action, row, col) {
    switch (action) {
      case 'icon-delete-record':
        this.onDelete(record);
        break;
      case 'icon-edit-record':
        this.onEdit(record);
        break;
    }
  },
   
  reset: function() {
    this.setTitle('<?php echo $osC_Language->get('table_heading_filters'); ?>');
    this.groupsId = null;
    this.groupsName = null;
    this.getStore().removeAll();
  } 
});