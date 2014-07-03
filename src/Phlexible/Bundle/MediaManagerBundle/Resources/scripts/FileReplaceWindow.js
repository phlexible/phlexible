Phlexible.mediamanager.FileReplaceWindowTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="m-filereplace-wrap">',
    '<div style="float: left;">',
    Phlexible.inlineIcon('p-mediamanager-arrow_right-icon'),
    '</div>',
    '<div style="padding-left: 20px;">',
    '<div class="m-filereplace-header">',
    '{header}',
    '</div>',
    '<div class="m-filereplace-text">',
    '{text}',
    '</div>',
    '<tpl if="src">',
    '<div>',
    '<div class="m-filereplace-img">',
    '<img src="{src}" width="48" height="48">',
    '</div>',
    '<div class="m-filereplace-desc">',
    '<div class="m-filereplace-name" style="font-weight: bold">{[values.name.shorten(50)]}</div>',
    '<div class="m-filereplace-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.type)]}</div>',
    '<div class="m-filereplace-size">' + Phlexible.mediamanager.Strings.size + ': {[Phlexible.Format.size(values.size)]}</div>',
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
    height: 400,
    bodyStyle: 'padding: 10px;',
    cls: 'p-filereplace',

    files: [],
    pointer: 0,

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: ['action', 'header', 'text', 'id', 'name', 'type', 'size', 'src']
        });

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
                itemSelector: 'div.m-filereplace-wrap',
                overClass: 'p-filereplace-wrap-over',
                style: 'overflow:auto',
                singleSelect: true,
                store: this.store,
                tpl: Phlexible.mediamanager.FileReplaceWindowTemplate,
                listeners: {
                    click: {
                        fn: this.saveFile,
                        scope: this
                    },
                    render: {
                        fn: this.showFile,
                        scope: this
                    }
                }
            },
            {
                xtype: 'checkbox',
                boxLabel: this.strings.apply_to_remaining_files
            }
        ];

        Phlexible.mediamanager.FileReplaceWindow.superclass.initComponent.call(this);
    },

    saveFile: function (view, index) {
        var all = 0;
        if (this.getComponent(3).getValue()) {
            all = 1;
        }

        var r = this.store.getAt(index);

        switch (r.data.action) {
            case 'replace':
                var params = {
                    'do': 'replace',
                    all: all,
                    temp_key: this.files[this.pointer].temp_key,
                    temp_id: this.files[this.pointer].temp_id
                };
                break;

            case 'keep':
                var params = {
                    'do': 'keep',
                    all: all,
                    temp_key: this.files[this.pointer].temp_key,
                    temp_id: this.files[this.pointer].temp_id
                };
                break;

            case 'add_version':
                var params = {
                    'do': 'version',
                    all: all,
                    temp_key: this.files[this.pointer].temp_key,
                    temp_id: this.files[this.pointer].temp_id
                };
                break;

            case 'discard':
            default:
                var params = {
                    'do': 'discard',
                    all: all,
                    temp_key: this.files[this.pointer].temp_key,
                    temp_id: this.files[this.pointer].temp_id
                };
                break;

        }

        var request = {
            url: Phlexible.Router.generate('mediamanager_upload_save'),
            params: params,
            failure: function (response) {
                var result = Ext.decode(response.responseText);

                Ext.MessageBox.alert('Failure', result.msg);
                this.nextFile();
            },
            scope: this
        };

        if (all) {
            request.success = function (response) {
                this.fireEvent('update');
                this.close();
            };
        } else {
            request.success = function (response) {
                this.nextFile();
            };
        }

        Ext.Ajax.request(request);
    },

    nextFile: function () {
        this.fireEvent('update');

        if (this.pointer >= (this.files.length - 1)) {
            this.close();
            return;
        }

        this.pointer++;

        this.showFile();
    },

    showFile: function () {
        var file = this.files[this.pointer];

        var data = [];

        data.push([
            'discard',
            this.strings.delete_uploaded_file,
            this.strings.delete_uploaded_file_desc,
            file.old_id,
            file.old_name,
            file.old_type,
            file.old_size,
            Phlexible.Router.generate('mediamanager_media', {file_id: file.old_id, template_key: '_mm_medium'})
        ]);

        if (!file.versions) {
            data.push([
                'replace',
                this.strings.replace_existing_file,
                this.strings.replace_existing_file_desc,
                file.new_id,
                file.old_name,
                file.new_type,
                file.new_size,
                Phlexible.Router.generate('mediamanager_upload_preview', {key: file.temp_key, id: file.temp_id, template: '_mm_medium'})
            ]);
        } else {
            data.push([
                'add_version',
                'Als neue Version der bestehenden Datei speichern.',
                'Es werden keine Daten verändert. Die vorhandene Datei wird um diese Datei ergänzt:',
                file.new_id,
                file.new_name,
                file.new_type,
                file.new_size,
                Phlexible.Router.generate('mediamanager_upload_preview', {key: file.temp_key, id: file.temp_id, template: '_mm_medium'})
            ]);
        }

        data.push([
            'keep',
            this.strings.keep_both_files,
            String.format(this.strings.keep_both_files_desc, file.alternative_name.shorten(60)),
            '',
            '',
            '',
            '',
            ''
        ]);

        this.store.loadData(data);
    }
});
