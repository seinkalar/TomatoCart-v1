 <?php
/*
  $Id: main.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  echo 'Ext.namespace("Toc.store");';
  
  include('store_grid.php');
?>

Ext.override(TocDesktop.StoreWindow, {

  createWindow : function() {
    var desktop = this.app.getDesktop();
    var win = desktop.getWindow('store-win');
     
    if (!win) {
      grd = new Toc.store.StoreGrid({owner: this});
      
      grd.on('deleteSuccess', function(feedback) {this.onDeleteSuccess(grd, feedback);}, this);

      win = desktop.createWindow({
        id: 'store-win',
        title: '<?php echo $osC_Language->get('heading_itle'); ?>',
        width: 800,
        height: 400,
        iconCls: 'icon-store-win',
        layout: 'fit',
        items: grd
      });
    }
           
    win.show();
  },
  
  onDeleteSuccess: function(grd, feedback) {
  	this.app.showNotification({title: TocLanguage.msgSuccessTitle, html: feedback});
  	grd.onRefresh();
  }
});
