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
  
  config.items = this.buildTabs(config);
 
  Toc.product_filters.MainPanel.superclass.constructor.call(this,config); 
};

Ext.extend(Toc.product_filters.MainPanel,Ext.Panel,{
	buildTabs: function(config) {
		var tabPanels = new Ext.TabPanel({
      activeTab: 0,
      defaults:{
        hideMode:'offsets'
      },
      deferredRender: false,
      items: [
      	this.getConfigurationPanel(),
      	this.getGridPanel(config)
      ]
    }); 
    
    return tabPanels;
	},
	
	getConfigurationPanel: function() {
	  var pnlMessage = new Ext.Panel({border: false, html: '<p><?php echo $osC_Language->get('introduction_enhance_performance'); ?></p>'});
	  
		this.frmConfiguration = new Ext.form.FormPanel({
			title: '<?php echo $osC_Language->get('heading_configuration_title'); ?>',
			border: false,
			layout: 'form',
      height: 335,
      url: Toc.CONF.CONN_URL,
      baseParams: {  
        module: 'product_filters',
        action: 'save_configurations'
      },
      defaults: {
          anchor: '98%'
      },
      layoutConfig: {
        labelSeparator: ''
      },
      labelWidth: 160,
      items: [
       	pnlMessage,
				{
          layout: 'column',
          border: false,
          items: [
            { 
              layout: 'form',
              labelSeparator: ' ',
              border: false,
              items:[
                {fieldLabel: '<?php echo $osC_Language->get('field_get_filters_method'); ?>', boxLabel: '<?php echo $osC_Language->get('products'); ?>' , name: 'get_filters_method', xtype:'radio', inputValue: 'p', checked: true}
              ]
            },
            { 
              layout: 'form',
              border: false,
              items:[
                { hideLabel: true, boxLabel: '<?php echo $osC_Language->get('categories'); ?>' , name: 'get_filters_method', xtype:'radio', inputValue: 'c'}
              ]
            }
          ]  
        },
        {
          layout: 'column',
          border: false,
          items: [
            { 
              layout: 'form',
              labelSeparator: ' ',
              border: false,
              items:[
                {fieldLabel: '<?php echo $osC_Language->get('field_active_price_range'); ?>', boxLabel: '<?php echo $osC_Language->get('yes'); ?>' , name: 'active_price_range', xtype:'radio', inputValue: '1', checked: true}
              ]
            },
            { 
              layout: 'form',
              border: false,
              items:[
                { hideLabel: true, boxLabel: '<?php echo $osC_Language->get('no'); ?>' , name: 'active_price_range', xtype:'radio', inputValue: '0'}
              ]
            }
          ]  
        },
        {
          layout: 'column',
          border: false,
          items: [
            { 
              layout: 'form',
              labelSeparator: ' ',
              border: false,
              items:[
                {fieldLabel: '<?php echo $osC_Language->get('field_calculate_products_counts'); ?>', boxLabel: '<?php echo $osC_Language->get('yes'); ?>' , name: 'calculate_products_counts', xtype:'radio', inputValue: '1', checked: true}
              ]
            },
            { 
              layout: 'form',
              border: false,
              items:[
                { hideLabel: true, boxLabel: '<?php echo $osC_Language->get('no'); ?>' , name: 'calculate_products_counts', xtype:'radio', inputValue: '0'}
              ]
            }
          ]  
        },
        
        {
          layout: 'column',
          border: false,
          items: [
            { 
              layout: 'form',
              labelSeparator: ' ',
              border: false,
              items:[
                {fieldLabel: '<?php echo $osC_Language->get('field_hide_disabled_filters'); ?>', boxLabel: '<?php echo $osC_Language->get('yes'); ?>' , name: 'hide_disabled_filters', xtype:'radio', inputValue: '1'}
              ]
            },
            { 
              layout: 'form',
              border: false,
              items:[
                { hideLabel: true, boxLabel: '<?php echo $osC_Language->get('no'); ?>' , name: 'hide_disabled_filters', xtype:'radio', inputValue: '0', checked: true}
              ]
            }
          ]  
        }
      ]
    });
    
    return this.frmConfiguration;
	},
	
	getGridPanel: function(config) {
		this.grdFiltersGroups = new Toc.product_filters.ProductFiltersGroupsGrid({owner: config.owner});
  	this.grdFilters = new Toc.product_filters.ProductFiltersGrid({owner: config.owner});
  	
  	this.grdFiltersGroups.on('selectchange', this.onFiltersGroupsSelectChange, this);
  	this.grdFiltersGroups.getStore().on('load', this.onFiltersGroupsLoad, this);
  	
  	var pnlGrid = new Ext.Panel({
  		title: '<?php echo $osC_Language->get('heading_filters_title'); ?>',
  		border: false,
  		layout: 'border',
  		height: 335,
  		items: [this.grdFiltersGroups, this.grdFilters]
  	});
  	
  	return pnlGrid;
	},
	
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