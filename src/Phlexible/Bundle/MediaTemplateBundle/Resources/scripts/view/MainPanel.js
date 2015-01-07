Ext.provide('Phlexible.mediatemplates.MainPanel');

Ext.require('Phlexible.mediatemplates.TemplatesGrid');
Ext.require('Phlexible.mediatemplates.image.MainPanel');
Ext.require('Phlexible.mediatemplates.audio.MainPanel');
Ext.require('Phlexible.mediatemplates.video.MainPanel');
Ext.require('Phlexible.mediatemplates.pdf2swf.MainPanel');

Phlexible.mediatemplates.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatemplates.Strings.mediatemplates,
    strings: Phlexible.mediatemplates.Strings,
    iconCls: 'p-mediatemplate-component-icon',
    layout: 'border',
    border: false,

    loadParams: function () {
    },

    initComponent: function () {
        this.templatesGrid = new Phlexible.mediatemplates.TemplatesGrid({
            region: 'west',
            width: '200',
            listeners: {
                templatechange: function (r) {
                    switch (r.get('type')) {
                        case 'image':
                            this.cardPanel.getLayout().setActiveItem(0);
                            this.imagePanel.loadParameters(r.get('key'));
                            break;

                        case 'video':
                            this.cardPanel.getLayout().setActiveItem(1);
                            this.videoFormPanel.loadParameters(r.get('key'));
                            break;

                        case 'audio':
                            this.cardPanel.getLayout().setActiveItem(2);
                            this.audioFormPanel.loadParameters(r.get('key'));
                            break;

                        case 'pdf':
                            this.cardPanel.getLayout().setActiveItem(3);
                            this.pdfFormPanel.loadParameters(r.get('key'));
                            break;

                        default:
                            Ext.MessageBox.alert('Warning', 'Unknown template');
                    }

                },
                create: function (template_id, template_title, template_type) {
                    switch (template_type) {
                        case 'image':
                            this.imagePanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(0);
                            break;

                        case 'video':
                            this.videoFormPanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(1);
                            break;

                        case 'audio':
                            this.audioFormPanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(2);
                            break;

                        case 'pdf':
                            this.pdfFormPanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(3);
                            break;

                        default:
                            Ext.MessageBox.alert('Warning', 'Unknown template');
                    }
                },
                scope: this,
            }
        });

        this.imagePanel = new Phlexible.mediatemplates.image.MainPanel({
            listeners: {
                paramsload: function () {

                },
                paramssave: function () {
                    this.templatesGrid.getStore().reload();
                },
                scope: this
            }
        });

        this.videoFormPanel = new Phlexible.mediatemplates.video.MainPanel({
            listeners: {
                paramsload: function () {

                },
                paramssave: function () {
                    this.templatesGrid.getStore().reload();
                },
                scope: this
            }
        });

        this.audioFormPanel = new Phlexible.mediatemplates.audio.MainPanel({
            listeners: {
                paramsload: function () {

                },
                paramssave: function () {
                    this.templatesGrid.getStore().reload();
                },
                scope: this
            }
        });

        this.pdfFormPanel = new Phlexible.mediatemplates.pdf2swf.MainPanel({
            listeners: {
                paramsload: function () {

                },
                paramssave: function () {
                    this.templatesGrid.getStore().reload();
                },
                scope: this
            }
        });

        this.cardPanel = new Ext.Panel({
            region: 'center',
            layout: 'card',
            activeItem: 0,
            border: false,
            items: [
                this.imagePanel,
                this.videoFormPanel,
                this.audioFormPanel,
                this.pdfFormPanel
            ]
        });

        this.items = [
            this.templatesGrid,
            this.cardPanel
        ];

        Phlexible.mediatemplates.MainPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('mediatemplates-main', Phlexible.mediatemplates.MainPanel);