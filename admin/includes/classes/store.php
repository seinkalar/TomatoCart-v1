<?php
/*
  $Id: store.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

class toC_Store_Admin {
	/**
	 * List the store
	 *
	 * @access public static
	 * @return array
	 */
	function listStores($start, $limit, $search = null) {
		global $osC_Database;
		
		$Qstores = $osC_Database->query('select * from :table_store');
		
		if ($search !== null) {
			$Qstores->appendQuery('where store_name like :store_name or url_address like :url_address or ssl_url_address like :ssl_url_address');
			$Qstores->bindValue(':store_name', '%' . $search . '%');
			$Qstores->bindValue(':url_address', '%' . $search . '%');
			$Qstores->bindValue(':ssl_url_address', '%' . $search . '%');
		}
		
		$Qstores->appendQuery('order by store_id');
		$Qstores->bindTable(':table_store', TABLE_STORE);
		$Qstores->setExtBatchLimit($start, $limit);
		$Qstores->execute();
		
		$stores = array();
		if ($Qstores->numberOfRows() > 0) {
			while ($Qstores->next()) {
				$stores[] = array(
						'store_id' => $Qstores->ValueInt('store_id'),
						'store_name' => $Qstores->Value('store_name'),
						'url_address' => $Qstores->Value('url_address'),
						'ssl_url_address' => $Qstores->Value('ssl_url_address'),
				);
			}
		}
		
		return $stores;
	}
	
	/**
	 * Delete the store
	 * 
	 * @access public static
	 * @param int store id
	 * @return boolean
	 */
	function deleteStore($id) {
		global $osC_Database;
		
		//delete store settings
		$Qdelete_settings = $osC_Database->query('delete from :table_configuration where store_id = :store_id');
		$Qdelete_settings->bindTable(':table_configuration', TABLE_CONFIGURATION);
		$Qdelete_settings->bindInt(':store_id', $id);
		$Qdelete_settings->execute();
		
		//delete store
		$Qdelete_store = $osC_Database->query('delete from :table_store where store_id = :store_id');
		$Qdelete_store->bindTable(':table_store', TABLE_STORE);
		$Qdelete_store->bindInt(':store_id', $id);
		$Qdelete_store->execute();
		
		if ($Qdelete_store->affectedRows() > 0) {
			osC_Cache::clear('box');
			osC_Cache::clear('categories');
			osC_Cache::clear('configuration');
			osC_Cache::clear('currencies');
			osC_Cache::clear('category_tree');
			
			osC_Cache::clear('product');
			
			osC_Cache::clear('also_purchased');
			osC_Cache::clear('sefu-products');
			osC_Cache::clear('new_products');
			osC_Cache::clear('feature-products');
			
		  return true;
		}
		
		return false;
	}
	
	/**
	 * Get installed templates
	 *
	 * @access public static
	 * @return array
	 */
	function getTemplates() {
		global $osC_Database;
		
		$Qtemplates = $osC_Database->query('select code, title from :table_templates');
		$Qtemplates->bindTable(':table_templates', TABLE_TEMPLATES);
		$Qtemplates->execute();
		
		$templates = array();
		if ($Qtemplates->numberOfRows() > 0) {
		  while ($Qtemplates->next()) {
		  	$templates[] = array('code' => $Qtemplates->value('code'), 'title' => $Qtemplates->value('title'));
		  }
		}
		
		$Qtemplates->freeResult();
		
		return $templates;
	}
	
	/**
	 * Get installed languages
	 *
	 * @access public static
	 * @return array
	 */
	function getLanguages() {
		global $osC_Database;
		
		$Qlanguages = $osC_Database->query('select code, name from :table_languages');
		$Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
		$Qlanguages->execute();
		
		$languages = array();
		if ($Qlanguages->numberOfRows() > 0) {
			while ($Qlanguages->next()) {
				$languages[] = array('code' => $Qlanguages->value('code'), 'name' => $Qlanguages->value('name'));
			}
		}
		
		$Qlanguages->freeResult();
		
		return $languages;
	}
	
	/**
	 * Get installed currencies
	 *
	 * @access public static
	 * @return array
	 */
	function getCurrencies() {
		global $osC_Database;
	
		$Qcurrencies = $osC_Database->query('select code, title from :table_currencies');
		$Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
		$Qcurrencies->execute();
	
		$currencies = array();
		if ($Qcurrencies->numberOfRows() > 0) {
			while ($Qcurrencies->next()) {
				$currencies[] = array('code' => $Qcurrencies->value('code'), 'title' => $Qcurrencies->value('title'));
			}
		}
	
		$Qcurrencies->freeResult();
	
		return $currencies;
	}
	
	/**
	 * Load Store
	 *
	 * @access public static
	 * @param int the store id
	 * @return mixed
	 */
	function load($store_id) {
	  global $osC_Database;
	  
	  $Qstore = $osC_Database->query('select store_name, url_address, ssl_url_address from :table_store where store_id = :store_id');
	  $Qstore->bindTable(':table_store', TABLE_STORE);
	  $Qstore->bindInt(':store_id', $store_id);
	  $Qstore->execute();
	  
	  
	  if ($Qstore->numberOfRows() > 0) {
	  	$result = array('store_url' => $Qstore->value('url_address'), 'ssl_url' => $Qstore->value('ssl_url_address'));
	  	
	  	//get keys database to form
	  	$form_to_database = self::getKeys();
	  	$database_to_form = array_flip($form_to_database);
	  	
	  	//store configurations
	  	$Qconfigurations = $osC_Database->query('select * from :table_configuration where store_id = :store_id');
	  	$Qconfigurations->bindTable(':table_configuration', TABLE_CONFIGURATION);
	  	$Qconfigurations->bindInt(':store_id', $store_id);
	  	$Qconfigurations->execute();
	  	
	  	if ($Qconfigurations->numberOfRows() > 0) {
	  	  while ($Qconfigurations->next()) {
	  	  	$result[$database_to_form[$Qconfigurations->value('configuration_key')]] = $Qconfigurations->value('configuration_value');
	  	  }
	  	}
	  	
	  	$Qconfigurations->freeResult();
	  	
	  	return $result;
	  }
	  
	  $Qstore->freeResult();
	  
	  return null;
	}
	
	/**
	 * Save store
	 *
	 * @access public static
	 * @param array configurations of store
	 * @return boolean
	 */
	function save($store_id, $configurations = array()) {
		global $osC_Database;
		
		$error = false;
		
		$osC_Database->startTransaction();
		
		//begin: save store
	  if (count($configurations)  > 0) {
	  	//get keys from form to database
	  	$form_to_database = self::getKeys();
	  	
	  	//insert the new store
	  	if ($store_id == 0) {
	  	  $Qstore = $osC_Database->query('insert into :table_store (store_name, url_address, ssl_url_address) values (:store_name, :url_address, :ssl_url_address)');
	  	}else {
	  		$Qstore = $osC_Database->query('update :table_store set store_name = :store_name, url_address = :url_address, ssl_url_address = :ssl_url_address where store_id = :store_id');
	  		$Qstore->bindInt(':store_id', $store_id);
	  	}
	  	
	  	$Qstore->bindTable(':table_store', TABLE_STORE);
	  	$Qstore->bindValue(':store_name', $configurations['store_name']);
	  	$Qstore->bindValue(':url_address', $configurations['store_url']);
	  	
	  	if (isset($configurations['ssl_url'])) {
	  		$Qstore->bindValue(':ssl_url_address', $configurations['ssl_url']);
	  	}else {
	  		$Qstore->bindValue(':ssl_url_address', $configurations['store_url']);
	  	}
	  	
	  	$Qstore->execute();
	  	
	  	$current_store_id = ($store_id > 0 ? $store_id : $osC_Database->nextID());
	  	
	  	if ($osC_Database->isError()) {
	  		$error = true;
	  	}
	  	
	  	//Begin: insert store configurations
	  	if ($error === false) {
	  		//delete configurations to update
	  		if ($store_id > 0) {
	  			$Qdelete = $osC_Database->query('delete from :table_configuration where store_id = :store_id');
	  			$Qdelete->bindTable(':table_configuration', TABLE_CONFIGURATION);
	  			$Qdelete->bindInt(':store_id', $store_id);
	  			$Qdelete->execute();
	  			
	  			if ($osC_Database->isError()) {
	  				$error = true;
	  			}
	  		}
	  		
	  		//insert configurations
	  		foreach ($configurations as $config_key => $config_value) {
	  			//store url is already saved in the store table
	  			if ($config_key == 'store_url' || $config_key == 'ssl_url') {
	  			  continue;
	  			}
	  			
					if (isset($form_to_database[$config_key])) {
						//get configuration info
						$Qinfo = $osC_Database->query('select configuration_title, configuration_description, configuration_group_id from :table_configuration where configuration_key = :configuration_key');
						$Qinfo->bindTable(':table_configuration', TABLE_CONFIGURATION);
						$Qinfo->bindValue(':configuration_key', $form_to_database[$config_key]);
						$Qinfo->execute();
						
						$information = $Qinfo->toArray();
						
						$Qinfo->freeResult();
						
						$Qconfiguration = $osC_Database->query('insert into :table_configuration (store_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id) values (:store_id, :configuration_title, :configuration_key, :configuration_value, :configuration_description, :configuration_group_id)');
						$Qconfiguration->bindTable(':table_configuration', TABLE_CONFIGURATION);
						$Qconfiguration->bindInt(':store_id', $current_store_id);
						$Qconfiguration->bindValue(':configuration_title', $information['configuration_title']);
						$Qconfiguration->bindValue(':configuration_key', $form_to_database[$config_key]);
						$Qconfiguration->bindValue(':configuration_value', $config_value);
						$Qconfiguration->bindValue(':configuration_description', $information['configuration_description']);
						$Qconfiguration->bindValue(':configuration_group_id', $information['configuration_group_id']);
						$Qconfiguration->execute();
						
						if ($osC_Database->isError()) {
							$error = true;
							break;
						}
					}
				//end: foreach	
	  		}
  		//end: insert store configurations
	  	}
  	//end: save store
	  }
	  
	  if ($error === false) {
	  	$osC_Database->commitTransaction();
	  	
	  	osC_Cache::clear('box');
			osC_Cache::clear('categories');
			osC_Cache::clear('configuration');
			osC_Cache::clear('currencies');
			osC_Cache::clear('category_tree');
			
			osC_Cache::clear('product');
			
			osC_Cache::clear('also_purchased');
			osC_Cache::clear('sefu-products');
			osC_Cache::clear('new_products');
			osC_Cache::clear('feature-products');
	  	
	    return true;
	  }
	  
	  $osC_Database->rollbackTransaction();
	  
	  return false;
	}
	
	/**
	 * Return keys to values from from to database
	 *
	 * @access private static
	 * @return array
	 */
	function getKeys() {
		$form_to_database = array(
				'store_name'  => 'STORE_NAME',
				'store_owner' => 'STORE_OWNER',
				'store_email_address' => 'STORE_OWNER_EMAIL_ADDRESS',
				'store_email_from' => 'EMAIL_FROM',
				'store_address_phone' => 'STORE_NAME_ADDRESS',
				'store_template_code' => 'DEFAULT_TEMPLATE',
				'countries_id' => 'STORE_COUNTRY',
				'zone_id' => 'STORE_ZONE',
				'time_zone' => 'STORE_TIME_ZONE',
				'language_code' => 'DEFAULT_LANGUAGE',
				'currency_code' => 'DEFAULT_CURRENCY',
				'maintenance_mode' => 'MAINTENANCE_MOD',
				'display_prices_with_tax' => 'DISPLAY_PRICE_WITH_TAX',
				'dislay_products_recursively' => 'DISPLAY_SUBCATALOGS_PRODUCTS',
				'synchronize_cart_with_database' => 'SYNCHRONIZE_CART_WITH_DATABASE',
				'show_confirmation_dialog' => 'ENABLE_CONFIRMATION_DIALOG',
				'check_stock_level' => 'STOCK_CHECK',
				'subtract_stock' => 'STOCK_LIMITED',
				'allow_checkout' => 'STOCK_ALLOW_CHECKOUT',
				'mark_out_of_stock' => 'STOCK_MARK_PRODUCT_OUT_OF_STOCK',
				'stock_reorder_level' => 'STOCK_REORDER_LEVEL',
				'stock_email_alerts' => 'STOCK_EMAIL_ALERT',
				'check_stock_cart_synchronization' => 'CHECK_STOCKS_SYNCHRONIZE_CART_WITH_DATABASE',
				'search_results' => 'MAX_DISPLAY_SEARCH_RESULTS',
				'list_per_row' => 'MAX_DISPLAY_CATEGORIES_PER_ROW',
				'new_products_listing' => 'MAX_DISPLAY_PRODUCTS_NEW',
				'search_results_auto_completer' => 'MAX_DISPLAY_AUTO_COMPLETER_RESULTS',
				'product_name_auto_completer' => 'MAX_CHARACTERS_AUTO_COMPLETER',
				'width_auto_completer' => 'WIDTH_AUTO_COMPLETER'
		);
		
		return $form_to_database;
	}
}