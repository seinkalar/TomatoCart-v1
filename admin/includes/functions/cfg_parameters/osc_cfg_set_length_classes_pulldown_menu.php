<?php
/*
  $Id: osc_cfg_set_length_classes_pulldown_menu.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_length_classes_pulldown_menu($default, $key = null) {
    global $osC_Database, $osC_Language;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $length_class_array = array();

    foreach (osC_Length::getClasses() as $class) {
      $length_class_array[] = array('id' => $class['id'],
                                    'text' => $class['title']);
    }

    $control = array();
    $control['name'] = $name;
    $control['type'] = 'combobox';
    $control['mode'] = 'local';
    $control['values'] = $length_class_array;

    return $control;    
  }
?>
