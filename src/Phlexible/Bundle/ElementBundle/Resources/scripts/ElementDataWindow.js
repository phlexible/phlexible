Phlexible.elements.ElementDataWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.element,
    width: 800,
    height: 600,
    layout: 'fit',
    constrainHeader: true,
    modal: true,

    initComponent: function () {

        this.addEvents(
            'save'
        );

        this.element = new Phlexible.elements.Element({
            siteroot_id: this.parentElement.siteroot_id,
            language: this.parentElement.language
        });

//        this.element.on('load', function() {
////            Phlexible.elements.ElementDataWindow.superclass.show.call(this);
//            if(this.parentElement.locked && this.parentElement.locked_by_me) {
//                this.dataPanel.onGetLock();
//            } else if(this.parentElement.locked) {
//                this.dataPanel.onIsLocked();
//            } else {
//                this.dataPanel.onRemoveLock();
//            }
//        }, this);

        this.dataPanel = new Phlexible.elements.ElementDataPanel({
            title: '',
            element: this.element,
            listeners: {
                loadVersion: {
                    fn: function (version) {
                        this.element.reload({
                            version: version
                        });
                    },
                    scope: this
                }
            }
        });

        this.dataPanel.on('render', function () {
            this.element.loadEid(this.element.eid, null, null, true);
        }, this);

        this.dataPanel.on('save', function () {
            this.fireEvent('save', this);
        }, this);

        this.items = this.dataPanel;

        Phlexible.elements.ElementDataWindow.superclass.initComponent.call(this);
    },

    show: function (title, eid) {
        this.setTitle(title);
        this.element.eid = eid;

        Phlexible.elements.ElementDataWindow.superclass.show.call(this);
    }
});
