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

include_once('includes/classes/store.php');

class toC_Json_Store {
	/**
	 * List stores
	 *
	 * @access public
	 * @return string
	 */
	function listStores() {
		global $toC_Json;
	
		$records = toC_Store_Admin::listStores();
		
		$response = array(EXT_JSON_READER_TOTAL => sizeof($records), EXT_JSON_READER_ROOT => $records);
	
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
}