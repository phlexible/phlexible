Phlexible.templates.ScriptsPanel = Ext.extend(Ext.form.FormPanel, {
    title: 'Scripts',
    cls: 'p-templates-scripts-panel',
    autoScroll: true,
    bodyStyle: 'padding: 5px',
    defaults: {
        hideLabel: true
    },

    initComponent: function() {

        this.items = [{
            xtype: 'checkbox',
            name: 'prototype',
            boxLabel: 'Prototype v1.6.0.2'
        },{
            xtype: 'panel',
            border: false,
            html: 'Provides support for the Prototype JS library'
        },{
            xtype: 'checkbox',
            name: 'scriptaculous',
            boxLabel: 'Scriptaculous v1.8.1'
        },{
            xtype: 'panel',
            border: false,
            html: 'Provides support for the Scriptaculous effect JS library<br />Includes Prototype v1.6.0.2<br />'
        },{
            xtype: 'checkbox',
            name: 'lightbox',
            boxLabel: 'Lightbox v2.04'
        },{
            xtype: 'panel',
            border: false,
            html: 'Provides support for the Lightbox image overlay JS library<br />Includes Prototype v1.6.0.2 and Scriptaculous v1.8.1'
        },{
            xtype: 'checkbox',
            name: 'dojo',
            boxLabel: 'Dojo v1.1.1'
        },{
            xtype: 'panel',
            border: false,
            html: 'Provides support for the Dojo Toolkit'
        }];

        this.tbar = [{
            text: 'save',
            handler: function() {
                this.form.submit({
                    url: Phlexible.Router.generate('templates_scripts_save', {id: this.templateID}),
                    success: this.onSuccess,
                    failure: this.onFailure,
                    scope: this
                });
            },
            scope: this
        }];

        Phlexible.templates.ScriptsPanel.superclass.initComponent.call(this);
    },

    loadData: function(templateID, data) {
        this.templateID = templateID;
        this.form.setValues(data);
    },

    onSuccess: function(form, action) {
        Ext.MessageBox.alert('Success', action.result.msg);
    },

    onFailure: function(form, action) {
        Ext.MessageBox.alert('Failure', action.result.msg);
    }

});
