<?php
/*
  $Id: filters.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com
  Author: Jack.yin

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Boxes_filters extends osC_Modules {
    var $_title,
        $_code = 'filters',
        $_filters_groups,
        $_author_name = 'Jack.yin',
        $_author_www = 'http://www.tomatocart.com',
        $_group = 'boxes';

    /*
     * Construct
     */
    function osC_Boxes_filters() {
      global $osC_Language;
      
      if ($osC_Language->get('box_filters_heading') == 'box_filters_heading') {
        $this->_title = 'Filters';
      }
      
      $this->_filters_groups = array();

      $this->_title = $osC_Language->get('box_filters_heading');
    }

    /*
     * Initialize the module
     */
    function initialize() {
      global $osC_Database, $osC_Language, $current_category_id, $osC_Products, $osC_Cache;
      
      //verify whether it is located in the products listing page
      if ((int)$current_category_id > 0) {
      	$this->filters_groups = $osC_Products->getFilterGroups(BOX_FILTERS_CACHE, BOX_FILTERS_SHOW_PROUDCTS_COUNT);
      	
        //make sure that there are available filters
        if (count($this->filters_groups) > 0) {
          //generate the box content
          $this->generate_content();
        }
      }
    }
    
    /*
     * Generate the module content
     */
    function generate_content() {
      global $osC_Language;
      
      //build the filters form
      if (count($this->filters_groups) > 0) {
        $this->_content = '<form id="frm-filters" name="filters" action="' . osc_href_link(FILENAME_DEFAULT) . '" method="get">';
        foreach($this->filters_groups as $groups_id => $group) {
          //ensure that there are filters in the group
          if (count($group['filters']) > 0) {
            $this->_content .= '<div class="filterBox">';
          
            //group title
            $this->_content .= '<div class="groupTitle"><h3 class="toggleTrigger triggerOpened">' . $group['group_name'] . '</h3></div>';
            
            //filters
            $this->_content .= '<ul class="filters">';
            
            foreach($group['filters'] as $filter) {
              //checked
              if (isset($_GET['f_' . $filter['filters_id']]) && ($_GET['f_' . $filter['filters_id']] == $filter['filters_id'])) {
                $this->_content .= '<li class="clearfix"><input type="checkbox" name="f_' . $filter['filters_id'] . '" value="' . $filter['filters_id'] . '" checked="checked" /><label>' . $filter['filters_name'] . '</label></li>';
              }else {
                $this->_content .= '<li class="clearfix"><input type="checkbox" name="f_' . $filter['filters_id'] . '" value="' . $filter['filters_id'] . '" /><label>' . $filter['filters_name'] . '</label></li>';
              }
              
            }
                      
            $this->_content .= '</ul>';
            
            
            $this->_content .= '</div>';
          }
        }
        
        //add cPath
        if (isset($_GET['cPath']) && !empty($_GET['cPath'])) {
           $this->_content .= '<input type="hidden" name="cPath" value="' .$_GET['cPath'] . '" />';
        }
        
        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
           $this->_content .= '<input type="hidden" name="sort" value="' .$_GET['sort'] . '" />';
        }
        
        if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
           $this->_content .= '<input type="hidden" name="manufacturers" value="' .$_GET['manufacturers'] . '" />';
        }
        
        $this->_content .= '</form>';
        
        $this->_content .= '<button id="reset-filters" class="btn btn-block btn-primary">' . $osC_Language->get('Reset Filters') . '</button>';
      }
      
      //add css and javascript for this module
      $this->addCss();
      
      //add javascript for this module
      $this->addJavascript();
    }
    
    /*
     * Add css resouces for this module
     */
    function addCss() {
      global $osC_Template;
      
      $osC_Template->addStyleSheet('ext/qkform/qkform.css');
      
       //css
      $css = '.filterBox .toggleTrigger {margin:0;cursor:pointer;padding: 10px 0; font-size: 12px;line-height:20px;background:url("images/filterarrow_open.gif") no-repeat right center;}' .
          	 '.filterBox .toggleTrigger.triggerClosed {cursor:pointer;padding: 10px 0; font-size: 12px;line-height:20px;background:url("images/filterarrow_closed.gif") no-repeat right center;}' .
             '.filterBox .groupTitle {padding: 0 10px;background:#FAFAFA;}' . 
             '#frm-filters .filterBox .filters {padding: 0 10px;overflow:hidden;margin-bottom:1px;}' .
      			 '.filtersLoadingMask {position:absolute;background: #FFFFFF url("images/loading.gif") no-repeat center 20%;opacity: 0.5;}' .
      			 '#frm-filters .filterBox .filters li:last-child {border-bottom:0;}';
      
      
      $osC_Template->addStyleDeclaration($css);
    }
    
    /*
     * Add javascript resouces for this module
     */
    function addJavascript() {
      global $osC_Template;
      
      $osC_Template->addJavascriptFilename('ext/qkform/qkform.js');
      $osC_Template->addJavascriptFilename('includes/javascript/filters.js');
    }

    /*
     * Install the module
     */
    function install() {
      global $osC_Language, $osC_Database;

      parent::install();
      
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('" . $osC_Language->get('box_filters_show_product_count') . "', 'BOX_FILTERS_SHOW_PROUDCTS_COUNT', 'Yes', '" . $osC_Language->get('box_filters_show_product_description') . "', '6', '0', 'osc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_FILTERS_CACHE', '60', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }
    
    /*
     * Get the configuration keys for this module
     */
    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_FILTERS_SHOW_PROUDCTS_COUNT', 'BOX_FILTERS_CACHE');
      }

      return $this->_keys;
    }
  }
?>
