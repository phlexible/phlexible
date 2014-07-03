Phlexible.frontendmedia.ImageFileField = Ext.extend(Ext.form.ImageFileField, {
    // private
    onRender: function (ct, position) {
        Phlexible.frontendmedia.ImageFileField.superclass.onRender.call(this, ct, position);

        this.templatesButton = this.leftButtons.createChild({
            tag: 'img',
            src: Ext.BLANK_IMAGE_URL,
            width: 16,
            height: 16,
            qtip: 'Media templates'
        });
        this.templatesButton.addClass('p-mediamanager-information-icon');
        this.templatesButton.setVisibilityMode(Ext.Element.DISPLAY);
        if (!this.templates_config || (Ext.isArray(this.templates_config) && !this.templates_config.length)) {
            this.templatesButton.hide(); //addClass('x-item-disabled');
        } else if (!this.file_id) {
            this.templatesButton.addClass('x-item-disabled');
        }
        this.templatesButton.on('click', function () {
            if (!this.templates_config || (Ext.isArray(this.templates_config) && !this.templates_config.length)) return;
            if (!this.file_id) return;
            var w = new Phlexible.frontendmedia.MediaTemplatesWindow({
                eid: this.element.eid,
                version: this.element.version,
                language: this.element.language,
                data_id: this.data_id,
                file_id: this.file_id,
                file_version: this.file_version
            });
            w.show();
        }, this);
        this.on({
            select: function () {
                if (!this.templates_config || (Ext.isArray(this.templates_config) && !this.templates_config.length)) {
                    return;
                }
                this.templatesButton.removeClass('x-item-disabled');
            },
            clear: function () {
                if (!this.templates_config || (Ext.isArray(this.templates_config) && !this.templates_config.length)) {
                    return;
                }
                this.templatesButton.addClass('x-item-disabled');
            },
            scope: this
        });

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
Ext.reg('frontendmedia-field-imagefilefield', Phlexible.frontendmedia.ImageFileField);

Phlexible.fields.Registry.addFactory('image', function (parentConfig, item, valueStructure, pos, element, repeatablePostfix, forceAdd) {
    if (element.master) {
        element.prototypes.addFieldPrototype(item);
    }

    element.prototypes.incCount(item.ds_id);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatablePostfix, forceAdd);

    // TODO: wie?
    item.media = item.media || {};

    Ext.apply(config, {
        xtype: 'frontendmedia-field-imagefilefield',
        data_id: item.data_id,

        file_id: item.media.file_id || false,
        folder_id: item.media.folder_id || false,
        folder_path: item.media.folder_path || false,
        fileTitle: item.media.name,

        templates: {},//item.templates || {},
        templates_config: {},//item.templates_config || {}

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsRepeatable: true
    });

    delete config.width;
    delete config.height;

    return config;
});

Phlexible.fields.FieldTypes.addField('image', {
    titles: {
        de: 'Bild',
        en: 'Image'
    },
    iconCls: 'p-frontendmedia-field_image-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    config: {
        properties: {
        },
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
        },
    }
});
