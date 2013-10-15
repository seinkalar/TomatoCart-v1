<?php
/*
  $Id: osc_cfg_set_ups_packaging_pull_down_menu.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_packaging_pull_down_menu($default, $key = null) {
    global $osC_Language;
    
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    
    $packaging_types = array(
			array('id' => '02', 'text' => $osC_Language->get('shipping_ups_package')),
			array('id' => '01', 'text' => $osC_Language->get('shipping_ups_letter')),
			array('id' => '03', 'text' => $osC_Language->get('shipping_ups_tube')),
			array('id' => '04', 'text' => $osC_Language->get('shipping_ups_pak')),
			array('id' => '21', 'text' => $osC_Language->get('shipping_ups_express_box')),
			array('id' => '24', 'text' => $osC_Language->get('shipping_ups_25kg_box')),
			array('id' => '25', 'text' => $osC_Language->get('shipping_ups_10kg_box'))
		);
   
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'combobox';
    $control['mode'] = 'local';
    $control['values'] = $packaging_types;

    return $control;
  }
?>
