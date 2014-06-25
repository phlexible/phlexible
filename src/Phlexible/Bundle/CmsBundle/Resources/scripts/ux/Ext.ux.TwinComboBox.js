Ext.ux.TwinComboBox = Ext.extend(Ext.form.ComboBox, {
    initComponent: Ext.form.TwinTriggerField.prototype.initComponent,
    getTrigger: Ext.form.TwinTriggerField.prototype.getTrigger,
//    initTrigger: Ext.form.TwinTriggerField.prototype.initTrigger,
    initTrigger: function() {
        var ts = this.trigger.select('.x-form-trigger', true);
//        this.wrap.setStyle('overflow', 'hidden');
        var triggerField = this;
        ts.each(function(t, all, index){
            t.hide = function(){
                var w = triggerField.wrap.getWidth() || triggerField.width || 100;
                this.dom.style.display = 'none';
                triggerField.el.setWidth(w-triggerField.trigger.getWidth());
            };
            t.show = function(){
                var w = triggerField.wrap.getWidth() || triggerField.width || 100;
                this.dom.style.display = '';
                triggerField.el.setWidth(w-triggerField.trigger.getWidth());
            };
            var triggerIndex = 'Trigger'+(index+1);

            if(this['hide'+triggerIndex]){
                t.dom.style.display = 'none';
            }
            t.on("click", this['on'+triggerIndex+'Click'], this, {preventDefault:true});
            t.addClassOnOver('x-form-trigger-over');
            t.addClassOnClick('x-form-trigger-click');
        }, this);
        this.triggers = ts.elements;
    },
    trigger1Class: 'x-form-clear-trigger',
    hideTrigger1: true,
//    onTrigger2Click: Ext.form.ComboBox.prototype.onTriggerClick, // Not sure if needed ???
//    hideTrigger2: Ext.form.ComboBox.prototype.hideTrigger      // Does not map hideTrigger ???

    reset: Ext.form.Field.prototype.reset.createSequence(function(){
        this.triggers[0].hide();
    }),

    onViewClick: Ext.form.ComboBox.prototype.onViewClick.createSequence(function(){
        this.triggers[0].show();
    }),

    onTrigger2Click: function(){
        this.onTriggerClick();
    },

    onClear: Ext.emptyFn,

    onTrigger1Click: function(){
        if(this.disabled){
            return;
        }
        this.clearValue();
        this.triggers[0].hide();
        this.onClear();
        this.fireEvent('clear', this);
    },

    setValue: Ext.form.ComboBox.prototype.setValue.createSequence(function(v){
        if (v && !this.readOnly) {
            this.hideTrigger1 = false;

            if(this.triggers) {
                this.triggers[0].show();
            }
        }
    })
});
Ext.reg('twincombobox', Ext.ux.TwinComboBox);
