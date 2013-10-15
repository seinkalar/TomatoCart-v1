<?php
/*
  $Id: osc_cfg_set_ups_mx_services_checkbox_field.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_mx_services_checkbox_field($default, $key = null) {
  	global $osC_Language;
  	
    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . '][]';
    
    $services = array(
    	array('id' => '07', 'text' => $osC_Language->get('shipping_ups_express')),
			array('id' => '08', 'text' => $osC_Language->get('shipping_ups_expedited')),
			array('id' => '54', 'text' => $osC_Language->get('shipping_ups_express_plus')),
			array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
		);
    
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'checkbox';
    $control['values'] = $services;

    return $control;
  }
?>
