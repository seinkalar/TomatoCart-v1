<?php
/*
  $Id: length_classes.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  require('includes/classes/length_classes.php');

  class toC_Json_Length_Classes {
    function listLengthClasses() {
      global $toC_Json, $osC_Language, $osC_Database;
      
      $start = empty($_REQUEST['start']) ? 0 : $_REQUEST['start']; 
      $limit = empty($_REQUEST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_REQUEST['limit']; 
      
      $Qclasses = $osC_Database->query('select length_class_id, length_class_key, length_class_title from :table_length_classes where language_id = :language_id order by length_class_title');
      $Qclasses->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qclasses->bindInt(':language_id', $osC_Language->getID());
      $Qclasses->setExtBatchLimit($start, $limit);
      $Qclasses->execute();

      
      $record = array();
      while ( $Qclasses->next() ) {
        $class_name = $Qclasses->value('length_class_title');
    
        if ( $Qclasses->valueInt('length_class_id') == SHIPPING_LENGTH_UNIT ) {
          $class_name .= ' (' . $osC_Language->get('default_entry') . ')';
        }
        $record[] = array('length_class_title' => $class_name,
                          'length_class_id' => $Qclasses->value('length_class_id'),
                          'length_class_key' => $Qclasses->value('length_class_key'));         
      }
      
      $response = array(EXT_JSON_READER_TOTAL => $Qclasses->getBatchSize(),
                        EXT_JSON_READER_ROOT => $record); 
                        
      echo $toC_Json->encode($response);
      
    }
    
    function loadLengthClasses() {
      global $toC_Json, $osC_Language, $osC_Database;
      
      $data = osC_LengthClasses_Admin::getData($_REQUEST['length_class_id']);
      
      if ( $data['length_class_id'] == SHIPPING_LENGTH_UNIT ) {
        $data['is_default'] = 1; 
      }
      
      $Qwc = $osC_Database->query('select language_id, length_class_key, length_class_title from :table_length_classes where length_class_id = :length_class_id');
      $Qwc->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qwc->bindInt(':length_class_id', $_REQUEST['length_class_id']);
      $Qwc->execute();
      
      while ( $Qwc->next() ) {
        $data['name[' . $Qwc->ValueInt('language_id') . ']'] =  $Qwc->value('length_class_title');
        $data['key[' . $Qwc->ValueInt('language_id') . ']'] = $Qwc->value('length_class_key');
      }
      $Qwc->freeResult();
      
      $Qrules = $osC_Database->query('select r.length_class_to_id, r.length_class_rule, c.length_class_title, c.length_class_key from :table_length_classes_rules r, :table_length_classes c where r.length_class_from_id = :length_class_from_id and r.length_class_to_id != :length_class_to_id and r.length_class_to_id = c.length_class_id and c.language_id = :language_id order by c.length_class_title');
      $Qrules->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
      $Qrules->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qrules->bindInt(':length_class_from_id', $_REQUEST['length_class_id']);
      $Qrules->bindInt(':length_class_to_id', $_REQUEST['length_class_id']);
      $Qrules->bindInt(':language_id', $osC_Language->getID());
      $Qrules->execute();
        
      $rules = array();
      while ( $Qrules->next() ) {
        $rules[] = array('length_class_id' => $Qrules->value('length_class_to_id'),
                         'length_class_rule' => $Qrules->value('length_class_rule'),
                         'length_class_title' => $Qrules->value('length_class_title'));         
      }
      $Qrules->freeResult();
      
      $data['rules'] = $rules;
      
      $response = array('success' => true, 'data' => $data); 
      
      echo $toC_Json->encode($response);  
    }
    
    function getLengthClassesRules() {
      global $toC_Json, $osC_Language, $osC_Database;
      
      $Qrules = $osC_Database->query('select length_class_id, length_class_title from :table_length_classes where language_id = :language_id order by length_class_title');
      $Qrules->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qrules->bindInt(':language_id', $osC_Language->getID());
      $Qrules->execute();
        
      $rules = array();
      while ( $Qrules->next() ) {
        $rules[] = array( 'length_class_id' => $Qrules->value('length_class_id'),
                          'length_class_title' => $Qrules->value('length_class_title'));         
      }
      
      $response = array('rules' => $rules); 
      
      echo $toC_Json->encode($response);  
    }
    
    function saveLengthClasses() {
      global $toC_Json, $osC_Language, $osC_Database;
      
      $data = array('name' => $_REQUEST['name'],
                    'key' => $_REQUEST['key'],
                    'rules' => $_REQUEST['rules']);
      
      if ( osC_LengthClasses_Admin::save(($_REQUEST['length_class_id'] > 0 ? $_REQUEST['length_class_id'] : null), $data, ( isset($_REQUEST['is_default']) && ( $_REQUEST['is_default'] == 'on' ) ? true : false )) ) {
        $response = array('success' => true ,'feedback' => $osC_Language->get('ms_success_action_performed'));
      } else {
        $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));    
      }
      
      echo $toC_Json->encode($response);
    }
  
    function deleteLengthClass() {
      global $toC_Json, $osC_Database, $osC_Language;
      
      $error = false;
      $feedback = array();
      
      if ( $_REQUEST['length_classes_id'] == SHIPPING_LENGTH_UNIT ) {
        $error = true;
        $feedback[] = $osC_Language->get('delete_error_length_class_prohibited');
      } else {
      $Qcheck = $osC_Database->query('select count(*) as total from :table_products where products_length_class = :products_length_class');
      $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
      $Qcheck->bindInt(':products_length_class', $_REQUEST['length_classes_id']);
      $Qcheck->execute();
            
        if ( $Qcheck->valueInt('total') > 0 ) {
          $error = true;
          $feedback[] = sprintf($osC_Language->get('delete_error_length_class_in_use'), $Qcheck->valueInt('total'));
        }
      }
      
      if ($error === false) {
        if (osC_LengthClasses_Admin::delete( $_REQUEST['length_classes_id'])) {
          $response = array('success' => true ,'feedback' => $osC_Language->get('ms_success_action_performed'));
        } else {
          $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));    
        }
      } else {
        $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed') . '<br />' . implode('<br />', $feedback));
      }
      
      echo $toC_Json->encode($response);
    }
    
    function deleteLengthClasses() {
      global $toC_Json, $osC_Database, $osC_Language;
    
      $error = false;
      $feedback = array();
      
      $batch = explode(',', $_REQUEST['batch']);
      foreach ($batch as $id) {
        if ( $id == SHIPPING_LENGTH_UNIT ) {
          $error = true;
          $feedback[] = $osC_Language->get('delete_error_length_class_prohibited');
        } else {
          $Qcheck = $osC_Database->query('select count(*) as total from :table_products where products_length_class = :products_length_class');
          $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
          $Qcheck->bindInt(':products_length_class', $id);
          $Qcheck->execute();
              
          if ( $Qcheck->valueInt('total') > 0 ) {
            $error = true;
            $feedback[] = $osC_Language->get('batch_delete_error_length_class_in_use');
            break;
          }
        }
      }
      
      if ($error === false) {
        foreach ($batch as $id) {
          if ( !osC_LengthClasses_Admin::delete($id) ) {
            $error = true;
            break;
          }
        }
      
        if ($error === false) {
          $response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
        } else {
          $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
        }
      } else {
        $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed') . '<br />' . implode('<br />', $feedback));
      }
      
      echo $toC_Json->encode($response);
    }
}
?>
