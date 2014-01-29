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
        $product_listing = $osC_Products->execute();
        
        $Qcategory_filters = $osC_Database->query('select ctf.filters_id, f.filters_groups_id, f.sort_order as filter_sort, fd.filters_name, fg.sort_order as group_sort, fgd.filters_groups_name from :table_categories_to_filters ctf inner join :table_filters f on ctf.filters_id = f.filters_id inner join :table_filters_description fd on (f.filters_id = fd.filters_id and fd.language_id = :language_id) inner join :table_filters_groups fg on f.filters_groups_id = fg.filters_groups_id inner join :table_filters_groups_description fgd on (f.filters_groups_id = fgd.filters_groups_id and fgd.language_id = :language_id) where ctf.categories_id = :categories_id order by fg.sort_order, f.sort_order');
        $Qcategory_filters->bindTable(':table_categories_to_filters', TABLE_CATEGORIES_TO_FILTERS);
        $Qcategory_filters->bindTable(':table_filters', TABLE_FILTERS);
        $Qcategory_filters->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
        $Qcategory_filters->bindTable(':table_filters_groups', TABLE_FILTERS_GROUPS);
        $Qcategory_filters->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
        $Qcategory_filters->bindInt(':categories_id', $current_category_id);
        $Qcategory_filters->bindInt(':language_id', $osC_Language->getID());
        $Qcategory_filters->bindInt(':language_id', $osC_Language->getID());
        
        //cache the database query results
        if (BOX_FILTERS_CACHE > 0) {
          $Qcategory_filters->setCache('box_filters-' . $current_category_id . '-' . $osC_Language->getCode(), BOX_FILTERS_CACHE);
        }
        
        $Qcategory_filters->execute();
        
        //build the filters groups
        if ($Qcategory_filters->numberOfRows() > 0) {
          while($Qcategory_filters->next()) {
            //verify whether the groups is pushed
            if (!isset($this->filters_groups[$Qcategory_filters->valueInt('filters_groups_id')])) {
              $this->filters_groups[$Qcategory_filters->valueInt('filters_groups_id')] = array('group_name' => $Qcategory_filters->value('filters_groups_name'), 
                                                                                               'group_sort' => $Qcategory_filters->value('group_sort'), 
                                                                                               'filters' => array());
            }
            
            if (defined('BOX_FILTERS_SHOW_PROUDCTS_COUNT') && BOX_FILTERS_SHOW_PROUDCTS_COUNT == 'Yes') {
              $count = $osC_Products->calculateProductsCount($Qcategory_filters->valueInt('filters_id'));
            }
            
            $this->filters_groups[$Qcategory_filters->valueInt('filters_groups_id')]['filters'][] = array('filters_id' => $Qcategory_filters->valueInt('filters_id'),
                                                                                                          'filters_name' => $Qcategory_filters->value('filters_name') . ' (' . $count . ')',
                                                                                                          'filter_sort' => $Qcategory_filters->valueInt('filter_sort'));
          }
        }
        
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
      $css = '.filterBox .toggleTrigger {cursor:pointer;padding: 10px 0; font-size: 12px;line-height:20px;}' .
             '.filterBox .groupTitle {padding: 0 10px;background:#FAFAFA;}' . 
             '.filterBox .filters {padding: 0 10px;overflow:hidden;}' .
      			 '.filtersLoadingMask {position:absolute;background: #FFFFFF url("images/loading.gif") no-repeat center 20%;opacity: 0.5;}';
      
      
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
