Phlexible.mediamanager.UploadStatusBar = Ext.extend(Ext.Toolbar, {
    height: 26,

    queued: 0,
    current: 0,
    files: null,
    startFn: Ext.emptyFn,
    stopFn: Ext.emptyFn,

    initComponent: function() {
        this.files = new Ext.util.MixedCollection();

        this.items = [{
            iconCls: 'm-mediamanager-upload_clear-icon',
            handler: this.clear,
            scope: this,
            hidden: true
        },' ',{
            xtype: 'tbtext',
            text: ' ',
            hidden: true
        },' ',{
            xtype: 'progress',
            width: 100,
            value: 0,
            text: '',
            hidden: true
        }];

        this.on({
            render: function(c) {
                c.listLayer = new Ext.Layer({
                    shadow: false,
                    cls: 't-mediamanager-upload-files-layer',
                    constrain:false
                });
                c.listLayer.setWidth(300);
                c.listLayer.setHeight(100);
                c.listLayerInner = c.listLayer.createChild({

                });
                c.listPanel = new Ext.Panel({
                    //title: 'test',
                    applyTo: c.listLayerInner,
                    frame: true
                });
                c.listPanel.setWidth(300);
                c.listPanel.setHeight(100);

                c.listTpl = new Ext.XTemplate('<tpl for="."><div>{file_name}</div></tpl>');
            },
            scope: this
        });

        Phlexible.mediamanager.UploadStatusBar.superclass.initComponent.call(this);
    },

    bind: function(startFn, stopFn) {
        this.startFn = startFn;
        this.stopFn = stopFn;
    },

    addFile: function(id, name, size, removeFn) {
        Phlexible.console.log('UploadStatusBar::addFile(' + id + ')');
        var iconCls = 'm-mediamanager-file-icon';
        var ext = name.split('.').pop();
        if (Phlexible.documenttypes.DocumentTypes.classMap[ext]) {
            iconCls = Phlexible.documenttypes.DocumentTypes.classMap[ext].cls + '-small';
        }
        var text = name;
        if (size) {
            text += ' (' + Phlexible.Format.size(size) + ')';
        }
        var btn = this.insertButton(5 + this.files.getCount(), {
            xtype: 'tbtext',
            iconCls: iconCls,
            text: text,
            handler: function() {
                this.removeFile(id);
            },
            scope: this,
            fileId: id,
            removeFn: removeFn
        });
        this.files.add(id, btn);
        this.getComponent(0).show();
    },

    clear: function() {
        Phlexible.console.log('UploadStatusBar::clear()');
        this.files.each(function(btn) {
            btn.removeFn(btn.id);
            this.removeButton(btn);
        }, this);
    },

    removeButton: function(btn) {
        btn.removeFn();
        btn.destroy();
        this.files.remove(btn);

        if (!this.files.getCount()) {
            this.getComponent(0).hide();
        }
    },

    removeFile: function(id) {
        Phlexible.console.log('UploadStatusBar::removeFile(' + id + ')');
        var btn = this.files.get(id);
        this.removeButton(btn);
    },

    start: function() {
        Phlexible.console.log('UploadStatusBar::start()');
        this.getComponent(0).setIconClass('m-mediamanager-upload_cancel-icon');
        this.getComponent(0).handler = this.stopFn;
        this.getComponent(4).show();
    },

    setActive: function(id) {
        Phlexible.console.log('UploadStatusBar::setActive(' + id + ')');
        this.getComponent(4).updateProgress(0, '');
        this.files.get(id).getEl().child('button').setStyle('font-weight', 'bold');
    },

    setProgress: function(id, percent) {
        Phlexible.console.log('UploadStatusBar::setProgress(' + id + ', ' + percent + ')');
        this.getComponent(4).updateProgress(parseInt(percent / 100, 10), percent + '%');
    },

    setFinished: function(id) {
        Phlexible.console.log('UploadStatusBar::setFinished(' + id + ')');
        this.getComponent(4).updateProgress(1, '');
        this.removeFile(id);
    },

    setError: function(code, msg, id, name) {
        Phlexible.console.log('UploadStatusBar::setError(' + id + ', ' + code + ', ' + msg + ', ' + name + ')');
        this.getComponent(4).updateProgress(1, '');
        this.removeFile(id);
    },

    stop: function() {
        Phlexible.console.log('UploadStatusBar::stop()');
        this.getComponent(4).hide();
        this.getComponent(0).setIconClass('m-mediamanager-upload-icon');
        this.getComponent(0).handler = this.startFn;
    },

    getComponent: function(index) {
        return this.items.items[index];
    }
});

Ext.reg('mediamanager-uploadstatusbar', Phlexible.mediamanager.UploadStatusBar);