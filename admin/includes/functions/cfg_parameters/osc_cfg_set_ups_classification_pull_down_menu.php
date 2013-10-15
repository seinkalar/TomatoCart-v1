<?php
/*
  $Id: osc_cfg_set_ups_classification_pull_down_menu.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_classification_pull_down_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    
    $classifications = array(
			array('id' => '01', 'text' => '01'),
			array('id' => '03', 'text' => '03'),
			array('id' => '04', 'text' => '04')
		);
   
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'combobox';
    $control['mode'] = 'local';
    $control['values'] = $classifications;

    return $control;
  }
?>
