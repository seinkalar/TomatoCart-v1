(function(window) {
  //toggle effect
  Fx.Tween.Toggle = new Class({
    Extends: Fx.Tween,
    options: {
      onToggle: '',
      onToggleIn: '',
      onToggleOut: '',
      property: 'opacity',
      duration: 'short',
      from: 0,
      to: 1
    },
    
    toggle: function(event){
      if(event) event.stop();
      (this.toggled) ? this.toggleOut() : this.toggleIn();
      this.fireEvent('onToggle');
      return this;
    },
    
    toggleIn: function(){
      this.toggled = true;
      this.start(this.options.to);
      this.fireEvent('onToggleIn');
      return this;
    },
    
    toggleOut: function(){
      this.toggled = false;
      this.start(this.options.from);
      this.fireEvent('onToggleOut');
      return this;
    },
    
    setIn: function(){
      this.toggled = true
      this.set(this.options.to);
      return this;
    },
    
    setOut: function(){
      this.toggled = false;
      this.set(this.options.from);
      return this;
    }
  });
  
  window.addEvent("domready", function() {
    //box filters class
    var filterQkForm = new Class({
      Extends: QuickForm,
      initialize: function(e,b){
        this.filtersForms = $$('form.products-filter');
        this.filtersChecked = [];
        
        this.parent(e,b);
        this.attachReset();
      },
      
      attachReset: function() {
        if ($('reset-filters') != null) {
          $('reset-filters').addEvent('click', function(e) {
            e.stop();
            
            this.getCheckboxs.each(function(checkbox){
              checkbox.setProperty('checked',false);
            });
            
            if ($$('.qkFormCheckbox') != null) {
              $$('.qkFormCheckbox').each(function(checkbox) {
                checkbox.removeClass('qkFormChecked');
              });
            }
            
            //clear filters in the list form
            if (this.filtersForms.length > 0) {
              this.filtersForms.each(function(filterForm) {
                var filterFields = filterForm.getElements('input.filterOption');
                
                if (filterFields.length > 0) {
                  filterFields.each(function(filterField) {
                    filterField.destroy();
                  });
                }
              }.bind(this));
              
              this.filtersForms[0].submit();
            }
            
            return false;
          }.bind(this));
        }
      },
      
      appendFilters: function(){
        this.filtersForms.each(function(filterForm) {
          var filterFields = filterForm.getElements('input.filterOption');
          
          if (filterFields.length > 0) {
            filterFields.each(function(filterField) {
              filterField.destroy();
            });
          }
          
          if (this.filtersChecked.length > 0) {
            this.filtersChecked.each(function(filter) {
              var inputField = new Element('input', {'type': 'hidden', 'name': 'f_' + filter, 'value': filter, 'class': 'filterOption'});
              
              filterForm.adopt(inputField);
            });
          }
        }.bind(this));
      },
      
      parseCheckboxs:function(){
        var productList = $$('.products-list')[0],
            loadingMask = new Element('div', {
              'class': 'filtersLoadingMask'
            });
        
        this.getCheckboxs.each(function(input){
          var nextLabel = input.getNext('label'),
              mySpan = new Element('span',{'class':'qkFormCheckboxWrapper'}),
              myAnchor = new Element('a',{'class':'qkFormCheckbox'}),
              inputValue = input.getProperty('value');
            
          if(input.hasClass('qkFormHidden')) {
            return;
          } else {
            input.addClass('qkFormHidden');
          }
          
          input.setStyle('display','none');
      
          // get the next label element , we need to position & add a click event to it
          if(nextLabel != null) nextLabel.setStyles({'cursor':'pointer','padding':'3px 0 0 2px'});
      
          // setup environment
          myAnchor.inject(mySpan);
          
          // add qkFormChecked class when input is already checked , for loading stuff ... 
          if(input.getProperty('checked')){
            myAnchor.addClass('qkFormChecked');
            
            if ( ! this.filtersChecked.contains(inputValue)) {
              this.filtersChecked.push(inputValue);
            }
          }
          
          //append filters into the product list form
          this.appendFilters();
          
          // click event : add checked class & remove it
          myAnchor.addEvent('click',function(){
            if(input.getProperty('disabled')){return false;}
            myAnchor.addClass('qkFormChecked');
            
            if(input.getProperty('checked') && myAnchor.hasClass('qkFormChecked')){
              myAnchor.removeClass('qkFormChecked');
              input.setProperty('checked',false);
              
              if (this.filtersChecked.contains(inputValue)) {
                this.filtersChecked.splice(this.filtersChecked.indexOf(inputValue), 1);
              }
            }
            else{
              input.setProperty('checked','checked');
              myAnchor.addClass('qkFormChecked');
              
              if ( ! this.filtersChecked.contains(inputValue)) {
                this.filtersChecked.push(inputValue);
              }
            }
            
            //add Loading Mask
            if (typeof productList != undefined) {
              var productsPosition = productList.getCoordinates();
              
              $(document.body).adopt(loadingMask);
              
              loadingMask.setStyles({'left': productsPosition.left, 'top': productsPosition.top, 'width': productsPosition.width, 'height': productsPosition.height});
            }
            
            if (this.filtersForms.length > 0) {
              this.appendFilters();
              
              this.filtersForms[0].submit();
            }
            
            return false;
          }.bind(this));

          // next label element do the same thing as anchor, so no need the repeat the same event , just clone it
          nextLabel.cloneEvents(myAnchor,'click');
          
          // insert the element now
          mySpan.inject(input,'before');
          input.inject(mySpan);
        }.bind(this));// for end's here 
      }
    });
    
    //create filter box
    new filterQkForm("frm-filters");
    
    if ($$('.filterBox') != null) {
      $$('.filterBox').each(function(filterBox) {
        var trigger = filterBox.getElement('.toggleTrigger');
        var filters = filterBox.getElement('.filters');
        
        var elHeight = filters.clientHeight;
        var myToggle = new Fx.Tween.Toggle(filters,{
          property: 'height',
          from: 0,
          to: elHeight,
          link: 'cancel',
          onToggleIn: function(){
            trigger.removeClass('triggerClosed');
            trigger.addClass('triggerOpened');
          },
          onToggleOut: function(){
            trigger.removeClass('triggerOpened');
            trigger.addClass('triggerClosed');
          }
        }).setIn();

        trigger.addEvent('click', function(e) {
          e.stop();
          
          myToggle.toggle();

          return false;
        });
      });
    }
  });
})(window, undefined);