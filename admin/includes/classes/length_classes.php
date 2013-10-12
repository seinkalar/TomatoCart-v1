<?php
/*
  $Id: length_classes.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_LengthClasses_Admin {
    function getData($id) {
      global $osC_Database, $osC_Language;

      $Qclass = $osC_Database->query('select * from :table_length_classes where length_class_id = :length_class_id and language_id = :language_id');
      $Qclass->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
      $Qclass->bindInt(':length_class_id', $id);
      $Qclass->bindInt(':language_id', $osC_Language->getID());
      $Qclass->execute();

      $data = $Qclass->toArray();

      $Qclass->freeResult();

      return $data;
    }

    function save($id = null, $data, $default = false) {
      global $osC_Database, $osC_Language;

      $error = false;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $length_class_id = $id;
      } else {
        $Qlc = $osC_Database->query('select max(length_class_id) as length_class_id from :table_length_classes');
        $Qlc->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
        $Qlc->execute();

        $length_class_id = $Qlc->valueInt('length_class_id') + 1;
      }

      foreach ( $osC_Language->getAll() as $l ) {
        if ( is_numeric($id) ) {
          $Qlc = $osC_Database->query('update :table_length_classes set length_class_key = :length_class_key, length_class_title = :length_class_title where length_class_id = :length_class_id and language_id = :language_id');
        } else {
          $Qlc = $osC_Database->query('insert into :table_length_classes (length_class_id, language_id, length_class_key, length_class_title) values (:length_class_id, :language_id, :length_class_key, :length_class_title)');
        }

        $Qlc->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
        $Qlc->bindInt(':length_class_id', $length_class_id);
        $Qlc->bindInt(':language_id', $l['id']);
        $Qlc->bindValue(':length_class_key', $data['key'][$l['id']]);
        $Qlc->bindValue(':length_class_title', $data['name'][$l['id']]);
        $Qlc->setLogging($_SESSION['module'], $length_class_id);
        $Qlc->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        if ( is_numeric($id) ) {
          $Qrules = $osC_Database->query('select length_class_to_id from :table_length_classes_rules where length_class_from_id = :length_class_from_id and length_class_to_id != :length_class_to_id');
          $Qrules->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
          $Qrules->bindInt(':length_class_from_id', $length_class_id);
          $Qrules->bindInt(':length_class_to_id', $length_class_id);
          $Qrules->execute();

          while ( $Qrules->next() ) {
            $Qrule = $osC_Database->query('update :table_length_classes_rules set length_class_rule = :length_class_rule where length_class_from_id = :length_class_from_id and length_class_to_id = :length_class_to_id');
            $Qrule->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
            $Qrule->bindValue(':length_class_rule', $data['rules'][$Qrules->valueInt('length_class_to_id')]);
            $Qrule->bindInt(':length_class_from_id', $length_class_id);
            $Qrule->bindInt(':length_class_to_id', $Qrules->valueInt('length_class_to_id'));
            $Qrule->setLogging($_SESSION['module'], $length_class_id);
            $Qrule->execute();

            if ( $osC_Database->isError() ) {
              $error = true;
              break;
            }
          }
        } else {
          $Qclasses = $osC_Database->query('select length_class_id from :table_length_classes where length_class_id != :length_class_id and language_id = :language_id');
          $Qclasses->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
          $Qclasses->bindInt(':length_class_id', $length_class_id);
          $Qclasses->bindInt(':language_id', $osC_Language->getID());
          $Qclasses->execute();

          while ( $Qclasses->next() ) {
            $Qdefault = $osC_Database->query('insert into :table_length_classes_rules (length_class_from_id, length_class_to_id, length_class_rule) values (:length_class_from_id, :length_class_to_id, :length_class_rule)');
            $Qdefault->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
            $Qdefault->bindInt(':length_class_from_id', $Qclasses->valueInt('length_class_id'));
            $Qdefault->bindInt(':length_class_to_id', $length_class_id);
            $Qdefault->bindValue(':length_class_rule', '1');
            $Qdefault->setLogging($_SESSION['module'], $length_class_id);
            $Qdefault->execute();

            if ( $osC_Database->isError() ) {
              $error = true;
              break;
            }

            if ( $error === false ) {
              $Qnew = $osC_Database->query('insert into :table_length_classes_rules (length_class_from_id, length_class_to_id, length_class_rule) values (:length_class_from_id, :length_class_to_id, :length_class_rule)');
              $Qnew->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
              $Qnew->bindInt(':length_class_from_id', $length_class_id);
              $Qnew->bindInt(':length_class_to_id', $Qclasses->valueInt('length_class_id'));
              $Qnew->bindValue(':length_class_rule', $data['rules'][$Qclasses->valueInt('length_class_id')]);
              $Qnew->setLogging($_SESSION['module'], $length_class_id);
              $Qnew->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        if ( $default === true ) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindInt(':configuration_value', $length_class_id);
          $Qupdate->bindValue(':configuration_key', 'SHIPPING_LENGTH_UNIT');
          $Qupdate->setLogging($_SESSION['module'], $length_class_id);
          $Qupdate->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        if ( $default === true ) {
          osC_Cache::clear('configuration');
        }

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function delete($id) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qrules = $osC_Database->query('delete from :table_length_classes_rules where length_class_from_id = :length_class_from_id or length_class_to_id = :length_class_to_id');
      $Qrules->bindTable(':table_length_classes_rules', TABLE_LENGTH_CLASSES_RULES);
      $Qrules->bindInt(':length_class_from_id', $id);
      $Qrules->bindInt(':length_class_to_id', $id);
      $Qrules->setLogging($_SESSION['module'], $id);
      $Qrules->execute();

      if ( $osC_Database->isError() ) {
        $error = true;
      }

      if ( $error === false ) {
        $Qclasses = $osC_Database->query('delete from :table_length_classes where length_class_id = :length_class_id');
        $Qclasses->bindTable(':table_length_classes', TABLE_LENGTH_CLASSES);
        $Qclasses->bindInt(':length_class_id', $id);
        $Qclasses->setLogging($_SESSION['module'], $id);
        $Qclasses->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('length-classes');
        osC_Cache::clear('length-rules');

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }
  }
?>
