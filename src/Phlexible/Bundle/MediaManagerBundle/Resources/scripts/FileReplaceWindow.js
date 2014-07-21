Phlexible.mediamanager.FileReplaceWindowTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="p-filereplace-wrap">',
        '<div style="float: left;">',
        Phlexible.inlineIcon('p-mediamanager-arrow_right-icon'),
        '</div>',
        '<div style="padding-left: 20px;">',
            '<div class="p-filereplace-header">',
                '{header}',
            '</div>',
                '<div class="p-filereplace-text">',
            '{text}',
            '</div>',
            '<tpl if="src">',
            '<div>',
                '<div class="p-filereplace-img">',
                    '<img src="{src}" width="48" height="48">',
                '</div>',
                '<div class="p-filereplace-desc">',
                    '<div class="p-filereplace-name" style="font-weight: bold">{[values.name.shorten(50)]}</div>',
                    '<div class="p-filereplace-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.type)]}</div>',
                    '<div class="p-filereplace-size">' + Phlexible.mediamanager.Strings.size + ': {[Phlexible.Format.size(values.size)]}</div>',
                '</div>',
            '</div>',
            '</tpl>',
        '</div>',
    '</div>',
    '</tpl>'
);

Phlexible.mediamanager.FileReplaceWindow = Ext.extend(Ext.Window, {
    title: Phlexible.mediamanager.Strings.uploaded_file_conflict,
    strings: Phlexible.mediamanager.Strings,
    width: 500,
    minWidth: 500,
    height: 400,
    minHeight: 400,
    bodyStyle: 'padding: 10px;',
    cls: 'p-filereplace',
    modal: true,
    closable: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'panel',
                border: false,
                plain: true,
                bodyStyle: 'font-size: 13px; font-weight: bold;',
                html: this.strings.uploaded_file_conflict_text
            },
            {
                xtype: 'panel',
                border: false,
                plain: true,
                bodyStyle: 'padding-bottom: 10px;',
                html: this.strings.uploaded_file_conflict_desc
            },
            {
                xtype: 'dataview',
                itemSelector: 'div.p-filereplace-wrap',
                overClass: 'p-filereplace-wrap-over',
                style: 'overflow:auto',
                singleSelect: true,
                store: new Ext.data.JsonStore({
                    fields: ['action', 'header', 'text', 'id', 'name', 'type', 'size', 'src']
                }),
                tpl: Phlexible.mediamanager.FileReplaceWindowTemplate,
                listeners: {
                    click: this.saveFile,
                    scope: this
                }
            },
            {
                xtype: 'checkbox',
                boxLabel: this.strings.apply_to_remaining_files
            }
        ];

        Phlexible.mediamanager.FileReplaceWindow.superclass.initComponent.call(this);
    },

    getDataView: function() {
        return this.getComponent(2);
    },

    loadFile: function () {
        var file = this.uploadChecker.getCurrent(),
            data = [];

        data.push({
            action: 'discard',
            header: this.strings.delete_uploaded_file,
            text: this.strings.delete_uploaded_file_desc,
            id: file.old_id,
            name: file.old_name,
            type: file.old_type,
            size: file.old_size,
            src: Phlexible.Router.generate('mediamanager_media', {file_id: file.old_id, template_key: '_mm_medium'})
        });

        if (!file.versions) {
            data.push({
                action: 'replace',
                header: this.strings.replace_existing_file,
                text: this.strings.replace_existing_file_desc,
                id: file.new_id,
                name: file.old_name,
                type: file.new_type,
                size: file.new_size,
                src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.temp_key, id: file.temp_id, template: '_mm_medium'})
            });
        } else {
            data.push({
                action: 'add_version',
                header: 'Als neue Version der bestehenden Datei speichern.',
                text: 'Es werden keine Daten verändert. Die vorhandene Datei wird um diese Datei ergänzt:',
                id: file.new_id,
                name: file.new_name,
                type: file.new_type,
                size: file.new_size,
                src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.temp_key, id: file.temp_id, template: '_mm_medium'})
            });
        }

        data.push({
            action: 'keep',
            header: this.strings.keep_both_files,
            text: String.format(this.strings.keep_both_files_desc, file.alternative_name.shorten(60)),
            id: '',
            name: '',
            type: '',
            size: '',
            src: ''
        });

        this.getDataView().getStore().loadData(data);
    },

    saveFile: function (view, index) {
        var all = this.getComponent(3).getValue() ? true : false,
            r = this.getDataView().getStore().getAt(index),
            action = r.get('action');

        this.fireEvent('save', action, all);
    }
});
