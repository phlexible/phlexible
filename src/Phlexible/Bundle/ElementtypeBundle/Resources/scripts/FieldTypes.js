Phlexible.fields.FieldTypes = {
    addField: function (field, fieldConfig) {
        Phlexible.fields.FieldTypes[field] = fieldConfig;
    },
    getField: function (field) {
        return Phlexible.fields.FieldTypes[field];
    },
    hasField: function (field) {
        return !!Phlexible.fields.FieldTypes[field];
    }
};

/*
 Phlexible.elementtypes.FieldTypes = {
 root: {
 titles: {
 de: 'Root',
 en: 'Root'
 },
 allowedIn: [],
 iconCls: 'p-fieldtypes-root-icon',
 accordions: ['rootproperties','rootviability']
 },
 referenceroot: {
 titles: {
 de: 'Referenz',
 en: 'Reference'
 },
 allowedIn: [],
 iconCls: 'p-fieldtypes-root-icon',
 accordions: ['rootproperties','rootviability']
 },
 tab: {
 titles: {
 de: 'Reiter',
 en: 'Tab'
 },
 allowedIn: ['root','referenceroot'],
 iconCls: 'p-fieldtypes-tab-icon',
 accordions: ['fieldproperties','fieldlabels']
 },
 accordion: {
 titles: {
 de: 'Akkordion',
 en: 'Accordeon'
 },
 allowedIn: ['tab','referenceroot'],
 iconCls: 'p-fieldtypes-accordion-icon',
 accordions: ['fieldproperties','fieldlabels']
 },
 group: {
 titles: {
 de: 'Gruppe',
 en: 'Group'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-group-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration']
 },
 checkbox: {
 titles: {
 de: 'Checkbox',
 en: 'Checkbox'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-checkbox-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 checkgroup: {
 titles: {
 de: 'Checkgroup',
 en: 'Checkgroup'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-checkbox-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 date: {
 titles: {
 de: 'Datum',
 en: 'Date'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-date-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 download: {
 titles: {
 de: 'Download',
 en: 'Download'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-download-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalidation']
 },
 editor: {
 titles: {
 de: 'Editor',
 en: 'Editor'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-editor-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 flash: {
 titles: {
 de: 'Flash',
 en: 'Flash'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-flash-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalidation']
 },
 image: {
 titles: {
 de: 'Bild',
 en: 'Image'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-image-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalidation']
 },
 label: {
 titles: {
 de: 'Label',
 en: 'Label'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-label-icon',
 accordions: ['fieldproperties','fieldlabels']
 },
 link: {
 titles: {
 de: 'Link',
 en: 'Link'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-link-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 numberfield: {
 titles: {
 de: 'Zahlenfeld',
 en: 'Numberfield'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-numberfield-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 radio: {
 titles: {
 de: 'Radio',
 en: 'Radio'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-radio-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 select: {
 titles: {
 de: 'Select',
 en: 'Select'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-select-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 table: {
 titles: {
 de: 'Tabelle',
 en: 'Table'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-table-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalidation']
 },
 textarea: {
 titles: {
 de: 'Textarea',
 en: 'Textarea'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-textarea-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 textfield: {
 titles: {
 de: 'Textfeld',
 en: 'Textfield'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-textfield-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalues','fieldvalidation']
 },
 video: {
 titles: {
 de: 'Video',
 en: 'Video'
 },
 allowedIn: ['tab','accordion','group','referenceroot'],
 iconCls: 'p-fieldtypes-video-icon',
 accordions: ['fieldproperties','fieldlabels','fieldconfiguration','fieldvalidation']
 }
 };
 */