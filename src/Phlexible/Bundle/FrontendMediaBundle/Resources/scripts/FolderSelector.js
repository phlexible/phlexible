Phlexible.frontendmedia.FolderSelector = Ext.extend(Ext.ux.TreeSelector, {
    maxHeight: 300,
    listenForLoad: false,

    initComponent: function () {
        this.menuConfig = Ext.apply(this.menuConfig || {}, {cls: 'x-tree-menu m-folder-selector'});

        this.tree = new Ext.tree.TreePanel({
            animate: false,
            border: false,
            width: this.treeWidth || 180,
            autoScroll: true,
            useArrows: true,
            selModel: new Ext.tree.ActivationModel(),
            rootVisible: false,
            loader: new Phlexible.elements.EidLoader({
                url: Phlexible.Router.generate('frontendmedia_folder')
            })
        });

        var root = new Ext.tree.AsyncTreeNode({
            text: 'Root',
            id: 'root',
            leaf: false,
            iconCls: 'icon-folder',
            expanded: true
        });

        this.tree.setRootNode(root);

        if (this.value) {
            this.tree.loader.on('load', function (v) {
                this.setValue(v);
            }.createDelegate(this, [this.value], false), this);
        }

//        this.tree.loader.load(root);
//        , function(loader, node) {
//                debugger;
//            loader.doPreload(node);
//        });

        Phlexible.frontendmedia.FolderSelector.superclass.initComponent.call(this);

        // selecting folders is not allowed, so filter them
        //this.tree.getSelectionModel().on('beforeselect', this.beforeSelection, this);

        // if being rendered before the store is loaded, reload when it is loaded
        if (this.listenForLoad) {
            this.store.on('load', function () {
                root.reload();
            }, this, {
                single: true
            });
        }

        this.ytriggerConfig = {
            tag: 'span', cls: 'x-form-twin-triggers', cn: [
                {tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger " + this.trigger1Class},
                {tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger " + this.trigger2Class}
            ]};
    },

    syncValue: function () {
        this.hiddenField.dom.value = this.getValue();
    },

    onRender: function () {
        Phlexible.frontendmedia.FolderSelector.superclass.onRender.apply(this, arguments);

        this.hiddenField = this.wrap.createChild({
            tag: 'input',
            type: 'hidden',
            name: this.hiddenName,
            value: ''
        });
    },

    xbeforeSelection: function (tree, node) {
        if (node && node.attributes.isFolder) {
            node.toggle();
            return false;
        }
    },

    ygetTrigger: function (index) {
        return this.triggers[index];
    },

    yinitTrigger: function () {
        var ts = this.trigger.select('.x-form-trigger', true);
        this.wrap.setStyle('overflow', 'hidden');
        var triggerField = this;
        ts.each(function (t, all, index) {
            t.hide = function () {
                var w = triggerField.wrap.getWidth();
                this.dom.style.display = 'none';
                triggerField.el.setWidth(w - triggerField.trigger.getWidth());
            };
            t.show = function () {
                var w = triggerField.wrap.getWidth();
                this.dom.style.display = '';
                triggerField.el.setWidth(w - triggerField.trigger.getWidth());
            };
            var triggerIndex = 'Trigger' + (index + 1);

            if (this['hide' + triggerIndex]) {
                t.dom.style.display = 'none';
            }
            t.on("click", this['on' + triggerIndex + 'Click'], this, {preventDefault: true});
            t.addClassOnOver('x-form-trigger-over');
            t.addClassOnClick('x-form-trigger-click');
        }, this);
        this.triggers = ts.elements;
    },

    // private
    yonDestroy: function () {
        Ext.destroy.apply(this, this.triggers);
        Ext.form.TwinTriggerField.superclass.onDestroy.call(this);
    },


    yonTrigger1Click: Ext.emptyFn,
    yonTrigger2Click: Ext.emptyFn

});
Ext.reg('folderselector', Phlexible.frontendmedia.FolderSelector);
Ext.reg('folderfield', Phlexible.frontendmedia.FolderSelector);
