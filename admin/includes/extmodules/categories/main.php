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

  echo 'Ext.namespace("Toc.categories", "Toc.common");';
  
  include('categories_tree_panel.php');
  include('categories_grid.php');
  include('categories_general_panel.php');
  include('categories_meta_info_panel.php');
  include('categories_dialog.php');
  include('categories_move_dialog.php');
  include('categories_main_panel.php');
  include('categories_ratings_grid_panel.php');
  include('categories_filters_panel.php');
  include('categories_filters_dialog.php');
  include(DIR_FS_CATALOG . DIR_FS_ADMIN . 'includes/extmodules/common/stores_grid.php');
?>

Ext.override(TocDesktop.CategoriesWindow, {

  createWindow: function(){
    var desktop = this.app.getDesktop();
    var win = desktop.getWindow('categories-win');
     
    if(!win){
      var pnl = new Toc.categories.mainPanel({owner: this});
      
      win = desktop.createWindow({
        id: 'categories-win',
        title: '<?php echo $osC_Language->get('heading_title'); ?>',
        width: 870,
        height: 400,
        iconCls: 'icon-categories-win',
        layout: 'fit',
        items: pnl
      });
    }
    
    win.show();
  },
  
  createCategoriesDialog: function(categoriesId) {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('categories-dialog-win');
    
    if (!dlg) {
      dlg = desktop.createWindow({categories_id: categoriesId, owner: this}, Toc.categories.CategoriesDialog);
      
      dlg.on('saveSuccess', function (feedback, categoriesId, text) {
        this.app.showNotification({
          title: TocLanguage.msgSuccessTitle,
          html: feedback
        });
      }, this);
    }

    return dlg;
  },
  
  createCategoriesMoveDialog: function() {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('categories-move-dialog-win');
    
    if (!dlg) {
      dlg = desktop.createWindow({}, Toc.categories.CategoriesMoveDialog);
      
      dlg.on('saveSuccess', function (feedback) {
        this.app.showNotification({
          title: TocLanguage.msgSuccessTitle,
          html: feedback
        });
      }, this);
    }
    
    return dlg;
  },
  
  createFiltersDialog: function(config) {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('categories_filters_dialog-win');
    
    if (!dlg) {
      dlg = desktop.createWindow(config, Toc.categories.FiltersDialog);
    }
    
    return dlg;
  }
});
