<?php
/*
  $Id: length.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Length {
    var $length_classes = array(),
        $precision;

// class constructor
    function osC_Length($precision = '1') {
      $this->precision = $precision;

      $this->prepareRules();
    }

    function getTitle($id) {
      global $osC_Database, $osC_Language;

      $Qlength = $osC_Database->query('select length_class_title from :table_length_classes where length_class_id = :length_class_id and language_id = :language_id');
      $Qlength->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qlength->bindInt(':length_class_id', $id);
      $Qlength->bindInt(':language_id', $osC_Language->getID());
      $Qlength->execute();

      return $Qlength->value('length_class_title');
    }

    function prepareRules() {
      global $osC_Database, $osC_Language;

      $Qrules = $osC_Database->query('select r.length_class_from_id, r.length_class_to_id, r.length_class_rule from :table_length_classes_rules r, :table_length_classes c where c.length_class_id = r.length_class_from_id');
      $Qrules->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
      $Qrules->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qrules->setCache('length-rules');
      $Qrules->execute();

      while ($Qrules->next()) {
        $this->length_classes[$Qrules->valueInt('length_class_from_id')][$Qrules->valueInt('length_class_to_id')] = $Qrules->value('length_class_rule');
      }

      $Qclasses = $osC_Database->query('select length_class_id, length_class_key, length_class_title from :table_length_classes where language_id = :language_id');
      $Qclasses->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qclasses->bindInt(':language_id', $osC_Language->getID());
      $Qclasses->setCache('length-classes');
      $Qclasses->execute();

      while ($Qclasses->next()) {
        $this->length_classes[$Qclasses->valueInt('length_class_id')]['key'] = $Qclasses->value('length_class_key');
        $this->length_classes[$Qclasses->valueInt('length_class_id')]['title'] = $Qclasses->value('length_class_title');
      }

      $Qrules->freeResult();
      $Qclasses->freeResult();
    }

    function convert($value, $unit_from, $unit_to) {
      global $osC_Language;

      if ($unit_from == $unit_to) {
        return number_format($value, (int)$this->precision, $osC_Language->getNumericDecimalSeparator(), $osC_Language->getNumericThousandsSeparator());
      } else {
        return number_format($value * $this->length_classes[(int)$unit_from][(int)$unit_to], (int)$this->precision, $osC_Language->getNumericDecimalSeparator(), $osC_Language->getNumericThousandsSeparator());
      }
    }

    function display($value, $class) {
      global $osC_Language;

      return number_format($value, (int)$this->precision, $osC_Language->getNumericDecimalSeparator(), $osC_Language->getNumericThousandsSeparator()) . $this->length_classes[$class]['key'];
    }

    function getClasses() {
      global $osC_Database, $osC_Language;

      $length_class_array = array();

      $Qclasses = $osC_Database->query('select length_class_id, length_class_title from :table_length_classes where language_id = :language_id order by length_class_title');
      $Qclasses->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qclasses->bindInt(':language_id', $osC_Language->getID());
      $Qclasses->execute();

      while ($Qclasses->next()) {
        $length_class_array[] = array('id' => $Qclasses->valueInt('length_class_id'),
                                      'title' => $Qclasses->value('length_class_title'));
      }

      return $length_class_array;
    }
  }
?>
