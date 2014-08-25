Phlexible.frontendmedia.DownloadFileField = Ext.extend(Ext.form.DownloadFileField, {
    // private
    onRender: function (ct, position) {
        Phlexible.frontendmedia.DownloadFileField.superclass.onRender.call(this, ct, position);

        Phlexible.frontendmedia.FieldHelper.inlineDiff.call(this);
        Phlexible.frontendmedia.FieldHelper.unlink.call(this);

        this.dropZone = new Ext.dd.DropZone(this.el.dom, {
            ddGroup: 'imageDD',
            /*notifyDrop: function(dd, e, data){
             alert(data);
             return true;
             }*/
            getTargetFromEvent: function (e) {
                return e.getTarget('.x-form-item');
            },
            xonNodeEnter: function (target, dd, e, data) {
                Phlexible.console.log('onNodeEnter');
                //Ext.fly(target).addClass('flower-target-hover');
            },
            xonNodeOut: function (target, dd, e, data) {
                Phlexible.console.log('onNodeOut');
                //Ext.fly(target).removeClass('flower-target-hover');
            },
            onNodeOver: function (target, dd, e, data) {
                return Ext.dd.DropZone.prototype.dropAllowed;
            },
            onNodeDrop: function (target, dd, e, data) {
                this.setFile(data.record.data.id, data.record.data.version, data.record.data.name, data.record.data.folder_id);
                /*  var rowIndex = g.getView().findRowIndex(target);
                 var h = g.getStore().getAt(rowIndex);
                 var targetEl = Ext.get(target);
                 targetEl.update(data.patientData.name + ', ' + targetEl.dom.innerHTML);
                 Ext.Msg.alert('Drop gesture', 'Dropped patient ' + data.patientData.name +
                 ' on hospital ' +
                 h.data.name);*/
                return true;
            }.createDelegate(this)
        });
    }
});
Ext.reg('frontendmedia-field-downloadfilefield', Phlexible.frontendmedia.DownloadFileField);

Phlexible.fields.Registry.addFactory('download', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatableId);

    // TODO: wie?
    item.media = item.media || {};

    Ext.apply(config, {
        xtype: 'frontendmedia-field-downloadfilefield',
        data_id: item.data_id,

        file_id: item.media.file_id || false,
        folder_id: item.media.folder_id || false,
        folder_path: item.media.folder_path || false,
        fileTitle: item.media.name,

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsRepeatable: true
    });

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    delete config.width;
    delete config.height;

    return config;
});

Phlexible.fields.FieldTypes.addField('download', {
    titles: {
        de: 'Download',
        en: 'Download'
    },
    iconCls: 'p-frontendmedia-field_download-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 1,
            suffix: 1,
            help: 1
        },
        configuration: {
            sync: 1,
            width: 0,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        validation: {
            required: 1,
            text: 0,
            numeric: 0,
            content: 0
        }
    }
});
