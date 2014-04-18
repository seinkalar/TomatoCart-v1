<?php
/*
  $Id: store.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

//include the store class - work as a model in mvc mode
include_once('includes/classes/store.php');

/**
 * Process the ajax request for adding or editing store
 * 
 * Work as a controller in mvc mode
 */
class toC_Json_Store {
	/**
	 * List stores
	 *
	 * @access public
	 * @return string
	 */
	function listStores() {
		global $toC_Json, $osC_Language;
		
		$start = empty($_POST['start']) ? 0 : $_POST['start'];
		$limit = empty($_POST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_POST['limit'];
		$search = !empty($_POST['search']) ? $_POST['search'] : null;
		
		$records = array();
		if ($start == 0) {
		  $records[] = array('store_id' => '0', 'store_name' => STORE_NAME . '<b> (' . $osC_Language->get('default_store') . '</b>)', 'url_address' => HTTP_SERVER, 'ssl_url_address' => HTTPS_SERVER);
		}
		
		$result = toC_Store_Admin::listStores($start, $limit, $search);
		
		$records = array_merge($records, toC_Store_Admin::listStores($start, $limit, $search));
		
		$response = array(EXT_JSON_READER_TOTAL => sizeof($records), EXT_JSON_READER_ROOT => $records);
	
		echo $toC_Json->encode($response);
	}
	
	/**
	 * Load store
	 *
	 * @access public
	 * @return string
	 */
	function loadStore() {
		global $toC_Json;
		
		$store_id = $_POST['store_id'];
		
		$data = toC_Store_Admin::load($store_id);
		
		$response = array('success' => TRUE, 'data' => $data); 
      
		echo $toC_Json->encode($response);
	}
	
	/**
	 * Delete store
	 * 
	 * @access public
	 * @return string
	 */
	function deleteStore() {
		global $toC_Json, $osC_Language;
		
		$store_id = $_POST['store_id'];
		$error = false;
		
		if ((int)$store_id > 0) {
			if (!toC_Store_Admin::deleteStore($store_id)) {
				$error = true;
			}
		}else {
		  $error = true;
		}
		
		if ($error === false) {
			$response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
		}else {
			$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
		}
		
		echo $toC_Json->encode($response);
	}
	
	/**
	 * List installed templates
	 * 
	 * @access public
	 * @return string
	 */
	function listTemplates() {
		global $toC_Json;
		
		$templates = toC_Store_Admin::getTemplates();
		
		$records = array();
		if (count($templates) > 0) {
		  foreach($templates as $template) {
		    $records[] = array('template_code' => $template['code'], 'template_name' => $template['title']);
		  }
		}
		
		$response = array(EXT_JSON_READER_TOTAL => sizeof($records), EXT_JSON_READER_ROOT => $records);
		
		echo $toC_Json->encode($response);
	}
	
	/**
	 * List installed languages
	 *
	 * @access public
	 * @return string
	 */
	function listLanguages() {
		global $toC_Json;
	
		$records = toC_Store_Admin::getLanguages();
	
		$response = array(EXT_JSON_READER_TOTAL => sizeof($records), EXT_JSON_READER_ROOT => $records);
	
		echo $toC_Json->encode($response);
	}
	
	/**
	 * List installed currencies
	 *
	 * @access public
	 * @return string
	 */
	function listCurrencies() {
		global $toC_Json;
	
		$records = toC_Store_Admin::getCurrencies();
	
		$response = array(EXT_JSON_READER_TOTAL => sizeof($records), EXT_JSON_READER_ROOT => $records);
	
		echo $toC_Json->encode($response);
	}
	
	/**
	 * Save store
	 *
	 * @access public
	 * @return string
	 */
	function saveStore() {
	  global $toC_Json, $osC_Language;
	  
	  $error = false;
	  $response = array();
	  $configurations = array();
	  
	  $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : 0;
	  
	  //required fields
	  if ( !(isset($_POST['store_url']) && filter_var($_POST['store_url'], FILTER_VALIDATE_URL)) ) {
	    $error = true;
	    
	    $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_wrong_store_url_format'));
	  }else {
	    $configurations['store_url'] = $_POST['store_url'];
	  }
	  
	  if ($error === false) {
	    if ( !(isset($_POST['store_name']) && !empty($_POST['store_name'])) ) {
	    	$error = true;
	    	 
	    	$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_wrong_store_name'));
	    }else {
	      $configurations['store_name'] = $_POST['store_name'];
	    }
	  }
	  
	  if ($error === false) {
	  	if ( !(isset($_POST['store_owner']) && !empty($_POST['store_owner'])) ) {
	  		$error = true;
	  		 
	  		$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_wrong_store_owner'));
	  	}else {
	  	  $configurations['store_owner'] = $_POST['store_owner'];
	  	}
	  }
	  
	  if ($error === false) {
	  	if ( !(isset($_POST['store_email_address']) && filter_var($_POST['store_email_address'], FILTER_VALIDATE_EMAIL)) ) {
	  		$error = true;
	  
	  		$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_wrong_email_address_format'));
	  	}else {
	  		$configurations['store_email_address'] = $_POST['store_email_address'];
	  	}
	  }
	  
	  if ($error === false) {
	  	if ( !(isset($_POST['store_email_from']) && filter_var($_POST['store_email_from'], FILTER_VALIDATE_EMAIL)) ) {
	  		$error = true;
	  		 
	  		$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_wrong_email_from_format'));
	  	}else {
	  		$configurations['store_email_from'] = $_POST['store_email_from'];
	  	}
	  }
	  
	  if ($error === false) {
	  	if ( !(isset($_POST['store_address_phone']) && !empty($_POST['store_address_phone'])) ) {
	  		$error = true;
	  
	  		$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_wrong_store_address_phone'));
	  	}else {
	  		$configurations['store_address_phone'] = $_POST['store_address_phone'];
	  	}
	  }
	  
	  //other non-repquired configurations
	  if ($error === false) {
	  	if (isset($_POST['ssl_url']) && !empty($_POST['ssl_url'])) {
	  		$configurations['ssl_url'] = $_POST['ssl_url'];
	  	}
	  	 
	  	if (isset($_POST['store_template_code']) && !empty($_POST['store_template_code'])) {
	  		$configurations['store_template_code'] = $_POST['store_template_code'];
	  	}
	  	 
	  	if (isset($_POST['countries_id']) && !empty($_POST['countries_id'])) {
	  		$configurations['countries_id'] = $_POST['countries_id'];
	  	}
	  	 
	  	if (isset($_POST['zone_id']) && !empty($_POST['zone_id'])) {
	  		$configurations['zone_id'] = $_POST['zone_id'];
	  	}
	  	 
	  	if (isset($_POST['language_code']) && !empty($_POST['language_code'])) {
	  		$configurations['language_code'] = $_POST['language_code'];
	  	}
	  	 
	  	if (isset($_POST['currency_code']) && !empty($_POST['currency_code'])) {
	  		$configurations['currency_code'] = $_POST['currency_code'];
	  	}
	  	 
	  	if (isset($_POST['time_zone']) && !empty($_POST['time_zone'])) {
	  		$configurations['time_zone'] = $_POST['time_zone'];
	  	}
	  	 
	  	if (isset($_POST['maintenance_mode']) && !empty($_POST['maintenance_mode'])) {
	  		$configurations['maintenance_mode'] = $_POST['maintenance_mode'];
	  	}
	  	 
	  	if (isset($_POST['display_prices_with_tax']) && !empty($_POST['display_prices_with_tax'])) {
	  		$configurations['display_prices_with_tax'] = $_POST['display_prices_with_tax'];
	  	}
	  	 
	  	if (isset($_POST['dislay_products_recursively']) && !empty($_POST['dislay_products_recursively'])) {
	  		$configurations['dislay_products_recursively'] = $_POST['dislay_products_recursively'];
	  	}
	  	 
	  	if (isset($_POST['synchronize_cart_with_database']) && !empty($_POST['synchronize_cart_with_database'])) {
	  		$configurations['synchronize_cart_with_database'] = $_POST['synchronize_cart_with_database'];
	  	}
	  	 
	  	if (isset($_POST['show_confirmation_dialog']) && !empty($_POST['show_confirmation_dialog'])) {
	  		$configurations['show_confirmation_dialog'] = $_POST['show_confirmation_dialog'];
	  	}
	  	 
	  	if (isset($_POST['check_stock_level']) && !empty($_POST['check_stock_level'])) {
	  		$configurations['check_stock_level'] = $_POST['check_stock_level'];
	  	}
	  	 
	  	if (isset($_POST['subtract_stock']) && !empty($_POST['subtract_stock'])) {
	  		$configurations['subtract_stock'] = $_POST['subtract_stock'];
	  	}
	  	 
	  	if (isset($_POST['allow_checkout']) && !empty($_POST['allow_checkout'])) {
	  		$configurations['allow_checkout'] = $_POST['allow_checkout'];
	  	}
	  	 
	  	if (isset($_POST['mark_out_of_stock']) && !empty($_POST['mark_out_of_stock'])) {
	  		$configurations['mark_out_of_stock'] = $_POST['mark_out_of_stock'];
	  	}
	  	 
	  	if (isset($_POST['stock_reorder_level']) && !empty($_POST['stock_reorder_level'])) {
	  		$configurations['stock_reorder_level'] = $_POST['stock_reorder_level'];
	  	}
	  	 
	  	if (isset($_POST['stock_email_alerts']) && !empty($_POST['stock_email_alerts'])) {
	  		$configurations['stock_email_alerts'] = $_POST['stock_email_alerts'];
	  	}
	  	 
	  	if (isset($_POST['stock_email_alerts']) && !empty($_POST['stock_email_alerts'])) {
	  		$configurations['check_stock_cart_synchronization'] = $_POST['check_stock_cart_synchronization'];
	  	}
	  	 
	  	if (isset($_POST['search_results']) && !empty($_POST['search_results'])) {
	  		$configurations['search_results'] = $_POST['search_results'];
	  	}
	  	 
	  	if (isset($_POST['list_per_row']) && !empty($_POST['list_per_row'])) {
	  		$configurations['list_per_row'] = $_POST['list_per_row'];
	  	}
	  	 
	  	if (isset($_POST['new_products_listing']) && !empty($_POST['new_products_listing'])) {
	  		$configurations['new_products_listing'] = $_POST['new_products_listing'];
	  	}
	  	 
	  	if (isset($_POST['search_results_auto_completer']) && !empty($_POST['search_results_auto_completer'])) {
	  		$configurations['search_results_auto_completer'] = $_POST['search_results_auto_completer'];
	  	}
	  	 
	  	if (isset($_POST['product_name_auto_completer']) && !empty($_POST['product_name_auto_completer'])) {
	  		$configurations['product_name_auto_completer'] = $_POST['product_name_auto_completer'];
	  	}
	  	 
	  	if (isset($_POST['width_auto_completer']) && !empty($_POST['width_auto_completer'])) {
	  		$configurations['width_auto_completer'] = $_POST['width_auto_completer'];
	  	}
	  }
	  
	  //call mode to save the configurations of the store
	  if (count($configurations) > 0) {
	    if (!toC_Store_Admin::save($store_id, $configurations)) {
	      $error = true;
	    }
	  }
	  
	  
	  if ($error === false) {
	  	$response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
	  }else {
	  	$response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
	  }
	  
	  echo $toC_Json->encode($response);
	}
	
}

?>