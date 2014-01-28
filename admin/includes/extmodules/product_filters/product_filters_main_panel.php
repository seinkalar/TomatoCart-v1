<?php
/*
  $Id: product_filters_main_panel.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>
Toc.product_filters.MainPanel = function(config) {

  config = config || {};
  
  config.layout = 'border';
  
  config.grdFiltersGroups = new Toc.product_filters.ProductFiltersGroupsGrid({owner: config.owner});
  config.grdFilters = new Toc.product_filters.ProductFiltersGrid({owner: config.owner});
  
  config.grdFiltersGroups.on('selectchange', this.onFiltersGroupsSelectChange, this);
  config.grdFiltersGroups.getStore().on('load', this.onFiltersGroupsLoad, this);
 
  config.items = [config.grdFiltersGroups, config.grdFilters];
   
  Toc.product_filters.MainPanel.superclass.constructor.call(this,config); 
};

Ext.extend(Toc.product_filters.MainPanel,Ext.Panel,{
  onFiltersGroupsLoad: function() {
    if (this.grdFiltersGroups.getStore().getCount() > 0) {
      this.grdFiltersGroups.getSelectionModel().selectFirstRow();
      record = this.grdFiltersGroups.getStore().getAt(0);
      
      this.onFiltersGroupsSelectChange(record);
    } else {
      this.grdFilters.reset();
    }
  },

  onFiltersGroupsSelectChange: function(record) {
    this.grdFilters.setTitle('<?php echo $osC_Language->get("heading_title");?>:  '+ record.get('filters_groups_name'));
    this.grdFilters.iniGrid(record);
  }
});