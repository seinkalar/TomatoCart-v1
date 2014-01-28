<?php
/*
  $Id: main.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  echo 'Ext.namespace("Toc.product_filters");';
 
  include('product_filters_groups_grid.php');
  include('product_filters_groups_dialog.php');
  include('product_filters_main_panel.php');
  include('product_filters_grid.php');
  include('product_filters_dialog.php');
?>

Ext.override(TocDesktop.ProductFiltersWindow, {
  createWindow: function () {
    var desktop = this.app.getDesktop();
    var win = desktop.getWindow('product_filters-win');
    
    if (!win) {
      pnl = new Toc.product_filters.MainPanel({ owner: this });
      
      win = desktop.createWindow({
        id: 'product_filters-win',
        title: '<?php echo $osC_Language->get("heading_title"); ?>',
        width: 800,
        height: 400,
        iconCls: 'icon-product_filters-win',
        layout: 'fit',
        items: pnl
      });
    }
    
    win.show();
  },
  
  createProductFiltersGroupsDialog: function () {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('product_filters_groups-dialog-win');
    
    if (!dlg) {
      dlg = desktop.createWindow({}, Toc.product_filters.ProductFiltersGroupsDialog);
      
      dlg.on('saveSuccess', function (feedback) {
        this.app.showNotification({title: TocLanguage.msgSuccessTitle, html: feedback});
      }, this);
    }
    
    return dlg;
  },
  
  createProductFiltersDialog: function () {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('product_filters-dialog-win');
    
    if (!dlg) {
      dlg = desktop.createWindow({}, Toc.product_filters.ProductFiltersDialog);
      
      dlg.on('saveSuccess', function (feedback) {
        this.app.showNotification({title: TocLanguage.msgSuccessTitle, html: feedback});
      }, this);
    }
    
    return dlg;
  }
});