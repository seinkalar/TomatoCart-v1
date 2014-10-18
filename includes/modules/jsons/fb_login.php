<?php
/*
  $Id: fb_login.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2010 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

include('includes/classes/account.php');

class toC_Json_Fb_login {
    function login() {
      	global $toC_Json, $osC_Database, $osC_Language, $osC_Session, $osC_Customer, $osC_ShoppingCart, $toC_Wishlist;
      
      	if (osC_Account::checkEntry($_POST['email'])) {
      		if (SERVICE_SESSION_REGENERATE_ID == '1') {
      			$osC_Session->recreate();
      		}
      		
      		$osC_Customer->setCustomerData(osC_Account::getID($_POST['email']));
      		
      		$Qupdate = $osC_Database->query('update :table_customers set date_last_logon = :date_last_logon, number_of_logons = number_of_logons+1 where customers_id = :customers_id');
      		$Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
      		$Qupdate->bindRaw(':date_last_logon', 'now()');
      		$Qupdate->bindInt(':customers_id', $osC_Customer->getID());
      		$Qupdate->execute();
      		
      		if ( defined('SYNCHRONIZE_CART_WITH_DATABASE') && (SYNCHRONIZE_CART_WITH_DATABASE == '1') ) {
      			$osC_ShoppingCart->synchronizeWithDatabase();
      		}
      		
      		$toC_Wishlist->synchronizeWithDatabase();
      	}else {
      		$data = array();
      		
      		$data['gender'] = $_POST['gender'];
      		$data['firstname'] = $_POST['first_name'];
      		$data['lastname'] = $_POST['last_name'];
      		$data['email_address'] = $_POST['email'];
      		$data['password'] = osc_encrypt_string(self::RandomString());
      		
      		osC_Account::createEntry($data);
      	}
      
      
      	echo $toC_Json->encode(array('success' => true));
    }
    
    function RandomString()
    {
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	
    	$randstring = '';
    	
    	for ($i = 0; $i < 10; $i++) {
    		$randstring = $characters[rand(0, strlen($characters))];
    	}
    	return $randstring;
    }
}
  