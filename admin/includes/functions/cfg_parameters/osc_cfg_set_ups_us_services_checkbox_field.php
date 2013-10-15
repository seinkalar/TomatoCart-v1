<?php
/*
  $Id: osc_cfg_set_ups_us_services_checkbox_field.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_us_services_checkbox_field($default, $key = null) {
  	global $osC_Language;
  	
    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . '][]';
    
    $services = array(
    	array('id' => '01', 'text' => $osC_Language->get('shipping_ups_next_day_air')),
			array('id' => '02', 'text' => $osC_Language->get('shipping_ups_2nd_day_air')),
			array('id' => '03', 'text' => $osC_Language->get('shipping_ups_ground')),
			array('id' => '07', 'text' => $osC_Language->get('shipping_ups_worldwide_express')),
			array('id' => '08', 'text' => $osC_Language->get('shipping_ups_worldwide_expedited')),
			array('id' => '11', 'text' => $osC_Language->get('shipping_ups_standard')),
			array('id' => '12', 'text' => $osC_Language->get('shipping_ups_3_day_select')),
			array('id' => '13', 'text' => $osC_Language->get('shipping_ups_next_day_air_saver')),
			array('id' => '14', 'text' => $osC_Language->get('shipping_ups_next_day_air_early_am')),
			array('id' => '54', 'text' => $osC_Language->get('shipping_ups_worldwide_express_plus')),
			array('id' => '59', 'text' => $osC_Language->get('shipping_ups_2nd_day_air_am')),
			array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
		);
    
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'checkbox';
    $control['values'] = $services;

    return $control;
  }
?>
