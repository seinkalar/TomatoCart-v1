<?php
/*
  $Id: length_classes_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>
Toc.length_classes.LengthClassesDialog = function (config) {

  config = config || {};
  
	config.id = 'length_classes-dialog-win';
	config.title = '<?php echo $osC_Language->get("action_heading_new_length_class"); ?>';
	config.layout = 'fit';
	config.width = 480;
	config.height = 360;
	config.modal = true;
	config.iconCls = 'icon-length_classes-win';
  config.items = this.buildForm();
  
	config.buttons = [
    {
	    text: TocLanguage.btnSave,
	    handler: function () {
		    this.submitForm();
	    },
	    scope: this
    }, 
    {
	    text: TocLanguage.btnClose,
	    handler: function () {
		    this.close();
	    },
	    scope: this
    }
  ];
  
	this.addEvents({ 'saveSuccess': true });
  
	Toc.length_classes.LengthClassesDialog.superclass.constructor.call(this, config);
}
Ext.extend(Toc.length_classes.LengthClassesDialog, Ext.Window, {

	show: function (id) {
    var lengthClassesId = id || null;
    
		this.frmLengthClass.form.reset();
    this.frmLengthClass.form.baseParams['length_class_id'] = lengthClassesId;
    
    if (lengthClassesId > 0) {
  		this.frmLengthClass.load({
  			url: Toc.CONF.CONN_URL,
  			params: {
  				action: 'load_length_classes',
          length_class_id: lengthClassesId
  			},
  			success: function (form, action) {
          var rules = action.result.data.rules;
          
          for (var i=0 ; i < rules.length ; i++){
            this.frmLengthClass.add({
              xtype: 'numberfield',
              fieldLabel: rules[i].length_class_title,
              name: 'rules[' + rules[i].length_class_id + ']',
              value: rules[i].length_class_rule
            });
          }
          
          if (!action.result.data.is_default) {    
            this.frmLengthClass.add({
              xtype: 'checkbox',
              name: 'is_default',
              fieldLabel: '<?php echo $osC_Language->get("field_set_as_default"); ?>'
            });
          }
          
          this.doLayout();
          
  				Toc.length_classes.LengthClassesDialog.superclass.show.call(this);
  			},
  			failure: function (form, action) {
  				Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
  			},
  			scope: this
  		});
    } else {
      Ext.Ajax.request({
        url: Toc.CONF.CONN_URL,
        params: {
          module: 'length_classes',
          action: 'get_length_classes_rules'
        },
        callback: function(options, success, response) {
          var result = Ext.decode(response.responseText);
          var rules = result.rules;
          
          for (var i = 0; i < rules.length; i++) {
            this.frmLengthClass.add({
              xtype: 'numberfield',
              name: 'rules[' + rules[i].length_class_id + ']',
              fieldLabel: rules[i].length_class_title,
              value: rules[i].length_class_rule
            });
          }
          
          this.frmLengthClass.add({
            xtype: 'checkbox',
            name: 'default',
            fieldLabel: '<?php echo $osC_Language->get("field_set_as_default"); ?>',
            anchor: ''
          });
          
          this.doLayout();
          
          Toc.length_classes.LengthClassesDialog.superclass.show.call(this);
        },
        scope: this
      });
    }
	},
	
  
	buildForm: function () {
		this.frmLengthClass = new Ext.form.FormPanel({
			url: Toc.CONF.CONN_URL,
			baseParams: {
				module: 'length_classes',
        action: 'save_length_classes'				
			},
      autoScroll: true,
      defaults: {
        anchor: '95%'
      },
			layoutConfig: { 
			  labelSeparator: '' 
			}
	  });
    
    <?php
      $i = 1; 
      foreach ( $osC_Language->getAll() as $l ) {
        $fieldLabel = 'fieldLabel: ' . (($i == 1) ? '"' . $osC_Language->get('field_title_and_code') . '"' : '"&nbsp;"');
          
        echo 'var lang' . $l['id'] . ' = { 
          id: "la' . $i . '", 
          layout: "column", 
          border: false, 
          items: [
            {
              width: 230,
              layout: "form", 
              labelSeparator: " ", 
              border: false, 
              items: [
                {
                  xtype: "textfield", 
                  name: "name[' . $l['id'] . ']",
                  labelStyle: "background: url(../images/worldflags/' . $l['country_iso'] . '.png) no-repeat right center !important",
                  width: 100,
                  allowBlank: false,' . 
                  $fieldLabel . '
                }
              ]
            },
            {
              layout: "form",
              border: false,
              items: {xtype: "textfield", name: "key[' . $l['id'] . ']",  width: 100, allowBlank: false, hideLabel: true}
            }
          ]};';
                  
        echo 'this.frmLengthClass.add(lang' . $l['id'] . ');';
        $i++;
      }     
    ?>
    
    this.frmLengthClass.add({
      xtype: 'statictextfield',
      border: false,
      fieldLabel: '<?php echo $osC_Language->get("field_rules"); ?>',
      value: ''
    });
    
    return this.frmLengthClass;
	},
  
	submitForm: function () {
		this.frmLengthClass.form.submit({
			waitMsg: TocLanguage.formSubmitWaitMsg,
			success: function (form, action) {
				this.fireEvent('saveSuccess', action.result.feedback);
				this.close();
			},
			failure: function (form, action) {
				if (action.failureType != 'client') {
					Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
				}
			},
			scope: this
		});
	}
});