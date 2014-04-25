<?php
/*
  $Id: product_filters.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as publish
*/

  class osC_ProductFilters_Admin {
    /*
     * Get the filters groups
     * 
     * @param $start - used for the pagination
     * @param $limit - used for the pagination
     * 
     * return array
     */
    function getFiltersGroups($start = NULL, $limit = NULL) {
      global $osC_Database, $osC_Language;
      
      $Qgroups = $osC_Database->query('select fg.*, fgd.filters_groups_name from :table_filters_groups fg inner join :table_filters_groups_description fgd on fg.filters_groups_id = fgd.filters_groups_id where fgd.language_id = :language_id');
      $Qgroups->bindTable(':table_filters_groups', TABLE_FILTERS_GROUPS);
      $Qgroups->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
      $Qgroups->bindInt(':language_id', $osC_Language->getID());
      
      if ($start !== NULL && $limit !== NULL) {
        $Qgroups->setExtBatchLimit($start, $limit);
      }
      
      $Qgroups->execute();
      
      $result = array('total' => 0, 'records' => array());
      if ($Qgroups->numberOfRows() > 0) {
        while($Qgroups->next()) {
          $result['records'][] = array('filters_groups_id' => $Qgroups->valueInt('filters_groups_id'), 
                                       'sort_order' => $Qgroups->valueInt('sort_order'), 
                                       'filters_groups_name' => $Qgroups->value('filters_groups_name'));
        }
      }
      
      $result['total'] = $Qgroups->getBatchSize();
      
      return $result;
    }
    
    /*
     * Save the filter group
     * 
     * @param $name - the group name
     * @param $sort_order - the sort order of the group
     * 
     * return bool
     */
    function saveGroup($name, $sort_order) {
      global $osC_Database, $osC_Language;
      
      $error = FALSE;
      
      //start transaction
      $osC_Database->startTransaction();
      
      //insert the filters groups
      $Qgroup = $osC_Database->query('insert into :table_filters_groups (sort_order) values (:sort_order)');
      $Qgroup->bindTable(':table_filters_groups', TABLE_FILTERS_GROUPS);
      $Qgroup->bindInt(':sort_order', $sort_order);
      $Qgroup->execute();
      
      if ($Qgroup->affectedRows() > 0){
        $group_id = $osC_Database->nextID();
      }else {
        $error = TRUE;
      }
      
      $Qgroup->freeResult();
      
      //insert descriptions of the filters groups 
      if ($error === FALSE) {
        foreach ($osC_Language->getAll() as $l) {
          $Qgroup_description = $osC_Database->query('insert into :table_filters_groups_description (filters_groups_id, language_id, filters_groups_name) values (:filters_groups_id, :language_id, :filters_groups_name)');
          $Qgroup_description->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
          $Qgroup_description->bindInt(':filters_groups_id', $group_id);
          $Qgroup_description->bindInt(':language_id', $l['id']);
          $Qgroup_description->bindValue(':filters_groups_name', $name[$l['id']]);
          $Qgroup_description->execute();
          
          if ($osC_Database->isError()) {
            $error = TRUE;
            break;
          }
        }
      }
      
      //commit
      if ($error == FALSE) {
        $osC_Database->commitTransaction();
        
        return TRUE;
      }
      
      //rollback
      $osC_Database->rollbackTransaction();
      
      return FALSE;
    }
    
    /*
     * Update the filter group
     * 
     * @param $id - the group id
     * @param $name - the group name
     * @param $sort_order - the sort order of the group
     * 
     * return bool
     */
    function updateGroup($id, $name, $sort_order) {
      global $osC_Database, $osC_Language;
       
      $error = FALSE;
       
      //start transaction
      $osC_Database->startTransaction();
      
      //update the filters groups
      $Qgroup = $osC_Database->query('update :table_filters_groups set sort_order = :sort_order where filters_groups_id = :filters_groups_id');
      $Qgroup->bindTable(':table_filters_groups', TABLE_FILTERS_GROUPS);
      $Qgroup->bindInt(':sort_order', $sort_order);
      $Qgroup->bindInt(':filters_groups_id', $id);
      $Qgroup->execute();
      
      if ($osC_Database->isError()) {
        $error = TRUE;
      }
      
      $Qgroup->freeResult();
      
      //update the filters groups description
      if ($error === FALSE) {
        foreach ($osC_Language->getAll() as $l) {
          $Qgroup_description = $osC_Database->query('update :table_filters_groups_description set filters_groups_name = :filters_groups_name where language_id = :language_id and filters_groups_id = :filters_groups_id');
          $Qgroup_description->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
          $Qgroup_description->bindInt(':language_id', $l['id']);
          $Qgroup_description->bindInt(':filters_groups_id', $id);
          $Qgroup_description->bindValue(':filters_groups_name', $name[$l['id']]);
          $Qgroup_description->execute();
          
          if ($osC_Database->isError()) {
            $error = TRUE;
            break;
          }
        }
      }
      
      //commit
      if ($error == FALSE) {
        $osC_Database->commitTransaction();
        
        return TRUE;
      }
      
      //rollback
      $osC_Database->rollbackTransaction();
      
      return FALSE;
    }
    
    /*
     * Load a filter group
     * 
     * @param $group_id - the group id
     * 
     * return array
     */
    function loadGroup($group_id) {
      global $osC_Database, $osC_Language;
      
      $Qgroups = $osC_Database->query('select fg.*, fgd.filters_groups_name, fgd.language_id from :table_filters_groups fg inner join :table_filters_groups_description fgd on fg.filters_groups_id = fgd.filters_groups_id where fg.filters_groups_id = :groups_id');
      $Qgroups->bindTable(':table_filters_groups', TABLE_FILTERS_GROUPS);
      $Qgroups->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
      $Qgroups->bindInt(':groups_id', $group_id);
      $Qgroups->execute();
      
      $result = array();
      if ($Qgroups->numberOfRows() > 0) {
        while($Qgroups->next()) {
          //groups id is same for different language
          if (!isset($result['filters_groups_id'])) {
            $result['filters_groups_id'] = $Qgroups->valueInt('filters_groups_id');
          }
          
          //sort order is same for different language
          if (!isset($result['sort_order'])) {
            $result['sort_order'] = $Qgroups->valueInt('sort_order');
          }
          
          $result['filters_groups_name[' . $Qgroups->valueInt('language_id') . ']'] = $Qgroups->value('filters_groups_name');
        }
      }
      
      return $result;
    }
    
    /*
     * Delete the filter group
     * 
     * @param $group_id - the group id
     * 
     * return bool
     */
    function deleteGroup($group_id) {
      global $osC_Database;
      
      $error = FALSE;
      
      //start transaction
      $osC_Database->startTransaction();
      
      //delete the group
      $Qdelete = $osC_Database->query('delete from :table_filters_group where filters_groups_id = :filters_groups_id');
      $Qdelete->bindTable(':table_filters_group', TABLE_FILTERS_GROUPS);
      $Qdelete->bindInt(':filters_groups_id', $group_id);
      $Qdelete->execute();
      
      if ($osC_Database->isError()) {
        $error = TRUE;
      }
      
      $Qdelete->freeResult();
      
      //delete the group description
      $Qdelete_description = $osC_Database->query('delete from :table_filters_groups_description where filters_groups_id = :filters_groups_id');
      $Qdelete_description->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
      $Qdelete_description->bindInt(':filters_groups_id', $group_id);
      $Qdelete_description->execute();
      
      if ($osC_Database->isError()) {
        $error = TRUE;
      }
      
      $Qdelete_description->freeResult();
      
      //commit
      if ($error === FALSE) {
        $osC_Database->commitTransaction();
        
        return TRUE;
      }
      
      //rollback
      $osC_Database->rollbackTransaction();
      
      return FALSE;
    }
    
    /*
     * Get the filters
     * 
     * @param $group_id - the group id
     * @param $start - used for the pagination
     * @param $limit - used for the pagination
     * 
     * return array
     */
    function getFilters($group_id, $start = NULL, $limit = NULL) {
      global $osC_Database, $osC_Language;
      
      $Qfilters = $osC_Database->query('select f.*, fd.filters_name from :table_filters f inner join :table_filters_description fd on f.filters_id = fd.filters_id where f.filters_groups_id = :filters_groups_id and fd.language_id = :language_id');
      $Qfilters->bindTable(':table_filters', TABLE_FILTERS);
      $Qfilters->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
      $Qfilters->bindInt(':filters_groups_id', $group_id);
      $Qfilters->bindInt(':language_id', $osC_Language->getID());
      
      if ($start !== NULL && $limit !== NULL) {
        $Qfilters->setExtBatchLimit($start, $limit);
      }
      
      $Qfilters->execute();
      
      $result = array('total' => 0, 'records' => array());
      while($Qfilters->next()) {
        $result['records'][] = $Qfilters->toArray();
      }
      
      $result['total'] = $Qfilters->getBatchSize();
      
      return $result;
    }
    
    /*
     * Save the filter
     * 
     * @param $filter_group_id - the filter group id
     * @param $sort_order - the sort order of the filter
     * @param $name - filter names for each language
     * 
     * return bool
     */
    function saveFilter($filter_group_id, $sort_order, $name) {
      global $osC_Database, $osC_Language;
      
      $error = FALSE;
      
      //start transaction
      $osC_Database->startTransaction();
      
      //save the filter
      $Qfilter = $osC_Database->query('insert into :table_filters (filters_groups_id, sort_order) values (:filters_groups_id, :sort_order)');
      $Qfilter->bindTable(':table_filters', TABLE_FILTERS);
      $Qfilter->bindInt(':filters_groups_id', $filter_group_id);
      $Qfilter->bindInt(':sort_order', $sort_order);
      $Qfilter->execute();
      
      if ($osC_Database->isError()) {
        $error = TRUE;
      }else {
        //get the new filter id
        $filter_id = $osC_Database->nextID();
        
        $Qfilter->freeResult();
        
        //save the filter description
        foreach ($osC_Language->getAll() as $l) {
          $Qfilter_description = $osC_Database->query('insert into :table_filters_description (filters_id, language_id, filters_name) values (:filters_id, :language_id, :filters_name)');
          $Qfilter_description->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
          $Qfilter_description->bindInt(':filters_id', $filter_id);
          $Qfilter_description->bindInt(':language_id', $l['id']);
          $Qfilter_description->bindValue(':filters_name', $name[$l['id']]);
          $Qfilter_description->execute();
          
          if ($osC_Database->isError()) {
            $error = TRUE;
            break;
          }
        }
      }
      
       //commit
      if ($error === FALSE) {
        $osC_Database->commitTransaction();
        
        return TRUE;
      }
      
      //rollback
      $osC_Database->rollbackTransaction();
      
      return FALSE;
    }
    
    /*
     * Update the filter
     * 
     * @param $filter_group_id - the filter group id
     * @param $filters_id - the filter id
     * @param $sort_order - the sort order of the filter
     * @param $name - filter names for each language
     * 
     * return bool
     */
    function updateFilter($filter_group_id, $filters_id, $sort_order, $name) {
      global $osC_Database, $osC_Language;
      
      $error = FALSE;
      
      //start transaction
      $osC_Database->startTransaction();
      
      //update the filter
      $Qfilter = $osC_Database->query('update :table_filters set sort_order = :sort_order where filters_groups_id = :filters_groups_id and filters_id = :filters_id');
      $Qfilter->bindTable(':table_filters', TABLE_FILTERS);
      $Qfilter->bindInt(':filters_groups_id', $filter_group_id);
      $Qfilter->bindInt(':filters_id', $filters_id);
      $Qfilter->bindInt(':sort_order', $sort_order);
      $Qfilter->execute();
      
      if ($osC_Database->isError()) {
        $error = TRUE;
      }
      
      $Qfilter->freeResult();
      
      if ($error === FALSE) {
        //update the filter description
        foreach ($osC_Language->getAll() as $l) {
          $Qfilter_description = $osC_Database->query('update :table_filters_description set filters_name = :filters_name where filters_id = :filters_id and language_id = :language_id');
          $Qfilter_description->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
          $Qfilter_description->bindInt(':filters_id', $filters_id);
          $Qfilter_description->bindInt(':language_id', $l['id']);
          $Qfilter_description->bindValue(':filters_name', $name[$l['id']]);
          $Qfilter_description->execute();
          
          if ($osC_Database->isError()) {
            $error = TRUE;
            break;
          }
        }
      }
      
       //commit
      if ($error === FALSE) {
        $osC_Database->commitTransaction();
        
        return TRUE;
      }
      
      //rollback
      $osC_Database->rollbackTransaction();
      
      return FALSE;
    }
    
    /*
     * Load the filter
     * 
     * @param $filters_groups_id - the filter group id
     * @param $filters_id - the filter id
     * 
     * return array
     */
    function loadFilter($filters_groups_id, $filters_id) {
      global $osC_Database, $osC_Language;
      
      $Qfilter = $osC_Database->query('select f.sort_order, fd.language_id, fd.filters_name from :table_filters f inner join :table_filters_description fd on f.filters_id = fd.filters_id where f.filters_id = :filters_id and f.filters_groups_id = :filters_groups_id');
      $Qfilter->bindTable(':table_filters', TABLE_FILTERS);
      $Qfilter->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
      $Qfilter->bindInt(':filters_id', $filters_id);
      $Qfilter->bindInt(':filters_groups_id', $filters_groups_id);
      $Qfilter->execute();
      
      $result = array();
      if ($Qfilter->numberOfRows() > 0) {
        while($Qfilter->next()) {
          //the sort order is same for each language
          if (!isset($result['sort_order'])) {
            $result['sort_order'] = $Qfilter->valueInt('sort_order');
          }
          
          $result['filters_name[' . $Qfilter->valueInt('language_id') . ']'] = $Qfilter->value('filters_name');
        }
      }
      
      return $result;
    }
    
    /*
     * Delete the filter
     * 
     * @param $filters_groups_id - the filter group id
     * @param $filters_id - the filter id
     * 
     * return bool
     */
    function deleteFilter($filters_groups_id, $filters_id) {
      global $osC_Database;
      
      $error = FALSE;
      
      //start transaction
      $osC_Database->startTransaction();
      
      //delete the filter
      $Qdelete = $osC_Database->query('delete from :table_filters where filters_groups_id = :filters_groups_id and filters_id = :filters_id');
      $Qdelete->bindTable(':table_filters', TABLE_FILTERS);
      $Qdelete->bindInt(':filters_groups_id', $filter_group_id);
      $Qdelete->bindInt(':filters_id', $filters_id);
      $Qdelete->execute();
      
      if ($osC_Database->isError()) {
        $error = TRUE;
      }
      
      $Qdelete->freeResult();
      
      //delete the filter description
      if ($error === FALSE) {
        $Qdelete_description = $osC_Database->query('delete from :table_filters_description where filters_id = :filters_id');
        $Qdelete_description->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
        $Qdelete_description->bindInt(':filters_id', $filters_id);
        $Qdelete_description->execute();
        
        if ($osC_Database->isError()) {
          $error = TRUE;
        }
        
        $Qdelete_description->freeResult();
      }
      
      //commit
      if ($error === FALSE) {
        $osC_Database->commitTransaction();
        
        return TRUE;
      }
      
      //rollback
      $osC_Database->rollbackTransaction();
      
      return FALSE;
    }
    
   /*
    * Save the configurations of products filters module
    *
    * @param $data array - configuration keys and values
    *
    * return bool
    */
    function saveConfigurations($data) {
    	global $osC_Database;
    	
      if (count($data) > 0) {
        foreach ($data as $configuration_key => $configuration_value) {
          $Qdelete = $osC_Database->query('delete from :table_configuration where configuration_key = :configuration_key');
          $Qdelete->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qdelete->bindValue(':configuration_key', $configuration_key);
          $Qdelete->execute();
          
          $Qinsert = $osC_Database->query('insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, date_added) vlaues (:configuration_title, :configuration_key, :configuration_value, :configuration_description, :configuration_group_id, now())');
          $Qinsert->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qinsert->bindValue(':configuration_title', TABLE_CONFIGURATION);
        }
      }else {
        return FALSE;
      }
    }
  }
?>