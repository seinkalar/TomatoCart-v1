 <?php
/*
  $Id: store_grid.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.store.StoreGrid = function(config) {
  
  config = config || {};
  
  config.border = false;
  config.viewConfig = {emptyText: TocLanguage.gridNoRecords};
  
  config.ds = new Ext.data.Store({
    url: Toc.CONF.CONN_URL,
    baseParams: {
      module: 'store',
      action: 'list_stores'
    },
    reader: new Ext.data.JsonReader({
      root: Toc.CONF.JSON_READER_ROOT,
      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
      id: 'store_id'
    }, [
        'store_id',
        'store_name',
        'url_address'
    ]),
    autoLoad: true
  });   
 
  config.rowActions = new Ext.ux.grid.RowActions({
    actions:[
      {iconCls: 'icon-edit-record', qtip: TocLanguage.tipEdit},
      {iconCls: 'icon-delete-record', qtip: TocLanguage.tipDelete}
    ],
    widthIntercept: Ext.isSafari ? 4 : 2
  });
  config.rowActions.on('action', this.onRowAction, this);    
  config.plugins = config.rowActions;
  
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm, 
    { id: 'store_name', header: '<?php echo $osC_Language->get('table_heading_store_name'); ?>', dataIndex: 'store_name', sortable: true},
    { header: '<?php echo $osC_Language->get('table_heading_store_url'); ?>', align: 'center', dataIndex: 'url_address', width: 300},
    config.rowActions
  ]);
  config.autoExpandColumn = 'store_name';
  
  config.tbar = [
    {
      text: TocLanguage.btnAdd,
      iconCls: 'add',
      handler: function() {this.fireEvent('onAdd');},
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
  
  Toc.store.StoreGrid.superclass.constructor.call(this, config);
};

Ext.extend(Toc.store.StoreGrid, Ext.grid.GridPanel, {
  onRefresh: function() {
    this.getStore().reload();
  },

  onBatchDelete: function(){
    var keys = this.getSelectionModel().selections.keys;
    
    if (keys.length > 0) {    
    }else{
       Ext.MessageBox.alert(TocLanguage.msgInfoTitle, TocLanguage.msgMustSelectOne);
    }
  },
  
  onDelete: function(record){
  	var storeId = record.get('store_id');
    
    Ext.MessageBox.confirm(
			TocLanguage.msgWarningTitle, 
			TocLanguage.msgDeleteConfirm,
			function(btn) {
				if (btn == 'yes') {
					Ext.Ajax.request({
						url: Toc.CONF.CONN_URL,
						params: {
							module: 'store',
							action: 'delete_store',
							store_id: storeId
						},
						callback: function(options, success, response) {
							var result = Ext.decode(response.responseText);
							if (result.success == true) {
								this.fireEvent('deleteSuccess', result.feedback);
							}else{
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
  
  onRowAction: function(grid, record, action, row, col) {
    switch(action) {
      case 'icon-edit-record':
      	this.fireEvent('onEdit', record);
        break;
      
      case 'icon-delete-record':
        this.onDelete(record);
        break; 
    }
  }
});