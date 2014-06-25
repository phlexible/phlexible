Phlexible.users.OptionsWindowThemeTemplate = new Ext.XTemplate(
    '<tpl for=".">',
        '<div class="thumb-wrap" id="{id}">',
        '<div class="thumb"><img src="' + Phlexible.component('/phlexibleuser/images/{preview}') + '" title="{name}"></div>',
        '<span class="x-editable">{name}</span></div>',
    '</tpl>',
    '<div class="x-clear"></div>'
);

Phlexible.users.OptionsWindow = Ext.extend(Ext.Window,{
    title: Phlexible.users.Strings.options,
    strings: Phlexible.users.Strings,
    plain: true,
    cls: 'p-users-options-window',
    iconCls: 'p-user-user-icon',
    width: 400,
    minWidth: 400,
    height: 400,
    minHeight: 400,
    layout: 'card',
    border: false,
    modal: true,
    activeItem: 0,
//    deferredRender: true,

    initComponent: function() {
        var lis = '';
        var items = [];
        var actions = {
            'view_overview': function(owner) {
                owner.layout.setActiveItem(0);
            }
        };

        var optionCards = Phlexible.PluginRegistry.get('userOptionCards');

        for(var i=0; i<optionCards.length; i++) {
            var item = optionCards[i];

            lis += '<li>' +
                       '<img src="' + Ext.BLANK_IMAGE_URL + '" class="' + item.iconCls + '" />' +
                       '<a id="view_' + item.xtype + '" href="#">' + item.title + '</a><br />' +
                       '<span>' + item.description + '</span>' +
                   '</li>';
            items.push({
                xtype: item.xtype,
                listeners: {
                    save: this.viewOverview,
                    cancel: this.viewOverview,
					scope: this
                }
            });
            actions['view_' + item.xtype] = function(owner, cardIndex) {
                owner.layout.setActiveItem(cardIndex);
            }.createDelegate(this, [i+1], true);
        }

        items.unshift({
            bodyStyle: 'padding: 15px',
            border: false,
            html: '<ul class="user">' + lis + '</ul>',
            buttons: [{
                text: this.strings.close,
                handler: this.close,
                scope: this
            }]
        });

        this.items = items;
        this.actions = actions;

        Phlexible.users.UserWindow.superclass.initComponent.call(this);
    },

    doAction : function(e, t){
        e.stopEvent();
        this.actions[t.id](this);
    },

    viewOverview: function() {
        this.actions['view_overview'](this);
    },

    afterRender: function() {
        this.body.on({
            'mousedown': {
                fn: this.doAction,
                scope: this,
                delegate: 'a'
            },
            'click': {
                fn: Ext.emptyFn,
                scope: null,
                delegate: 'a',
                preventDefault: true
            }
        });

        Phlexible.users.OptionsWindow.superclass.afterRender.call(this);
    }
});
