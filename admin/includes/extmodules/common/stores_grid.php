<?php
/*
  $Id: stores_grid.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.common.StoresGrid = function(config) {
	config = config || {};
	
	config.title = '<?php echo $osC_Language->get('section_stores'); ?>';
  
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
      {name: 'store_id'},
      {name: 'store_name'},
      {name: 'url_address'}
    ]),
    autoLoad: true
  });
  
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm,
    {id:'store_name', header: "<?php echo $osC_Language->get('table_heading_stores_name'); ?>", sortable: true, dataIndex: 'store_name'},
    {header: "<?php echo $osC_Language->get('table_heading_stores_url'); ?>", align: 'center', dataIndex: 'url_address', width: 200}
  ]);
  config.autoExpandColumn = 'store_name';
  
  config.txtSearch = new Ext.form.TextField({
    width:160,
    paramName: 'search'
  });
  
  config.tbar = [
    { 
      text: TocLanguage.btnRefresh,
      iconCls:'refresh',
      handler: this.onRefresh,
      scope: this
    }, 
    '->',
    config.txtSearch,
    ' ', 
    {
      iconCls : 'search',
      handler : this.onSearch,
      scope : this
    }
  ];
  
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
  
  Toc.common.StoresGrid.superclass.constructor.call(this, config);
}

Ext.extend(Toc.common.StoresGrid, Ext.grid.GridPanel, {
	onRefresh: function() {
    this.getStore().reload();
  },
  
  onSearch: function() {
    var filter = this.txtSearch.getValue() || null;
    var store = this.getStore();
    
    store.baseParams['search'] = filter;
    store.reload();
  },
  
  getStoreIds: function() {
  	var keys = Ext.util.JSON.encode(this.selModel.selections.keys);
  	
  	return keys;
  }
});