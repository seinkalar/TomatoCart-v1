<?php
/*
  $Id: product_filters.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  require(DIR_FS_CATALOG . DIR_FS_ADMIN . 'includes/classes/product_filters.php');
  
  class toC_Json_Product_Filters {
    /*
     * List the filters groups
     * 
     */
    function listProductFitlersGroups() {
      global $toC_Json;
      
      $start = empty($_POST['start']) ? 0 : $_POST['start']; 
      $limit = empty($_POST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_POST['limit'];

      $groups = osC_ProductFilters_Admin::getFiltersGroups($start, $limit);
      
      $response = array(EXT_JSON_READER_TOTAL => $groups['total'],
                        EXT_JSON_READER_ROOT => $groups['records']); 
                        
      echo $toC_Json->encode($response);
    }
    
    /*
     * Save the filter group
     * 
     */
    function saveFilterGroup() {
      global $toC_Json, $osC_Language;
      
      $filters_groups_names = $_POST['filters_groups_name'];
      $sort_order = $_POST['sort_order'];
      $filters_groups_id = $_POST['filters_groups_id'];
      
      $error = FALSE;
      if (isset($filters_groups_id) && (int)$filters_groups_id > 0) {
        if (!osC_ProductFilters_Admin::updateGroup($filters_groups_id, $filters_groups_names, $sort_order)) {
          $error = TRUE;
        }
      }else {
        if (!osC_ProductFilters_Admin::saveGroup($filters_groups_names, $sort_order)) {
          $error = TRUE;
        }
      }
      
      if ($error == FALSE) {
        $response = array('success' => TRUE ,'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => FALSE, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));   
      }
      
      echo $toC_Json->encode($response);
    }
    
    /*
     * Load the filter group
     * 
     */
    function loadFilterGroup() {
      global $toC_Json;
      
      $filter_group_id = $_POST['filters_groups_id'];
      
      $data = osC_ProductFilters_Admin::loadGroup($filter_group_id);
      
      $response = array('success' => TRUE, 'data' => $data); 
      
      echo $toC_Json->encode($response);
    }
    
    /*
     * Delete the filter group
     * 
     */
    function deleteFilterGroup() {
      global $toC_Json, $osC_Language;
      
      $filter_group_id = $_POST['groups_id'];
      
      if (osC_ProductFilters_Admin::deleteGroup($filter_group_id)) {
        $response = array('success' => TRUE ,'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => FALSE, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));  
      }
      
      echo $toC_Json->encode($response);
    }
    
    /*
     * List the filters
     * 
     */
    function listFilters() {
      global $toC_Json;
      
      $filter_group_id = $_POST['groups_id'];
      
      $start = empty($_POST['start']) ? 0 : $_POST['start']; 
      $limit = empty($_POST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_POST['limit'];
      
      $filters = osC_ProductFilters_Admin::getFilters($filter_group_id, $start, $limit);
      
      $response = array(EXT_JSON_READER_TOTAL => $filters['total'],
                        EXT_JSON_READER_ROOT => $filters['records']); 
                        
      echo $toC_Json->encode($response);
    }
    
    /*
     * Save filter
     * 
     */
    function saveFilter() {
      global $toC_Json, $osC_Language;
      
      $error = FALSE;
      
      $filter_group_id = $_POST['groups_id'];
      $filters_id = $_POST['filters_id'];
      $sort_order = $_POST['sort_order'];
      $filter_name = $_POST['filters_name'];
      
      //update filter
      if ((int)$filters_id > 0) {
        if (!osC_ProductFilters_Admin::updateFilter($filter_group_id, $filters_id, $sort_order, $filter_name)) {
          $error = TRUE;
        }
      //new filer
      }else {
        if (!osC_ProductFilters_Admin::saveFilter($filter_group_id, $sort_order, $filter_name)) {
          $error = TRUE;
        }
      }
      
      if ($error == FALSE) {
        $response = array('success' => TRUE ,'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => FALSE, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));   
      }
      
      echo $toC_Json->encode($response);
    }
    
    /*
     * Save filter
     * 
     */
    function loadFilter() {
      global $toC_Json;
      
      $filters_groups_id = $_POST['groups_id'];
      $filters_id = $_POST['filters_id'];
      
      $data = osC_ProductFilters_Admin::loadFilter($filters_groups_id, $filters_id);
      
      $response = array('success' => TRUE, 'data' => $data); 
      
      echo $toC_Json->encode($response);
    }
    
    /*
     * delete filter
     * 
     */
    function deleteFilter() {
      global $toC_Json, $osC_Language;
      
      $filters_groups_id = $_POST['groups_id'];
      $filters_id = $_POST['filters_id'];
      
      if (osC_ProductFilters_Admin::deleteFilter($filters_groups_id, $filters_id)) {
        $response = array('success' => TRUE ,'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => FALSE, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));   
      }
      
      echo $toC_Json->encode($response);
    }
    
    /*
     * batch delete filter
     * 
     */
    function deleteFilters() {
      global $toC_Json, $osC_Language;
      
      $error = FALSE;
      
      $filters_groups_id = $_POST['groups_id'];
      $filters_ids = json_decode($_POST['batch']);
      
      if (count($filters_ids) > 0) {
        foreach ($filters_ids as $filters_id) {
          if (!osC_ProductFilters_Admin::deleteFilter($filters_groups_id, $filters_id)) {
            $error = TRUE;
            break;
          }
        }
      }
      
      if ($error == FALSE) {
        $response = array('success' => TRUE ,'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => FALSE, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));   
      }
      
      echo $toC_Json->encode($response);
    }
  }
  