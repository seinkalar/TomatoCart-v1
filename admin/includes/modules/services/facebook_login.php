<?php
/*
  $Id: facebook_login.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Services_facebook_login_Admin {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes;

    function osC_Services_facebook_login_Admin() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/services/facebook_login.php');

      $this->title = $osC_Language->get('services_facebook_login_title');
      $this->description = $osC_Language->get('services_facebook_login_description');
    }

    function install() {
      global $osC_Database, $osC_Language;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('" . $osC_Language->get('services_facebook_login_satus') . "', 'SERVICE_FACEBOOK_LOGIN_STATUS', '1', '"  . $osC_Language->get('services_facebook_login_status_secription') . "', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $osC_Language->get('services_facebook_login_appid') . "', 'SERVICE_FACEBOOK_LOGIN_APPID', '', '" . $osC_Language->get('services_facebook_login_appid_description') . "', '6', '0', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICE_FACEBOOK_LOGIN_STATUS', 'SERVICE_FACEBOOK_LOGIN_APPID');
    }
  }
?>
