<?php
/*
  $Id: ups.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

 class osC_Shipping_ups extends osC_Shipping_Admin {
    var $icon;

    var $_title,
        $_code = 'ups',
        $_author_name = 'tomatocart',
        $_author_www = 'http://www.tomatocart.com',
        $_status = false,
        $_sort_order;

		// class constructor
    function osC_Shipping_ups() {
      global $osC_Language;

      $this->icon = DIR_WS_IMAGES . 'icons/shipping_ups.gif';

      $this->_title = $osC_Language->get('shipping_ups_title');
      $this->_description = $osC_Language->get('shipping_ups_description');
      $this->_status = (defined('MODULE_SHIPPING_UPS_STATUS') && (MODULE_SHIPPING_UPS_STATUS == 'Yes') ? true : false);
      $this->_sort_order = (defined('MODULE_SHIPPING_UPS_SORT_ORDER') ? MODULE_SHIPPING_UPS_SORT_ORDER : null);
    }

		//check module installation
    function isInstalled() {
      return (bool)defined('MODULE_SHIPPING_UPS_STATUS');
    }

    //install the module
    function install() {
      global $osC_Database, $osC_Language;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_status') . "', 'MODULE_SHIPPING_UPS_STATUS', 'Yes', '" . $osC_Language->get('shipping_ups_status_description') . "', '6', '0', 'osc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_access_key') . "', 'MODULE_SHIPPING_UPS_ACCESS_KEY', '', '" . $osC_Language->get('shipping_ups_access_key_description') . "', '6', '1', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_username') . "', 'MODULE_SHIPPING_UPS_USRERNAME', '', '" . $osC_Language->get('shipping_ups_username_description') . "', '6', '2', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_password') . "', 'MODULE_SHIPPING_UPS_PASSWORD', '', '" . $osC_Language->get('shipping_ups_password_description') . "', '6', '3', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_pickup') . "', 'MODULE_SHIPPING_UPS_PICKUP', '01', '" . $osC_Language->get('shipping_ups_pickup_description') . "', '6', '4', 'osc_cfg_use_get_pickup_method_title', 'osc_cfg_set_ups_pickup_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_packaging_type') . "', 'MODULE_SHIPPING_UPS_PACKAGING_TYPE', '02', '" . $osC_Language->get('shipping_ups_packaging_type_description') . "', '6', '5', 'osc_cfg_use_get_packaging_title', 'osc_cfg_set_ups_packaging_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_classification') . "', 'MODULE_SHIPPING_UPS_CLASSIFICATION', '01', '" . $osC_Language->get('shipping_ups_classification_description') . "', '6', '6', 'osc_cfg_set_ups_classification_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_origin') . "', 'MODULE_SHIPPING_UPS_ORIGIN', 'US', '" . $osC_Language->get('shipping_ups_origin_description') . "', '6', '7', 'osc_cfg_use_get_origin_title', 'osc_cfg_set_ups_origin_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_city') . "', 'MODULE_SHIPPING_UPS_CITY', '', '" . $osC_Language->get('shipping_ups_city_description') . "', '6', '8', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_state') . "', 'MODULE_SHIPPING_UPS_STATE', '', '" . $osC_Language->get('shipping_ups_state_description') . "', '6', '9', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_country') . "', 'MODULE_SHIPPING_UPS_COUNTRY', '', '" . $osC_Language->get('shipping_ups_country_description') . "', '6', '10', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $osC_Language->get('shipping_ups_postcode') . "', 'MODULE_SHIPPING_UPS_POSTCODE', '', '" . $osC_Language->get('shipping_ups_postcode_description') . "', '6', '11', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_test') . "', 'MODULE_SHIPPING_UPS_TEST_MODE', 'Yes', '" . $osC_Language->get('shipping_ups_test_description') . "', '6', '12', 'osc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_quote_type') . "', 'MODULE_SHIPPING_UPS_QUOTE_TYPE', 'residential', '" . $osC_Language->get('shipping_ups_quote_type_description') . "', '6', '13', 'osc_cfg_use_get_quote_type_title', 'osc_cfg_set_ups_quote_type_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_enable_insurance') . "', 'MODULE_SHIPPING_UPS_ENABLE_INSURANCE', 'No', '" . $osC_Language->get('shipping_ups_enable_insurance_description') . "', '6', '14', 'osc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())");
      
      //weight
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_display_weight') . "', 'MODULE_SHIPPING_UPS_DISPLAY_WEIGHT', 'No', '" . $osC_Language->get('shipping_ups_display_weight_description') . "', '6', '15', 'osc_cfg_set_boolean_value(array(\'Yes\', \'No\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_weight_class') . "', 'MODULE_SHIPPING_UPS_WEIGHT_CLASS_ID', '2', '" . $osC_Language->get('shipping_ups_weight_class_description') . "', '6', '16', 'toc_cfg_use_get_weight_class_title', 'osc_cfg_set_weight_classes_pulldown_menu', now())");

      //length
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_length_class') . "', 'MODULE_SHIPPING_UPS_LENGTH_CLASS_ID', '1', '" . $osC_Language->get('shipping_ups_length_class_description') . "', '6', '17', 'osc_cfg_use_get_length_class_title', 'osc_cfg_set_length_classes_pulldown_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_dimensions_width') . "', 'MODULE_SHIPPING_UPS_DIMENSIONS_WIDTH', '', '" . $osC_Language->get('shipping_ups_dimensions_width_description') . "', '6', '18', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_dimensions_height') . "', 'MODULE_SHIPPING_UPS_DIMENSIONS_HEIGHT', '', '" . $osC_Language->get('shipping_ups_dimensions_height_description') . "', '6', '19', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('<span style=\'color:red;\'><b>* </b></span>" . $osC_Language->get('shipping_ups_dimensions_length') . "', 'MODULE_SHIPPING_UPS_DIMENSIONS_LENGTH', '', '" . $osC_Language->get('shipping_ups_dimensions_length_description') . "', '6', '20', now())");
      
      //tax class
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_tax_classes') . "', 'MODULE_SHIPPING_UPS_TAX_CLASS', '0', '', '6', '21', 'osc_cfg_use_get_tax_class_title', 'osc_cfg_set_tax_classes_pull_down_menu', now())");
      
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_zone') . "', 'MODULE_SHIPPING_UPS_ZONE', '0', '" . $osC_Language->get('shipping_ups_zone_description') . "', '6', '22', 'osc_cfg_use_get_zone_class_title', 'osc_cfg_set_zone_classes_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $osC_Language->get('shipping_ups_sort_order') . "', 'MODULE_SHIPPING_UPS_SORT_ORDER', '0', '" . $osC_Language->get('shipping_ups_sort_order_description') . "', '6', '23', now())");
      //services
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_us_service') . "', 'MODULE_SHIPPING_UPS_US_SERVICES', '', '', '6', '24', 'osc_cfg_set_ups_us_services_checkbox_field', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_ca_service') . "', 'MODULE_SHIPPING_UPS_CA_SERVICES', '', '', '6', '25', 'osc_cfg_set_ups_ca_services_checkbox_field', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_eu_service') . "', 'MODULE_SHIPPING_UPS_EU_SERVICES', '', '', '6', '26', 'osc_cfg_set_ups_eu_services_checkbox_field', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_mx_service') . "', 'MODULE_SHIPPING_UPS_MX_SERVICES', '', '', '6', '27', 'osc_cfg_set_ups_mx_services_checkbox_field', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_pr_service') . "', 'MODULE_SHIPPING_UPS_PR_SERVICES', '', '', '6', '28', 'osc_cfg_set_ups_pr_services_checkbox_field', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $osC_Language->get('shipping_ups_other_service') . "', 'MODULE_SHIPPING_UPS_OTHER_SERVICES', '', '', '6', '29', 'osc_cfg_set_ups_other_services_checkbox_field', now())");
    }

    //get the configurations keys for this module
    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array(
					'MODULE_SHIPPING_UPS_STATUS',
					'MODULE_SHIPPING_UPS_ACCESS_KEY',
					'MODULE_SHIPPING_UPS_USRERNAME', 
					'MODULE_SHIPPING_UPS_PASSWORD', 
        	'MODULE_SHIPPING_UPS_PICKUP', 
					'MODULE_SHIPPING_UPS_PACKAGING_TYPE', 
					'MODULE_SHIPPING_UPS_CLASSIFICATION', 
					'MODULE_SHIPPING_UPS_ORIGIN', 
					'MODULE_SHIPPING_UPS_CITY', 
					'MODULE_SHIPPING_UPS_STATE', 
					'MODULE_SHIPPING_UPS_COUNTRY', 
					'MODULE_SHIPPING_UPS_POSTCODE', 
					'MODULE_SHIPPING_UPS_TEST_MODE', 
					'MODULE_SHIPPING_UPS_QUOTE_TYPE',
					'MODULE_SHIPPING_UPS_ENABLE_INSURANCE',
					'MODULE_SHIPPING_UPS_DISPLAY_WEIGHT', 
					'MODULE_SHIPPING_UPS_WEIGHT_CLASS_ID',
					'MODULE_SHIPPING_UPS_LENGTH_CLASS_ID',
        	'MODULE_SHIPPING_UPS_DIMENSIONS_WIDTH',
					'MODULE_SHIPPING_UPS_DIMENSIONS_HEIGHT',
					'MODULE_SHIPPING_UPS_DIMENSIONS_LENGTH',
					'MODULE_SHIPPING_UPS_TAX_CLASS',
					'MODULE_SHIPPING_UPS_ZONE',
					'MODULE_SHIPPING_UPS_SORT_ORDER',
					'MODULE_SHIPPING_UPS_US_SERVICES',
					'MODULE_SHIPPING_UPS_CA_SERVICES',
					'MODULE_SHIPPING_UPS_EU_SERVICES',
					'MODULE_SHIPPING_UPS_MX_SERVICES',
					'MODULE_SHIPPING_UPS_PR_SERVICES', 
					'MODULE_SHIPPING_UPS_OTHER_SERVICES'
				);
      }

      return $this->_keys;
    }
  }
?>