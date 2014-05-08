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
		
		$error = false;
		
		$osC_Database->startTransaction();
		
		//delete store settings
		$Qdelete_settings = $osC_Database->query('delete from :table_configuration where store_id = :store_id');
		$Qdelete_settings->bindTable(':table_configuration', TABLE_CONFIGURATION);
		$Qdelete_settings->bindInt(':store_id', $id);
		$Qdelete_settings->execute();
		
		if ($osC_Database->isError()) {
		  $error = true;
		}
		
		//delete articles categories to stores
		if ($error == false) {
		  $Qarticles_categories_stores = $osC_Database->query('delete from :table_articles_categories_to_stores where stores_id = :stores_id');
		  $Qarticles_categories_stores->bindTable(':table_articles_categories_to_stores', TABLE_ARTICLES_CATEGORIES_TO_STORES);
		  $Qarticles_categories_stores->bindInt(':stores_id', $id);
		  $Qarticles_categories_stores->execute();
		  
		  if ($osC_Database->isError()) {
		  	$error = true;
		  }
		}
		
		//delete articles to stores
		if ($error == false) {
			$Qarticles_stores = $osC_Database->query('delete from :table_articles_to_stores where stores_id = :stores_id');
			$Qarticles_stores->bindTable(':table_articles_to_stores', TABLE_ARTICLES_TO_STORES);
			$Qarticles_stores->bindInt(':stores_id', $id);
			$Qarticles_stores->execute();
		
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//delete categories to stores
		if ($error == false) {
			$Qcategories_stores = $osC_Database->query('delete from :table_categories_to_stores where stores_id = :stores_id');
			$Qcategories_stores->bindTable(':table_categories_to_stores', TABLE_CATEGORIES_TO_STORES);
			$Qcategories_stores->bindInt(':stores_id', $id);
			$Qcategories_stores->execute();
		
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//delete faqs to stores
		if ($error == false) {
			$Qfaqs_stores = $osC_Database->query('delete from :table_faqs_to_stores where stores_id = :stores_id');
			$Qfaqs_stores->bindTable(':table_faqs_to_stores', TABLE_FAQS_TO_STORES);
			$Qfaqs_stores->bindInt(':stores_id', $id);
			$Qfaqs_stores->execute();
		
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//delete manufacturers to stores
		if ($error == false) {
			$Qmanufacturers_stores = $osC_Database->query('delete from :table_manufacturers_to_stores where stores_id = :stores_id');
			$Qmanufacturers_stores->bindTable(':table_manufacturers_to_stores', TABLE_MANUFACTURERS_TO_STORES);
			$Qmanufacturers_stores->bindInt(':stores_id', $id);
			$Qmanufacturers_stores->execute();
		
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//delete products to stores
		if ($error == false) {
			$Qproducts_stores = $osC_Database->query('delete from :table_products_to_stores where stores_id = :stores_id');
			$Qproducts_stores->bindTable(':table_products_to_stores', TABLE_PRODUCTS_TO_STORES);
			$Qproducts_stores->bindInt(':stores_id', $id);
			$Qproducts_stores->execute();
		
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//delete slide images to stores
		if ($error == false) {
			$Qslide_images_stores = $osC_Database->query('delete from :table_slide_images_to_stores where stores_id = :stores_id');
			$Qslide_images_stores->bindTable(':table_slide_images_to_stores', TABLE_SLIDE_IMAGES_TO_STORES);
			$Qslide_images_stores->bindInt(':stores_id', $id);
			$Qslide_images_stores->execute();
		
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//delete store logo
		if ($error == false) {
			self::deleteLogo('originals_' . $id);
			
			$osC_DirectoryListing = new osC_DirectoryListing('../templates');
			$osC_DirectoryListing->setIncludeDirectories(true);
			$osC_DirectoryListing->setIncludeFiles(false);
			$osC_DirectoryListing->setExcludeEntries('system');
			
			$templates = $osC_DirectoryListing->getFiles();
			
			foreach ($templates as $template) {
				$code = $template['name'];
			
				self::deleteLogo($code . '_' . $id);
			}
		}
		
		//delete store
		if ($error == false) {
			$Qdelete_store = $osC_Database->query('delete from :table_store where store_id = :store_id');
			$Qdelete_store->bindTable(':table_store', TABLE_STORE);
			$Qdelete_store->bindInt(':store_id', $id);
			$Qdelete_store->execute();
			
			if ($osC_Database->isError()) {
				$error = true;
			}
		}
		
		//clear cache
		if ($error == false) {
			$osC_Database->commitTransaction();
			
			osC_Cache::clear('box');
			osC_Cache::clear('categories');
			osC_Cache::clear('configuration');
			osC_Cache::clear('currencies');
			osC_Cache::clear('category_tree');
			osC_Cache::clear('slide-images');
				
			osC_Cache::clear('product');
				
			osC_Cache::clear('also_purchased');
			osC_Cache::clear('sefu-products');
			osC_Cache::clear('new_products');
			osC_Cache::clear('feature-products');
			osC_Cache::clear('upcoming-products');
				
			return true;
		}
		
		$osC_Database->rollbackTransaction();
		
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
	  	  	$configuration_key = $Qconfigurations->value('configuration_key');
	  	  	//meta info configurations
	  	  	if (preg_match('/^(HOME_PAGE_TITLE)_(\w+)$/', $configuration_key, $matches) || preg_match('/^(HOME_META_KEYWORD)_(\w+)$/', $configuration_key, $matches) || preg_match('/^(HOME_META_DESCRIPTION)_(\w+)$/', $configuration_key, $matches)) {
	  	  		$meta_info_key = $matches[1] . '[' . $matches[2] . ']';
	  	  		
	  	  	  $result[$meta_info_key] = $Qconfigurations->value('configuration_value');
  	  	  //other general configurations
	  	  	}else {
	  	  		$result[$database_to_form[$Qconfigurations->value('configuration_key')]] = $Qconfigurations->value('configuration_value');
	  	  	}
	  	  }
	  	}
	  	
	  	$Qconfigurations->freeResult();
	  	
	  	//store logo
	  	$store_logo = self::getOriginalLogo($store_id);
	  	if ($store_logo !== false) {
	  		$result['store_logo'] = $store_logo;
	  	}
	  	
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
		
		$edit_action = false;
		if ($store_id > 0) {
		  $edit_action = true;
		}
		
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
	  	
	  	//store logo
	  	if (!self::saveLogo($store_id, $current_store_id)) {
	  		$error = true;
	  	}
	  	
	  	//Begin: insert store configurations
	  	if ($error === false) {
	  		//insert configurations
	  		foreach ($configurations as $config_key => $config_value) {
	  			//store url is already saved in the store table
	  			if ($config_key == 'store_url' || $config_key == 'ssl_url') {
	  			  continue;
	  			}
	  			
	  			//meta info
	  			if ($config_key == 'page_title' || $config_key == 'keywords' || $config_key == 'descriptions') {
	  				foreach($config_value as $key => $value) {
	  					//edit
	  					if ($edit_action) {
	  						$Qmeta_meta_info = $osC_Database->query("update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key and store_id = :store_id");
	  					//new
	  					}else {
	  					  $Qmeta_meta_info = $osC_Database->query('insert into :table_configuration (store_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, date_added) values (:store_id, :configuration_title, :configuration_key, :configuration_value, :configuration_description, 6, now())');
	  					}
	  					
	  					switch($config_key) {
	  					  case 'page_title':
	  					  	$configuration_key = 'HOME_PAGE_TITLE_' . $key;
	  					  	
	  					  	if ($edit_action == false) {
	  					  		$configuration_title = 'Homepage Page Title';
	  					  		$configuration_description = 'The page title for store front';
	  					  	}
	  					  	break;
	  					  case 'keywords':
	  					  	$configuration_key = 'HOME_META_KEYWORD_' . $key;
	  					  	
	  					  	if ($edit_action == false) {
	  					  		$configuration_title = 'Homepage Meta Keywords';
	  					  		$configuration_description = 'The meta keywords for store front';
	  					  	}
	  					  	break;
  					  	case 'descriptions':
  					  		$configuration_key = 'HOME_META_DESCRIPTION_' . $key;
  					  		
  					  		if ($edit_action == false) {
  					  			$configuration_title = 'Homepage Meta Description';
  					  			$configuration_description = 'The meta description for store front';
  					  		}
  					  		break;
	  					}
	  					
	  					$Qmeta_meta_info->bindTable(":table_configuration", TABLE_CONFIGURATION);
	  					$Qmeta_meta_info->bindValue(":configuration_key", $configuration_key);
	  					$Qmeta_meta_info->bindValue(":configuration_value", $value);
	  					$Qmeta_meta_info->bindInt(':store_id', $current_store_id);
	  					
	  					if ($edit_action == false) {
	  						$Qmeta_meta_info->bindValue(":configuration_title", $configuration_title);
	  						$Qmeta_meta_info->bindValue(":configuration_description", $configuration_description);
	  					}
	  					
	  					$Qmeta_meta_info->execute();
	  					
	  					if($osC_Database->isError()) {
	  						$error = true;
	  						break;
	  					}
	  				}
	  			}
	  			
					if (isset($form_to_database[$config_key])) {
						//get configuration info
						$Qinfo = $osC_Database->query('select configuration_title, configuration_description, configuration_group_id from :table_configuration where configuration_key = :configuration_key');
						$Qinfo->bindTable(':table_configuration', TABLE_CONFIGURATION);
						$Qinfo->bindValue(':configuration_key', $form_to_database[$config_key]);
						$Qinfo->execute();
						
						$information = $Qinfo->toArray();
						
						$Qinfo->freeResult();
						
						//store template, update configuration description with correct template name
						if ($config_key == 'store_template_code') {
						  $Qtemplate = $osC_Database->query('select title from :table_templates where code = :code');
						  $Qtemplate->bindTable(':table_templates', TABLE_TEMPLATES);
						  $Qtemplate->bindValue(':code', $config_value);
						  $Qtemplate->execute();
						  
						  $information['configuration_description'] = $Qtemplate->value('title');
						  
						  $Qtemplate->freeResult();
						}
						
						//edit
						if ($edit_action) {
							$Qconfiguration = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key and store_id = :store_id');
						}else {
							$Qconfiguration = $osC_Database->query('insert into :table_configuration (store_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id) values (:store_id, :configuration_title, :configuration_key, :configuration_value, :configuration_description, :configuration_group_id)');
							
							$Qconfiguration->bindValue(':configuration_title', $information['configuration_title']);
							$Qconfiguration->bindValue(':configuration_description', $information['configuration_description']);
							$Qconfiguration->bindValue(':configuration_group_id', $information['configuration_group_id']);
						}
						
						$Qconfiguration->bindTable(':table_configuration', TABLE_CONFIGURATION);
						$Qconfiguration->bindInt(':store_id', $current_store_id);
						$Qconfiguration->bindValue(':configuration_key', $form_to_database[$config_key]);
						$Qconfiguration->bindValue(':configuration_value', $config_value);
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
			osC_Cache::clear('upcoming_products');
			
	    return true;
	  }
	  
	  $osC_Database->rollbackTransaction();
	  
	  return false;
	}
	
	/**
	 * Save store logo
	 *
	 * @access public
	 * @param int store id to represent edit or add action
 	 * @param int new store id to use for inserting
	 * @return bool
	 */
	function saveLogo($store_id, $current_store_id) {
		$store_logo = new upload('store_logo');
		
		if ($store_logo->exists() ) {
			if ($store_id > 0) {
				self::deleteLogo('originals_' . $store_id);
			}
		
			$img_type = substr($_FILES['store_logo']['name'], ( strrpos($_FILES['store_logo']['name'], '.') + 1 ));
			$original = DIR_FS_CATALOG . DIR_WS_IMAGES . 'logo_originals_' . $current_store_id . '.' . $img_type;
		
			$store_logo->set_destination(realpath(DIR_FS_CATALOG . 'images/'));
		
			if ($store_logo->parse() && $store_logo->save()) {
				copy(DIR_FS_CATALOG . 'images/' . $store_logo->filename, $original);
				@unlink(DIR_FS_CATALOG . 'images/' . $store_logo->filename);
		
				$osC_DirectoryListing = new osC_DirectoryListing('../templates');
				$osC_DirectoryListing->setIncludeDirectories(true);
				$osC_DirectoryListing->setIncludeFiles(false);
				$osC_DirectoryListing->setExcludeEntries('system');
		
				$templates = $osC_DirectoryListing->getFiles();
		
				foreach ($templates as $template) {
					$code = $template['name'];
					if( file_exists('../templates/' . $code . '/template.php') ){
						include('../templates/' . $code . '/template.php');
						$class = 'osC_Template_' . $code;
		
						self::deleteLogo($code . '_' . $current_store_id);
		
						if ( class_exists($class) ) {
							$module = new $class();
		
							$logo_height = $module->getLogoHeight();
							$logo_width = $module->getLogoWidth();
		
							$dest_image = DIR_FS_CATALOG . DIR_WS_IMAGES . 'logo_' . $code . '_' . $current_store_id . '.' . $img_type;
		
							osc_gd_resize($original, $dest_image, $logo_width, $logo_height);
						}
					}
				}
				return true;
			}
		}else {
		  return true;
		}
		
		return false;
	}
	
	/**
	 * Delete original logo
	 *
	 * @access private static
	 * @param string logo code
	 * @return void
	 */
	function deleteLogo($code) {
		$osC_DirectoryListing = new osC_DirectoryListing('../' . DIR_WS_IMAGES);
		$osC_DirectoryListing->setIncludeDirectories(false);
		$files = $osC_DirectoryListing->getFiles();
	
		$logo = 'logo_' . $code;
	
		foreach ( $files as $file ) {
			$filename = explode(".", $file['name']);
	
			if($filename[0] == $logo){
				$image_dir  = DIR_FS_CATALOG . 'images/';
				@unlink($image_dir . $file['name']);
			}
		}
	}
	
	/**
	 * Get original logo
	 *
	 * @access private static
	 * @param int store id
	 * @return mixed
	 */
	function getOriginalLogo($store_id) {
		$osC_DirectoryListing = new osC_DirectoryListing('../' . DIR_WS_IMAGES);
		$osC_DirectoryListing->setIncludeDirectories(false);
		$files = $osC_DirectoryListing->getFiles();
	
		foreach ( $files as $file ) {
			$filename = explode(".", $file['name']);
	
			if($filename[0] == 'logo_originals_' . $store_id){
				return '../' . DIR_WS_IMAGES . 'logo_originals_' . $store_id . '.' . $filename[1];
			}
		}
	
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
				'maintenance_mode' => 'MAINTENANCE_MODE',
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