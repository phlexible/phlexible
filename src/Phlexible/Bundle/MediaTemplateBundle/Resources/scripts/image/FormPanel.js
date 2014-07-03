Phlexible.mediatemplates.image.FormPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.mediatemplates.Strings.image_template,
    strings: Phlexible.mediatemplates.Strings,
//    labelWidth: 80,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    previewFile: null,
    debugPreview: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'panel',
                layout: 'form',
                title: this.strings.image,
                iconCls: 'p-mediatemplate-type_image-icon',
                bodyStyle: 'padding: 5px',
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'method', 'help'],
                            data: [
                                ['width', this.strings.method_width, this.strings.method_width_help],
                                ['height', this.strings.method_height, this.strings.method_height_help],
                                ['exact', this.strings.method_exact, this.strings.method_exact_help],
                                ['exactFit', this.strings.method_exactFit, this.strings.method_exactFit_help],
                                ['fit', this.strings.method_fit, this.strings.method_fit_help],
                                ['crop', this.strings.method_crop, this.strings.method_crop_help]
                            ]
                        }),
                        tpl: '<tpl for="."><div class="x-combo-list-item"><div>{method}</div><div style="font-size: 11px; color: gray;">{help}</div></div></tpl>',
                        displayField: 'method',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        emptyText: this.strings.select_method,
                        selectOnFocus: true,
                        editable: false,
                        allowBlank: false,
                        fieldLabel: this.strings.method,
                        hiddenName: 'xmethod',
                        width: 280,
                        listWidth: 650,
                        helpText: this.strings.help_method,
                        listeners: {
                            select: this.updateMethod,
                            scope: this
                        }
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'width',
                        fieldLabel: this.strings.width,
                        helpText: this.strings.help_width_image
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'height',
                        fieldLabel: this.strings.height,
                        helpText: this.strings.help_height_image
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'scale'],
                            data: [
                                ['down', this.strings.scale_down],
                                ['up', this.strings.scale_up],
                                ['updown', this.strings.scale_up_and_down]
                            ]
                        }),
                        displayField: 'scale',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        emptyText: this.strings.select_scale,
                        selectOnFocus: true,
                        editable: false,
                        allowBlank: false,
                        fieldLabel: this.strings.scale,
                        hiddenName: 'scale',
                        width: 280,
                        //listWidth: 550,
                        emptyText: this.strings.select_scale,
                        helpText: this.strings.help_scale
                    },
                    {
                        xtype: 'xcheckbox',
                        width: 280,
                        name: 'for_web',
                        hideLabel: true,
                        boxLabel: this.strings.for_web,
                        helpText: this.strings.help_for_web_image,
                        listeners: {
                            check: function (c, checked) {
                                this.updateFormat();
                                this.updateQuality();
                                this.updateColorspace();
                                this.updateColorDepth();
                                this.updateTiffCompression();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'format'],
                            data: [
                                //['', this.strings.keep_format],
                                ['gif', 'GIF'],
                                ['jpg', 'JPG'],
                                ['png', 'PNG'],
                                ['tif', 'TIF'],
                                ['bmp', 'BMP']
                            ]
                        }),
                        displayField: 'format',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        selectOnFocus: true,
                        editable: false,
                        fieldLabel: this.strings.format,
                        hiddenName: 'format',
                        width: 280,
                        listWidth: 280,
                        allowBlank: false,
                        emptyText: this.strings.select_format,
                        helpText: this.strings.help_format,
                        listeners: {
                            select: function (c, r) {
                                this.updateColorspace();
                                this.updateQuality();
                                this.updateColorDepth();
                                this.updateTiffCompression();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'colorspace'],
                            data: [
                                ['', this.strings.keep_colorspace],
                                ['rgb', this.strings.colorspace_rgb],
                                ['cmyk', this.strings.colorspace_cmyk],
                                ['gray', this.strings.colorspace_gray]
                            ]
                        }),
                        displayField: 'colorspace',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        selectOnFocus: true,
                        editable: false,
                        fieldLabel: this.strings.colorspace,
                        hiddenName: 'colorspace',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_colorspace
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'depth'],
                            data: [
                                ['', this.strings.keep_depth],
                                ['8', this.strings.eight_bit_per_channel],
                                ['16', this.strings.sixteen_bit_per_channel]
                            ]
                        }),
                        displayField: 'depth',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        selectOnFocus: true,
                        editable: false,
                        fieldLabel: this.strings.depth,
                        hiddenName: 'depth',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_depth
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'tiffcompression'],
                            data: [
                                ['none', this.strings.tiffcompression_none],
                                ['zip', this.strings.tiffcompression_zip],
                                ['lzw', this.strings.tiffcompression_lzw]
                            ]
                        }),
                        displayField: 'tiffcompression',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        selectOnFocus: true,
                        editable: false,
                        fieldLabel: this.strings.tiffcompression,
                        hiddenName: 'tiffcompression',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.tiffcompression_help
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'quality',
                        fieldLabel: this.strings.quality,
                        helpText: this.strings.help_quality,
                        allowBlank: false,
                        minValue: 1,
                        maxValue: 100
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'compression'],
                            data: [
                                ['0', '0 - ' + this.strings.compression_huffman],
                                ['1', '1 - ' + this.strings.compression_fastest],
                                ['2', '2'],
                                ['3', '3'],
                                ['4', '4'],
                                ['5', '5'],
                                ['6', '6'],
                                ['7', '7'],
                                ['8', '8'],
                                ['9', '9 - ' + this.strings.compression_best]
                            ]
                        }),
                        displayField: 'compression',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        selectOnFocus: true,
                        editable: false,
                        fieldLabel: this.strings.compression,
                        hiddenName: 'compression',
                        width: 280,
                        listWidth: 280,
                        allowBlank: false,
                        emptyText: this.strings.select_compression,
                        helpText: this.strings.help_compression
                    },
                    {
                        xtype: 'combo',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'filtertype'],
                            data: [
                                ['0', 'None'],
                                ['1', 'Sub'],
                                ['2', 'Up'],
                                ['3', 'Average'],
                                ['4', 'Paeth'],
                                ['5', 'Adaptive filtering']
                            ]
                        }),
                        displayField: 'filtertype',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        selectOnFocus: true,
                        editable: false,
                        fieldLabel: this.strings.filtertype,
                        hiddenName: 'filtertype',
                        width: 280,
                        listWidth: 280,
                        allowBlank: false,
                        emptyText: this.strings.select_filtertype,
                        helpText: this.strings.help_filtertype
                    },
                    {
                        xtype: 'colorfield',
                        width: 280,
                        name: 'backgroundcolor',
                        emptyText: this.strings.empty_backgroundcolor,
                        fieldLabel: this.strings.backgroundcolor,
                        helpText: this.strings.help_backgroundcolor
                    }
                ]
            }
        ];

        this.previewSizes = [
            [1000, 600],
            [600, 1000],
            [1000, 1000],
            [500, 350],
            [160, 120],
            [120, 160],
            [120, 120],
            [800, 600, 'with alpha channel']
        ];
        var previewBtns = [];
        Ext.each(this.previewSizes, function (item) {
            previewBtns.push({
                text: item[0] + 'x' + item[1] + (item[2] ? ' ' + item[2] : ''),
                handler: function () {
                    this.doPreviewSize(item[0], item[1]);
                },
                scope: this
            });
        }, this);
        previewBtns.push('-');
        previewBtns.push({
            text: this.strings.debug,
            checked: this.debugPreview,
            checkHandler: function (checkItem, checked) {
                this.debugPreview = checked;
            },
            scope: this
        });

        this.tbar = [
            {
                text: this.strings.save,
                iconCls: 'p-mediatemplate-save-icon',
                handler: this.saveParameters,
                scope: this
            },
            '->',
            {
                xtype: 'tbsplit',
                text: this.strings.preview,
                iconCls: 'p-mediatemplate-preview-icon',
                handler: function () {
                    this.doPreview();
                },
                scope: this,
                menu: previewBtns
            }
        ];

        this.on({
            clientvalidation: function (f, valid) {
                this.getTopToolbar().items.items[0].setDisabled(!valid);
            },
            render: function () {
                this.setPreviewSize(this.previewSizes[0][0], this.previewSizes[0][1]);
            },
            scope: this
        });

        Phlexible.mediatemplates.image.FormPanel.superclass.initComponent.call(this);

    },

    setPreviewSize: function (width, height) {
        this.previewFile = width + '_' + height;
        this.getTopToolbar().items.items[2].setText(this.strings.preview + ' ' + width + 'x' + height);
    },

    doPreviewSize: function (width, height) {
        this.setPreviewSize(width, height);
        this.doPreview();
    },

    getSafeValues: function () {
        var values = this.getForm().getValues();

        if (values.method) {
            values.xmethod = values.method;
            delete values.xmethod;
        }
        if (values.for_web === 'true') {
            values.for_web = 1;
        } else {
            delete values.for_web;
        }
        if (values.format === 'png') {
            values.quality = values.compression * 10 + values.filtertype * 1;
        }
        delete values.compression;
        delete values.filtertype;

        return values;
    },

    doPreview: function () {
        values = this.getSafeValues();

        values.template = this.template_key;
        values.preview_image = this.previewFile;
        values.debug = this.debugPreview;

        this.fireEvent('preview', values, this.debugPreview);
    },

    updateMethod: function () {
        var method = this.getComponent(0).getComponent(0).getValue();

        if (method === 'width') {
            this.getComponent(0).getComponent(1).enable();
            this.getComponent(0).getComponent(2).disable();
        }
        else if (method === 'height') {
            this.getComponent(0).getComponent(1).disable();
            this.getComponent(0).getComponent(2).enable();
        }
        else {
            this.getComponent(0).getComponent(1).enable();
            this.getComponent(0).getComponent(2).enable();
        }
    },

    updateFormat: function () {
        var optimize = this.getComponent(0).getComponent(4);
        var format = this.getComponent(0).getComponent(5);

        if (optimize.getValue()) {
            format.store.loadData([
                ['gif', 'GIF'],
                ['jpg', 'JPG'],
                ['png', 'PNG']
            ]);
        } else {
            format.store.loadData([
                ['', this.strings.keep_format],
                ['gif', 'GIF'],
                ['jpg', 'JPG'],
                ['png', 'PNG'],
                ['tif', 'TIF'],
                ['bmp', 'BMP']
            ]);
        }
    },

    updateColorspace: function () {
        var optimize = this.getComponent(0).getComponent(4);
        var formatValue = this.getComponent(0).getComponent(5).getValue;
        var colorspace = this.getComponent(0).getComponent(6);

        if (optimize.getValue()) {
            if (formatValue == 'jpg' || formatValue == 'tif') {
                colorspace.setValue('rgb');
            }
            colorspace.store.loadData([
                ['rgb', this.strings.colorspace_rgb],
                ['gray', this.strings.colorspace_gray]
            ]);
        } else {
            colorspace.store.loadData([
                ['', this.strings.keep_colorspace],
                ['rgb', this.strings.colorspace_rgb],
                ['cmyk', this.strings.colorspace_cmyk],
                ['gray', this.strings.colorspace_gray]
            ]);
        }
    },

    updateColorDepth: function () {
        var format = this.getComponent(0).getComponent(5);
        var colorDepth = this.getComponent(0).getComponent(7);

        var formatValue = format.getValue();
        if (formatValue != 'tif') {
            colorDepth.setValue('');
            colorDepth.disable();
            return;
        }

        colorDepth.enable();
    },

    updateTiffCompression: function () {
        var format = this.getComponent(0).getComponent(5);
        var tiffcompression = this.getComponent(0).getComponent(8);

        var formatValue = format.getValue();
        var tiffcompressionValue = tiffcompression.getValue();


        if (formatValue != 'tif') {
            tiffcompression.setValue('');
            tiffcompression.disable();
            return;
        }

        if (tiffcompressionValue === '') {
            tiffcompression.setValue('none');
        }
        tiffcompression.enable();
    },

    updateQuality: function () {
        var format = this.getComponent(0).getComponent(5);
        var tiffcompression = this.getComponent(0).getComponent(8);
        var quality = this.getComponent(0).getComponent(9);
        var compression = this.getComponent(0).getComponent(10);
        var filtertype = this.getComponent(0).getComponent(11);

        var formatValue = format.getValue();
        if (formatValue == 'jpg') {
            quality.show();
            quality.enable();
            compression.hide();
            compression.disable();
            filtertype.hide();
            filtertype.disable();
            tiffcompression.hide();
            tiffcompression.disable();
        }
        else if (formatValue == 'tif') {
            quality.hide();
            quality.disable();
            compression.hide();
            compression.disable();
            filtertype.hide();
            filtertype.disable();
            tiffcompression.show();
            tiffcompression.enable();
        }
        else if (formatValue == 'png') {
            quality.hide();
            quality.disable();
            compression.show();
            compression.enable();
            filtertype.show();
            filtertype.enable();
            tiffcompression.hide();
            tiffcompression.disable();
        }
        else {
            quality.hide();
            quality.disable();
            compression.hide();
            compression.disable();
            filtertype.hide();
            filtertype.disable();
            tiffcompression.hide();
            tiffcompression.disable();
        }
    },

    loadParameters: function (template_key) {
        this.disable();
        this.template_key = template_key;

        this.getForm().reset();
        this.getForm().load({
            url: Phlexible.Router.generate('mediatemplates_form_load'),
            params: {
                template_key: template_key
            },
            success: function (form, data) {
                this.enable();

                this.updateMethod();
                this.updateFormat();
                this.updateColorspace();
                this.updateColorDepth();
                this.updateQuality();
                this.updateTiffCompression();

                if (data.result.data.quality) {
                    var quality = parseInt(data.result.data.quality, 10);
                    var compression = Math.floor(quality / 10);
                    var filtertype = quality % 10;

                    this.getComponent(0).getComponent(9).setValue(quality);
                    this.getComponent(0).getComponent(10).setValue(compression);
                    this.getComponent(0).getComponent(11).setValue(filtertype);
                }

                this.fireEvent('paramsload');
            },
            scope: this
        });

    },

    saveParameters: function () {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert('Error', this.strings.required_values);
            return;
        }

        Ext.MessageBox.confirm(this.strings.save, this.strings.save_hint, function (btn) {
            if (btn !== 'yes') {
                return;
            }

            this.getForm().submit({
                url: Phlexible.Router.generate('mediatemplates_form_save'),
                params: {
                    template_key: this.template_key
                },
                success: function (form, action) {
                    var data = Ext.decode(action.response.responseText);
                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.fireEvent('paramssave');
                    }
                    else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    }
});

Ext.reg('mediatemplates-imageformpanel', Phlexible.mediatemplates.image.FormPanel);
