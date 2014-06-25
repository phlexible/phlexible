Ext.namespace(
	'Phlexible.elementtypes.configuration',
	'Phlexible.elementtypes.menuhandle',
	'Phlexible.fields'
);

Phlexible.elementtypes.TYPE_FULL            = 'full';
Phlexible.elementtypes.TYPE_STRUCTURE       = 'structure';
Phlexible.elementtypes.TYPE_LAYOUTAREA      = 'layout';
Phlexible.elementtypes.TYPE_LAYOUTCONTAINER = 'layoutcontainer';
Phlexible.elementtypes.TYPE_PART            = 'part';
Phlexible.elementtypes.TYPE_REFERENCE       = 'reference';

Phlexible.elementtypes.Format = {
    title: function(title, meta, record){
        return '<img src="' + Phlexible.component('/elementtypes/elementtypes/' + record.get('icon')) + '" width="18" height="18" border="0" alt="' + title + '" /> ' + title;
    },

    status: function(status, meta, record){
        return '';
    }
};

Phlexible.elementtypes.ElementtypeRecord = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'icon', type: 'string'},
    {name: 'title', type: 'string'},
    {name: 'version', type: 'string'},
    {name: 'type', type: 'string'}
]);

Phlexible.elementtypes.FieldMap = {
    field: {
        type: '',
        working_title: '',
        comment: '',
        image: ''
    },
    validation: {},
    configuration: {},
    labels: {
        fieldlabel: {
            de: '',
            en: ''
        },
        boxlabel: {
            de: '',
            en: ''
        },
        prefix: {
            de: '',
            en: ''
        },
        suffix: {
            de: '',
            en: ''
        },
        context_help: {
            de: '',
            en: ''
        }
    },
    options: {
    },
    content_channels: {
        allow_deactivation: false,
        allow_unlink: false,
        list: []
    }
};
